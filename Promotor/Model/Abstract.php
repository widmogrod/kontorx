<?php
/**
 * @author gabriel
 *
 */
class Promotor_Model_Abstract {

	/* Status */
	const SUCCESS = 'SUCCESS';
	const FAILURE = 'FAILURE';

	/**
	 * @var string
	 */
	protected $_dbTableClass = null;
	
	/**
	 * @var Zend_Db_Table_Abstract
	 */
	protected $_dbTable = null;
	
	/**
	 * @return Zend_Db_Table_Abstract
	 */
	public function getDbTable() {
		if (null === $this->_dbTable) {
			$this->_dbTable = new $this->_dbTableClass();
			if (!$this->_dbTable instanceof Zend_Db_Table_Abstract) {
				throw new Promotor_Model_Exception(sprintf('table class "%s" is not istantce of Zend_Db_Table_Abstract', $this->_dbTableClass));
			}
		}
		return $this->_dbTable;
	}

	/**
	 * @var string
	 */
	private $_status = null;
	
	/**
	 * @return string
	 */
	public function getStatus() {
		$status = $this->_status;
		$this->_status = null;
		return $status;
	}

	/**
	 * @param string $status
	 * @return Promotor_Model_Abstract
	 */
	protected function _setStatus($status) {
		$this->_status = $status;
	}
	
	/**
	 * @var array
	 */
	private $_messages = array();
	
	/**
	 * @param bool $withExceptions
	 * @return array
	 */
	public function getMessages($withExceptions = true) {
		$messages = $this->_messages;
		if ($withExceptions) {
			foreach ($this->_exception as $exception) {
				$message = $exception->getMessage() . "\n" . $exception->getTraceAsString();
				$messages[] = $message;
			}
		}
		return $messages;
	}

	/**
	 * @param array $messages
	 * @return Promotor_Model_Abstract
	 */
	protected function _setMessages(array $messages) {
		$this->_messages = $messages;
		return $this;
	}
	
	/**
	 * @param string $message
	 * @return Promotor_Model_Abstract
	 */
	protected function _addMessage($message) {
		$this->_messages[] = $message;
		return $this;
	}
	
	/**
	 * @param array $messages
	 * @return Promotor_Model_Abstract
	 */
	protected function _addMessages(array $messages) {
		$this->_messages = array_merge($this->_messages, $messages);
		return $this;
	}
	
	/**
	 * @var array
	 */
	protected $_exception = array();

	/**
	 * @return array
	 */
	public function getExceptions() {
		return $this->_exception;
	}
	
	/**
	 * @param Exception $exception
	 * @return Promotor_Model_Abstract
	 */
	protected function _addException(Exception $exception) {
		$this->_exception[] = $exception;
		return $this;
	}
	
	/**
     * @var Zend_Cache_Core
     */
    private static $_defaultResultCache = null;

    /**
     * Ustawienie @see Zend_Cache_Core keszujacego wynik zapytania
     * @return void
     */
    public static function setDefaultResultCache($resultCache) {
        self::$_defaultResultCache = self::_setupMetadataCache($resultCache);
    }

	/**
     * @param mixed $metadataCache Either a Cache object, or a string naming a Registry key
     * @return Zend_Cache_Core
     * @throws Promotor_Model_Exception
     */
    protected static function _setupMetadataCache($metadataCache) {
        if ($metadataCache === null) {
            return null;
        }
        if (is_string($metadataCache)) {
            require_once 'Zend/Registry.php';
            $metadataCache = Zend_Registry::get($metadataCache);
        }
        if (!$metadataCache instanceof Zend_Cache_Core) {
            require_once 'Promotor/Model/Exceptionn.php';
            throw new Promotor_Model_Exception('Argument must be of type Zend_Cache_Core, or a Registry key where a Zend_Cache_Core object is stored');
        }
        return $metadataCache;
    }
    
    /**
     * Zwraca objekt @see Zend_Cache_Core lub null
     * @return Zend_Cache_Core|null
     */
    public static function getDefaultResultCache() {
        return self::$_defaultResultCache;
    }

    /**
     * @var Zend_Cache_Core|null
     */
    private $_resultCache = null;

    /**
     * Zwraca objekt @see Zend_Cache_Core lub null
     * @return Zend_Cache_Core|null
     */
    public function getResultCache() {
        if (null === $this->_resultCache) {
            $this->_resultCache = self::$_defaultResultCache;
        }
        return $this->_resultCache;
    }

