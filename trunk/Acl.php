<?php
/**
 * KontorX_Acl
 * 
 * @category 	KontorX
 * @package 	KontorX_Acl
 * @version 	0.1.7
 * @license		GNU GPL
 * @author 		Marcin `widmogror` Habryn, widmogrod@gmail.com
 */
class KontorX_Acl extends Zend_Acl {
	/**
	 * Nazwa klasy kontrolera sprawdzajacego uprawnienia
	 *
	 * @var string
	 */
	protected $_pluginClass = 'KontorX_Acl_Controller_Plugin_Acl';

    /**
	 * Nazwa klasy pomocnika akcji sprawdzajacego uprawnienia
	 *
	 * @var string
	 */
    protected $_helperClass = 'KontorX_Acl_Controller_Action_Helper_Acl';

	/**
	 * Instacja klasy pluginu sprawdzajacego uprawnienia
	 *
	 * @var KontorX_Acl_Controller_Plugin_Acl
	 */
	protected $_pluginInstance = null;
	
	/**
	 * Przechowuje instancje @see KontorX_Acl
	 * 
	 * @var KontorX_Acl
	 */
	protected static $_mvcInstance;
	
	/**
	 * Konstruktor
	 *
	 * @param Zend_Config|array $options
	 */
	private function __construct($options) {
		$this->setConfig($options);
		$this->_initPlugin();
        $this->_initHelper();
	}

	/**
	 * Zwraca instancje @see KontorX_Acl
	 *
	 * @return KontorX_Acl
	 */
	public static function getInstance() {
		if (null === self::$_mvcInstance) {
			$message = "KontorX_Acl mvc instance was not start";
			require_once 'Zend/Acl/Exception.php';
			throw new Zend_Acl_Exception($message);
		}
		return self::$_mvcInstance;
	}
	
	/**
	 * Uruchamia MVC ACL
	 *
	 * @param mixed $options
	 * @return KontorX_Acl
	 */
	public static function startMvc($options) {
		if (null === self::$_mvcInstance) {
			self::$_mvcInstance = new self($options);
		} else {
			self::$_mvcInstance->setConfig($options);
        }
		return self::$_mvcInstance;
    }

	/**
	 * Inicjuje plugin akcji
	 *
	 */
	protected function _initPlugin() {
        $pluginClass = $this->getPluginClass();
        require_once 'Zend/Controller/Front.php';
        $front = Zend_Controller_Front::getInstance();
        if (!$front->hasPlugin($pluginClass)) {
            require_once 'Zend/Loader.php';
            Zend_Loader::loadClass($pluginClass);
            $front->registerPlugin(
                // register to run last | BUT before the ErrorHandler (if its available)
                $this->_pluginInstance = new $pluginClass($this), 
                1
            );
        }
    }

    /**
     * Zwraca nazwe klasy pluginu
     *
     * @return string
     */
    public function getPluginClass() {
    	return $this->_pluginClass;
    }

    /**
     * Zwraca instancje KontorX_Acl_Controller_Plugin_Acl
     * 
     * Zwraca instancje KontorX_Acl_Controller_Plugin_Acl
     * ale tylko po zainicjowaniu poprzez self::startMvc
     * w przeciwnym razie null
     *
     * @return KontorX_Acl_Controller_Plugin_Acl
     */
    public function getPluginInstance() {
    	return $this->_pluginInstance;
    }

    /**
     * Initialize action helper
     * 
     * @return void
     */
    protected function _initHelper(){
        $helperClass = $this->getHelperClass();
        require_once 'Zend/Controller/Action/HelperBroker.php';
        if (!Zend_Controller_Action_HelperBroker::hasHelper('acl')) {
            require_once 'Zend/Loader.php';
            Zend_Loader::loadClass($helperClass);
            $this->_helperInstance = new $helperClass();
            $this->_helperInstance->setPluginInstance($this->getPluginInstance());
            Zend_Controller_Action_HelperBroker::getStack()->offsetSet(-10, $this->_helperInstance);
        }
    }

    /**
     * @return string
     */
    public function getHelperClass(){
        return $this->_helperClass;
    }

    /**
     * @param  string $helperClass
     * @return KontorX_Acl_Controller_Action_Helper_Acl
     */
    public function setHelperClass($helperClass){
        $this->_helperClass = (string) $helperClass;
        return $this;
    }

    protected $_helperInstance = null;

    /**
     * @return KontorX_Acl_Controller_Action_Helper_Acl
     */
    public function getHelperInstance() {
    	return $this->_helperInstance;
    }
    
	/**
	 * Ustawienie konfiguracji
	 *
	 * @param Zend_Config|array $config
	 */
	public function setConfig($config) {
		if ($config instanceof Zend_Config) {
			$config = $config->toArray();
		}

		require_once 'Zend/Acl/Role.php';
		require_once 'Zend/Acl/Resource.php';
		
		foreach ($config as $roleName => $resources){
			$roleName = strtolower($roleName);
			$this->addRole(new Zend_Acl_Role($roleName));

			foreach ($resources as $resourceName => $privileges) {
				$resourceName = strtolower($resourceName);
				if (!$this->has($resourceName)) {
					$this->add(new Zend_Acl_Resource($resourceName));
				}

				if (array_key_exists('allow', $privileges)) {
					if (!is_array($privileges)) {
						$allow = explode('.', strtolower($privileges['allow']));
					} else {
						$allow = $privileges['allow'];
					}
					// czyszczenie pustych kluczy - mogą się zdarzyć
					$allow = array_filter($allow);
					// zezwalaj wszystkim
					// TODO Czy nie będzie jakiś niespodzianek ?
					if (empty($allow)) {
						$this->allow($roleName, $resourceName);
					} else {
						$this->allow($roleName, $resourceName, $allow);
					}
				}
				if (array_key_exists('deny', $privileges)) {
					// TODO zabranianie wszyskim ..
					if (!is_array($privileges)) {
						$deny = explode('.', strtolower($privileges['deny']));
					} else {
						$deny = $privileges['deny'];
					}
					// czyszczenie pustych kluczy - mogą się zdarzyć
					$deny = array_filter($deny);
					// zabraniaj wszystkim
					// TODO Czy nie będzie jakiś niespodzianek ?
					if (empty($deny)) {
						$this->deny($roleName, $resourceName);
					} else {
						$this->deny($roleName, $resourceName, $deny);
					}
				}
			}
		}
	}
}