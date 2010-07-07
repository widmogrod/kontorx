<?php
/**
 * @author $Author$
 * @version $Id$
 */
class KontorX_Sisi
{
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
     * @var bool
     */
    protected static $_testing = false;

    /**
     * @param array|null $options 
     */
    public function __construct($options = null) {
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
            $method = 'set'. ucfirst($key);
            if (method_exists($this, $method))
                $this->$method($value);
        }
    }
    
    /**
     * @param KontorX_Sisi_Response_Abstract|string $response
     */
    public function setResponse($response) {
        if (is_string($response))
        {
            $name  = ucfirst(basename($response));
            $class = 'KontorX_Sisi_Response_' . $name;
            $file  = str_replace('_','/', $class) . '.php';
            
            if (!class_exists($class)) {
            	// TODO: Dodać sprawdzanie czy plik istnieje
                require_once $file;

                if (!class_exists($class))
                    throw new Exception(sprintf('Response class "%s" do not exsist', $class));
            }
            
            $response = new $class;
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
        
        $actionClass = 'KontorX_Sisi_Action_' . $this->_action;
        $actionClassFile = LIB_PATHNAME . '/' . str_replace('_','/',$actionClass) . '.php';
        
        if (!class_exists($actionClass) && 
                        !file_exists($actionClassFile)) 
        {
            $response->addMessage(sprintf('Akcja "%s" nie istnieje', $this->_action));
            return;
        }
        
        require_once $actionClassFile;

        if (!class_exists($actionClass)) {
            $response->addMessage(sprintf('Klasa akcji "%s" nie istnieje', $this->_action));
            return;
        }
        
        $actionInstance = new $actionClass();

        if (!($actionInstance instanceof KontorX_Sisi_Action_Interface)) {
            $response->addMessage(sprintf('Akcja "%s" nie implementuje interfejsu "KontorX_Sisi_Action_Interface"', $this->_action));
            return;
        }

        $actionInstance->run($this);
    }
}