    /**
     * @param string|Zend_Cache_Core $cache
     * @return Promotor_Model_Abstract
     */
    public function setResultCache($cache) {
    	if (is_string($cache)) {
    		/* @var $registry Zend_Registry */
    		$registry = Zend_Registry::getInstance();
    		if (isset($registry[$cache])) {
    			$cache = $registry[$cache];
    		}
    	}

    	if (!$cache instanceof Zend_Cache_Core) {
    		throw new Promotor_Model_Exception('Zend_Cache_Core is not set');
    	}

    	$this->_resultCache = $cache;
    	return $this;
    }

    /**
     * @return Promotor_Model_Abstract
     */
    public function clearResultCache() {
    	$this->_resultCache = null;
    	return $this;
    }
    
    /**
     * Tablica zdefiniowanych przez użytkownika method, które będą keszowane
     * @var array
     */
    protected $_cachedMethods = array();

    /**
     * Wywoluje metode keszujac rezultat jej wyniku
     * @return mixed
     * @throws Zend_Db_Table_Exception
     */
    public function cache() {
        // pobieranie parametrow
        $params = func_get_args();
        $method = array_shift($params);

		// metoda nie istnieje w tablicy zdefiniowanej przez użytkownika
		if (!in_array($method, $this->_cachedMethods)) {
			$message = "Method '$method' is not enabled as cached method";
			require_once 'Zend/Db/Table/Exception.php';
			throw new Zend_Db_Table_Exception($message);
		}

        $resultCache = $this->getResultCache();

        if (!$resultCache instanceof Zend_Cache_Core) {
            $message = "Cache object is not instanceof Zend_Cache_Core or is not set";
            require_once 'Zend/Db/Table/Exception.php';
            throw new Zend_Db_Table_Exception($message);
        }

        // identyfikator cache
        $cacheId = $this->_getResultCacheId($method, $params);

        $tags = array(get_class($this), $method);

        // keszowanie
        if (false === ($result = $resultCache->load($cacheId))) {
        	
        	try {
        		// łapę błędy - jeżeli występują żuć wyjątek!
        		set_error_handler(array($this, '_cacheErorHandler'));
        		$result = call_user_func_array(array($this, $method), $params);
        		restore_error_handler();

        		$resultCache->save($result, $cacheId, $tags);
        	} catch (Exception $e) {
        		$this->_addException($e);
        	}
        }

        return $result;
    }

    /**
     * @param $errno
     * @param $errstr
     * @param $errfile
     * @param $errline
     * @return void
     * @throws Exception
     */
    private function _cacheErorHandler($errno, $errstr, $errfile, $errline) {
    	$error = sprintf('ERROR %d :: %s (%s [%d])', $errno, $errstr, basename($errfile), $errline);
    	throw new Exception($error);
    }
    
    /**
     * Zwraca cache id.
     * @return string
     */
    private function _getResultCacheId($method, $params = null) {
        // baza id
        $result = array(get_class($this), $method);

        // budowanie id ze wzgledu na parametry
        if (is_array($params)) {
            foreach ($params as $key => $value) {
                if (is_object($value)) {
                    $class = get_class($value);
                    $result[] = $key . $class . serialize($value);
                } else
                if (is_array($value)) {
                    $result[] = $key . serialize($value);
                } else {
                    $result[] = $key . $value;
                }
            }
        } else {
           $result[] = $params;
        }

        return sha1(implode($result));
    }

    /**
     * Magick method
     * 
     * @return mixed
     * @throws Zend_Db_Table_Exception
     */
    public function __call($name, array $params = array()) {
        // tablica przechowuje dopasowania wyrazenia reguralnego
        $matches = array();

        // sprawdzenie czy wywolana metoda jest zakońconych "Cache"
        if (preg_match('/^(?P<method>\w+)Cache$/i', $name, $matches)) {
            if (!isset($matches['method'])) {
                $message = "Method '$name' do not exsists";
                require_once 'Zend/Db/Table/Exception.php';
                throw new Zend_Db_Table_Exception($message);
            }

            $method = $matches['method'];

            // dodanie metody jako pierwszego atrybutu
            array_unshift($params, $method);
            // wywolanie metody "cache"
            return call_user_func_array(array($this, 'cache'), $params);
        }
    }
}