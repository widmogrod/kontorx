<?php
require_once 'Zend/Db/Table/Abstract.php';
abstract class KontorX_Db_Table_Abstract extends Zend_Db_Table_Abstract {

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
     * Tablica zdefiniowanych przez użytkownika method, które będą keszowane
     * @var array
     */
    protected $_cachedMethods = array();

    /**
     * Tablica zdefiniowanych method, które będą keszowane
     * @var array
     */
    private $_defaultCachedMethods = array('fetchAll','fetchRow');

    /**
     * Wywoluje metode keszujac rezultat jej wyniku
     * @return mixed
     */
    public function cache() {
        // pobieranie parametrow
        $params = func_get_args();
        $method = array_shift($params);

        // metoda nie istnieje w zdefiniowanej podstawowej tablicy
        if (!in_array($method, $this->_defaultCachedMethods)) {
            // metoda nie istnieje w tablicy zdefiniowanej przez użytkownika
            if (!in_array($method, $this->_cachedMethods)) {
                $message = "Method '$method' is not enabled as cached method";
                require_once 'Zend/Db/Table/Exception.php';
                throw new Zend_Db_Table_Exception($message);
            }
        }

        $resultCache = $this->getResultCache();

        if (!$resultCache instanceof Zend_Cache_Core) {
            $message = "Cache object is not instanceof Zend_Cache_Core or is not set";
            require_once 'Zend/Db/Table/Exception.php';
            throw new Zend_Db_Table_Exception($message);
        }

        // identyfikator cache
        $cacheId = $this->_getResultCacheId($method, $params);

        // keszowanie
        if (false === ($result = $resultCache->load($cacheId))) {
            $result = call_user_func_array(array($this, $method), $params);
            $resultCache->save($result, $cacheId);
        }

        return $result;
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
                    $result[] = $key . $class . serialize(get_class_vars($class));
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

    /**
     * Nazwa kolumny przechowującej rodzaj widoczności rekordu
     *
     * @var string
     */
    protected $_columnForSpecialCredentials = 'visible';

        /**
     * Przygotowuje zapytanie @see Zend_Db_Select
     *
     * Przygotowuje zapytanie określające, które rekordy
     * mogą zostać wyłowione z BD dla użytkownika.
     * Czy ma uprawnienia do danych rekordów czy nie ..
     *
     * @param Zend_Controller_Request_Abstract $request
     * @param Zend_Db_Select $select
     * @return Zend_Db_Select
     */
    public function selectForRowOwner(Zend_Controller_Request_Abstract $request, Zend_Db_Select $select = null) {
        $select = (null === $select)
        ? $this->select()
        : $select;

        $controller = $request->getControllerName();
        $module	 	= $request->getModuleName();

        /**
         * Jeżlie jest brak uprawnień do moderowania, pokaż pod względem ID
         */
        require_once 'user/models/User.php';
        if (!User::hasCredential(User::PRIVILAGE_MODERATE, $controller, $module)) {
            $userId = User::getAuth(User::AUTH_USERNAME_ID);
            $select->where('user_id = ?', $userId);
        }
        return $select;
    }

    /**
     * Nazwa kolumny przechowującej timestamp
     *
     * @var string
     */
    protected $_columnForTimeRange = 't_create';

    /**
     * Ustawia dla @see Zend_Db_Select przedzial czasowy
     *
     * @param Zend_Db_Select $select
     * @param integer $year
     * @param integer $month
     * @param integer $day
     */
    public function selectSetupForTimeRange(Zend_Db_Select $select, $year = null, $month = null, $day = null) {
        // year
        if (is_numeric($year)) {
            $select->where("YEAR($this->_columnForTimeRange) = ?", abs($year));
        }
        // month
        if (is_numeric($month) && $month >= 1 && $month <= 12) {
            $select->where("MONTH($this->_columnForTimeRange) = ?", abs($month));
        }
        // day
        if (is_numeric($day) && $day >= 1 && $day <= 31) {
            $select->where("MONTH($this->_columnForTimeRange) = ?", abs($day));
        }
    }

    /**
     * Przygotowuje zapytanie @see Zend_Db_Select
     *
     * Przygotowuje zapytanie określające, które rekordy
     * mogą zostać wyłowione z BD dla użytkownika
     *
     * @param Zend_Controller_Request_Abstract $request
     * @param Zend_Db_Select $select
     * @return Zend_Db_Select
     */
    public function selectForSpecialCredentials(Zend_Controller_Request_Abstract $request, Zend_Db_Select $select = null) {
        $select = (null === $select)
        ? $this->select()
        : $select;

        $controller = $request->getControllerName();
        $module	 	= $request->getModuleName();

        require_once 'user/models/User.php';
        User::selectForSpecialCredentials($select, $this->_columnForSpecialCredentials, $controller, $module);
        return $select;
    }

    //	/**
    //	 * Enter description here...
    //	 *
    //	 * @var Zend_Cache_Core
    //	 */
    //	protected static $_defaultRowsetCache = null;
    //
    //	protected $_caching = true;
    //
    //	public function setCaching($flag = true) {
    //		$this->_caching = (bool) $flag;
    //	}
    //
    //	public function isCaching() {
    //		return (true === $this->_caching &&
    //			null !== self::$_defaultRowsetCache);
    //	}
    //
    //	public static function setDefaultRowsetCache($rowsetCache) {
    //		self::$_defaultRowsetCache = self::_setupRowsetCache($rowsetCache);
    //	}
    //
    //	/**
    //	 * Enter description here...
    //	 *
    //	 * @return Zend_Cache_Core
    //	 */
    //	public static function getDefaultRowsetCache() {
    //		return self::$_defaultRowsetCache;
    //	}
    //
    //	protected static final function _setupRowsetCache($rowsetCache) {
    //		// wykonuje to co powinno zatem delegujemy komunikat dalej ;]
    //		return self::_setupMetadataCache($rowsetCache);
    //	}
    //
    //	protected function _getCacheTags(array $aditionalTags = array()) {
    //		$tags = array($this->_schema.$this->_name);
    //		$tags += $aditionalTags;
    //		// TODO Dodać jeszcze dodawanie tagow z konfiguracji ..
    //		return $tags;
    //	}
    //
    //	/**
    //	 * Wyszukuje w zapytaniu SQL WHERE primaryKey
    //	 *
    //	 * Na podstawie primaryKey bedzie generowany klucz dla cache
    //	 *
    //	 * @param string $sql
    //	 * @param string $name
    //	 * @return string
    //	 */
    //	protected function _getCacheIdFromSql($sql, $name = null) {
    //		$matched = array();
    //		$parentName = null === $name ? '[\w\.]+|[\w]+' : (string) $name;
    //		$pattern = '/[`"\']{0,1}(?P<primaryKey>'.$parentName.')[`"\']{0,1}[\s]*=[\s]*["\']{0,1}(?P<primaryValue>[\wd\-\_]+)["\']{0,1}/i';
    //		if(!preg_match($pattern, $sql, $matched)) {
    //			// TODO Czy null czy sql ??
    //			return null;
    //		}
    //		return $matched['primaryValue'];
    //	}
    //
    //	/**
    //	 * Overwrite
    //	 * @return Zend_Db_Table_Rowset_Abstract
    //	 */
    //	public function fetchAll($where = null, $order = null, $count = null, $offset = null) {
    //		// sprawdza czy cachowac
    //		if (!$this->isCaching()) {
    //			return parent::fetchAll($where, $order, $count, $offset);
    //		}
    //
    //		// tworzenie identyfikatora cache
    //		$args = func_get_args();
    //		$id = md5($this->_schema . $this->_name. implode(':', $args));
    //
    //		$cache = self::$_defaultRowsetCache;
    //
    //		// wczytujemy
    //		$data = $cache->load($id);
    //		if (false === $data) {
    //			// nie ma w cache - to dodajemy
    //			$data = parent::fetchAll($where, $order, $count, $offset);
    //			// dodaje tag `rowset`, w momecie czyszczenia keszu idzie
    //			// czyszczenie tylko `rowset`
    //			$tags = $this->_getCacheTags(array(('rowset' . $this->_schema . $this->_name)));
    //			$cache->save($data, $id, $tags);
    //		}
    //
    //		return $data;
    //	}
    //
    //	/**
    //	 * Overwrite
    //	 * @return Zend_Db_Table_Row_Abstract
    //	 */
    //	public function fetchRow($where = null, $order = null) {
    //		// sprawdza czy cachowac
    //		if (!$this->isCaching()) {
    //			return parent::fetchRow($where, $order);
    //		}
    //
    //		// tworzenie identyfikatora cache
    //		// TODO udoskonalić tworzenie id .. parsowanie where
    //		// pod warunkiem wyszukania parentKey ..
    ////		$sqlId = ($where instanceof Zend_Db_Table_Select)
    ////				? $this->_getCacheIdFromSql($where->__toString(), current($this->_primary))
    ////				: $this->_getCacheIdFromSql($where);
    //
    ////		$args = func_get_args();
    ////		$primary = null === $sqlId
    ////			? implode(':', $args)
    ////			: $sqlId;
    //
    //		$args = func_get_args();
    //		$id = md5($this->_schema . $this->_name . implode(':', $args));
    //
    //		$cache = self::$_defaultRowsetCache;
    //
    //		// wczytujemy
    //		$data = $cache->load($id);
    //		if (false === $data) {
    //			// nie ma w cache - to dodajemy
    //			$data = parent::fetchRow($where, $order);
    //			// dodaje tag `row`, w momecie czyszczenia keszu idzie
    //			// czyszczenie tylko `row`
    //			$tags = $this->_getCacheTags(array(('row' . $this->_schema . $this->_name)));
    //			$cache->save($data, $id, $tags);
    //		}
    //
    //		return $data;
    //	}
    //
    //	/**
    //	 * Overwrite
    //	 * @return int
    //	 */
    //	public function delete($where) {
    //		$result = parent::delete($where);
    //
    //		// sprawdza czy cache ON?
    //		if ($this->isCaching()) {
    //			// tworzenie identyfikatora cache
    //			// TODO udoskonalić tworzenie id .. parsowanie where
    //			// pod warunkiem wyszukania parentKey ..
    ////			$sqlId = ($where instanceof Zend_Db_Table_Select)
    ////				? $this->_getCacheIdFromSql($where->__toString(), current($this->info(self::PRIMARY)))
    ////				: $this->_getCacheIdFromSql($where);
    //
    //			$args = func_get_args();
    //			$id = md5($this->_schema . $this->_name. implode(':', $args));
    //
    //			$cache = self::$_defaultRowsetCache;
    ////			$cache->remove($id);
    //			$cache->clean(Zend_Cache::CLEANING_MODE_MATCHING_TAG, array(('rowset' . $this->_schema . $this->_name)));
    //		}
    //
    //		return $result;
    //	}
}