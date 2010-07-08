<?php
/**
 * @author $Author$
 * @version $Id$
 */
class KontorX_Sisi
{
	const RESPONSE = 'RESPONSE';
	const ACTION = 'ACTION';

    /**
     * @var KontorX_Sisi_Response_Abstract
     */
    protected $_response;

    /**
     * @var string
     */
    protected $_action;
    
    /**
     * @var string
     */
    protected $_params = array();
    
    /**
     * @var array
     */
    protected $_paths = array(
    	self::RESPONSE => array(),
    	self::ACTION => array()
    );
    
    /**
     * @var array
     */
    protected $_restrictedOptionKeys = array(
    	'prefxpath'
    );
    
    /**
     * @var bool
     */
    protected static $_testing = false;

    /**
     * @param array|null $options 
     */
    public function __construct($options = null) {
    	$this->setPrefixPaths(array(
    		'KontorX_Sisi'
    	));

        if (is_array($options))
            $this->setOptions($options);
    }

    /**
     * Implicite, podczas destrukcji klasy
     * zwracane jest wynik response do przeglądarki
     */
    public function __destruct() {
		// tesotwanie aplikacji to wyłanczam destruct
		if (self::$_testing) 
			return;

        print $this->getResponse()->send();
    }

	/**
	 * @param bool $flag
	 */
	public static function setTesting($flag) {
		self::$_testing = (bool) $flag;
	}

    /**
     * @param array $options
     */
    public function setOptions(array $options) {
        foreach($options as $key => $value) {
        	if (in_array(strtolower($key), $this->_restrictedOptionKeys))
        		continue;

            $method = 'set'. ucfirst($key);
            if (method_exists($this, $method))
                $this->$method($value);
            else
            	$this->setParam($key, $value);
        }
    }
    
    /**
	 * @param array $paths
     */
    public function setPrefixPaths(array $paths) {
    	$this->clearPrefixPath();

    	foreach($paths as $path)
    		$this->addPrefixPath($path);
    }

    /**
     * @param string $prefix
     * @param string $path
     * @param string $type
     */
    public function addPrefixPath($prefix, $path = null, $type = null) {
    	$prefix = rtrim($prefix,'_');
    	
    	if (null === $path)
    		$path = str_replace('_','/', $prefix);

		$path = str_replace(array('..','//'), '', $path);
		$path = rtrim($path, '/');

		$pathInfo = array(
			'prefix' => $prefix,
			'path' => $path,
		);

		switch($type) {
			case self::RESPONSE:
			case self::ACTION:
				array_unshift($this->_paths[$type], $pathInfo);
				break;

			default:
				$responsePathInfo = $pathInfo;
				$responsePathInfo['prefix'] .= '_Response';
				$responsePathInfo['path'] .= '/Response';
				
				array_unshift($this->_paths[self::RESPONSE], $responsePathInfo);
				
				$actionPathInfo = $pathInfo;
				$actionPathInfo['prefix'] .= '_Action';
				$actionPathInfo['path'] .= '/Action';
				array_unshift($this->_paths[self::ACTION], $actionPathInfo);		
		}
    }

	/**
	 * 
     */
    public function clearPrefixPath() {
    	$this->_paths = array(
			self::RESPONSE => array(),
			self::ACTION => array()
		);
    }
    
    /**
     * Sprawdź czy plik istnieje w ścieżce plików.
	 * Działa na zasadzie LIFO
	 *
	 * @param string $anme
	 * @param string $type
	 * @return string|null
     */
	protected function _loadClass($name, $type) {
		$name = basename($name);
		$name = ucfirst($name);
		
		$includePaths = (array) get_include_path();
		
		foreach($this->_paths[$type] as $pathInfo) {
			$className = $pathInfo['prefix'] . '_' . $name;
			
			if (class_exists($className, false))
				return $className;

			$path = $pathInfo['path'];
			$path .= '/' . $name . '.php';

			foreach ($includePaths as $includePath)
				if (!is_file($includePath . '/' . $path))
					continue;
				
			require_once $path;

			if (!class_exists($className))
				throw new Exception(sprintf('Response class "%s" do not exsist in path "%s"', $className, $pathInfo['path']));

			return $className;
		}
	}

    /**
     * @param KontorX_Sisi_Response_Abstract|string $response
     */
    public function setResponse($response) {
        if (is_string($response))
        {
			if ($className = $this->_loadClass($response, self::RESPONSE))
	            $response = new $className;
        }
        
        if (!$response instanceof KontorX_Sisi_Response_Abstract)
            throw new Exception('Response is not instance of "KontorX_Sisi_Response_Abstract" ');

        $this->_response = $response;
    }

    /**
     * @return KontorX_Sisi_Response_Abstract
     */
    public function getResponse() {
        if (null === $this->_response) {
                require_once "KontorX/Sisi/Response/Json.php";
                $this->_response = new KontorX_Sisi_Response_Json();
        }

        return $this->_response;
    }

    /**
     * @param string $action
     */
    public function setAction($action) {
        $this->_action = (string) ucfirst(basename($action));
    }

    /**
     * @param array $params
     */
    public function setParams($params) {
        $this->_params = (array) $params;
    }

    /**
     * @return array
     */
    public function getParams() {
        return $this->_params;
    }
    
    /**
     * @param array $params
     */
    public function setParam($name, $value) {
        $this->_params[(string)$name] = $value;
    }

    /**
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public function getParam($name, $default = null) {
    	$name = (string) $name;
    	return (array_key_exists($name, $this->_params))
            ? $this->_params[$name]
            : $default;
    }

    /**
     * Inicjacha Api
     * - rozpoznanie akcji
     */
    public function handle() {
        $response = $this->getResponse();
        
        if (!$this->_action) {
            $response->addMessage("Akcja nie została podana");
            return;
        }
        
        if ($className = $this->_loadClass($this->_action, self::ACTION))
            $actionInstance = new $className;
        
#        if (!class_exists($actionClass))
##        && !file_exists($actionClassFile) ) 
#        {
#            $response->addMessage(sprintf('Akcja "%s" nie istnieje', $this->_action));
#            return;
#        }
#        
#        require_once $actionClassFile;

#        if (!class_exists($actionClass)) {
#            $response->addMessage(sprintf('Klasa akcji "%s" nie istnieje', $this->_action));
#            return;
#        }
        
#        $actionInstance = new $actionClass();

        if (!($actionInstance instanceof KontorX_Sisi_Action_Interface)) {
            $response->addMessage(sprintf('Akcja "%s" nie implementuje interfejsu "KontorX_Sisi_Action_Interface"', $this->_action));
            return;
        }

        $actionInstance->run($this);
    }
}
