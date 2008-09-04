<?php
require_once 'Zend/Controller/Plugin/Abstract.php';
class KontorX_Controller_Plugin_Bootstrap extends Zend_Controller_Plugin_Abstract {

	public function init() {
		$this->_initConfiguration();
		
		$this->_initAcl();
		$this->_initRouter();
		$this->_initCache();
		$this->_initDatabase();
		$this->_initForm();
		$this->_initFramework();
		$this->_initLayout();
		$this->_initLocale();
		$this->_initLog();
	}
	
	/**
	 * Instance of @see Zend_Controller_Front
	 *
	 * @var Zend_Controller_Front
	 */
	protected $_frontController = null;

	/**
	 * Zwraca instancje @see Zend_Controller_Front
	 *
	 * @return Zend_Controller_Front
	 */
	public function getFrontController() {
		if (null === $this->_frontController) {
			$this->_frontController = Zend_Controller_Front::getInstance();
		}
		return $this->_frontController;
	}

	const PRODUCTION  = 'production';
	const DEVELOPMENT = 'development';

	protected $_bootstrap = null;
	
	public function setBootstrap($type) {
		switch ($type) {
			case self::DEVELOPMENT:
			case self::PRODUCTION:
				$this->_bootstrap = $type;
				break;
		}
		return $this;
	}

	public function getBootstrap() {
		if (null === $this->_bootstrap) {
			$this->_bootstrap = self::DEVELOPMENT;
		}
		return $this->_bootstrap;
	}

	protected $_baseDirectory = null;

	public function setBaseDirectory($directory) {
		if (!is_dir($directory)) {
			$message = "Application base directory `$directory` do not exsists";
			require_once 'Zend/Controller/Exception.php';
			throw new Zend_Controller_Exception($message);
		}
		$this->_baseDirectory = $directory;
		return $this;
	}

	public function getBaseDirectory() {
		if (null === $this->_baseDirectory) {
			$message = "Application base directory is not set";
			require_once 'Zend/Controller/Exception.php';
			throw new Zend_Controller_Exception($message);
		}
		return $this->_baseDirectory;
	}

	protected $_applicationDirname = 'application';

	public function setApplicatoinDirname($dirname) {
		$this->_applicationDirname = $dirname;
	}

	public function getApplicatoinDirectory() {
		return $this->getBaseDirectory() . '/' . $this->_applicationDirname;
	}
	
	protected $_translateDirname = 'languages';

	public function setTranslateDirname($dirname) {
		$this->_configurationDirname = $dirname;
	}

	public function getTranslateDirectory() {
		return $this->getBaseDirectory() . '/' . $this->_translateDirname;
	}

	protected $_logDirname = 'logs';

	public function setLogDirname($dirname) {
		$this->_logDirname = $dirname;
	}

	public function getLogDirectory() {
		return $this->getBaseDirectory() . '/' . $this->_logDirname;
	}
	
	protected $_configurationDirname = 'configuration';

	public function setConfigurationDirname($dirname) {
		$this->_configurationDirname = $dirname;
	}

	public function getConfigurationDirectory() {
		return $this->getApplicatoinDirectory() . '/' . $this->_configurationDirname;
	}

	protected $_configurationOptions = array('allowModifications' => true);

	public function setConfigurationOptions(array $options) {
		$this->_configurationOptions = $options;
		return $this;
	}
	
	public function getConfigurationOptions() {
		return $this->_configurationOptions;
	}

	const CONFIG_FRAMEWORK 		= 'framework';
	const CONFIG_APPLICATION 	= 'application';
	const CONFIG_DATABASE 		= 'database';
	const CONFIG_ROUTER 		= 'router';
	const CONFIG_CACHE 			= 'cache';
	const CONFIG_ACL 			= 'acl';
	
	protected $_configuration = array();
	
	public function getConfiguration($type) {
		if (!array_key_exists($type, $this->_configuration)) {
			$message = "Configuration `$type` do not exsists";
			require_once 'Zend/Controller/Exception.php';
			throw new Zend_Controller_Exception($message);
		}
		return $this->_configuration[$type];
	}

	protected function _initConfiguration() {
		$bootstrap = $this->getBootstrap();
		$directory = $this->getConfigurationDirectory();
		$options   = $this->getConfigurationOptions();

		$this->_configuration[self::CONFIG_FRAMEWORK] 	= new Zend_Config_Ini("$directory/framework.ini", 	$bootstrap, $options);
		$this->_configuration[self::CONFIG_APPLICATION] = new Zend_Config_Ini("$directory/application.ini", $bootstrap, $options);
		$this->_configuration[self::CONFIG_DATABASE]	= new Zend_Config_Ini("$directory/database.ini", 	$bootstrap, $options);
		$this->_configuration[self::CONFIG_ROUTER] 		= new Zend_Config_Ini("$directory/router.ini", 		$bootstrap, $options);
		$this->_configuration[self::CONFIG_CACHE] 		= new Zend_Config_Ini("$directory/cache.ini", 		$bootstrap, $options);
		$this->_configuration[self::CONFIG_ACL]			= new Zend_Config_Ini("$directory/acl.ini", 		null, 		$options);
		
		require_once 'Zend/Registry.php';
		Zend_Registry::set('configFramework', $this->_configuration[self::CONFIG_FRAMEWORK]);
		Zend_Registry::set('configApplication', $this->_configuration[self::CONFIG_APPLICATION]);
		
		return $this;
	}

	protected function _initFramework() {
		$config = $this->getConfiguration(self::CONFIG_FRAMEWORK);

		require_once 'Zend/Controller/Action/HelperBroker.php';
		Zend_Controller_Action_HelperBroker::addPath('KontorX/Controller/Action/Helper','KontorX_Controller_Action_Helper');
		
		$front = $this->getFrontController();
		$front->setControllerDirectory($config->controller->directory->toArray());
		$front->setDefaultModule($config->controller->default->module);
		$front->setBaseUrl($config->baseUrl);
		$front->throwExceptions($config->throwExceptions);
		$front->setParams($config->params->toArray());
	}

	protected function _initRouter() {
		$config = $this->getConfiguration(self::CONFIG_ROUTER);

		$front  = $this->getFrontController();
		$router = $front->getRouter();
		$router->addConfig($config);
	}

	protected function _initCache() {
		$config = $this->getConfiguration(self::CONFIG_ROUTER);

		require_once 'Zend/Cache.php';
//		if (isset($config->dbquery)) {
//			$query = Zend_Cache::factory(
//				$config->dbquery->frontend->name,
//				$config->dbquery->backend->name,
//				$config->dbquery->frontend->options->toArray(),
//				$config->dbquery->backend->options->toArray()
//			);
//		}
		if (isset($config->database)) {
			$database = Zend_Cache::factory(
				$config->database->frontend->name,
				$config->database->backend->name,
				$config->database->frontend->options->toArray(),
				$config->database->backend->options->toArray()
			);
			require_once 'Zend/Db/Table/Abstract.php';
			Zend_Db_Table_Abstract::setDefaultMetadataCache($database);
		}
		if (isset($config->translate)) {
			$translate = Zend_Cache::factory(
				$config->translate->frontend->name,
				$config->translate->backend->name,
				$config->translate->frontend->options->toArray(),
				$config->translate->backend->options->toArray()
			);
			require_once 'Zend/Translate.php';
			Zend_Translate::setCache($translate);
		}
	}

	protected function _initAcl() {
		$config = $this->getConfiguration(self::CONFIG_ACL);

		require_once 'KontorX/Acl.php';
		$acl = KontorX_Acl::startMvc($config);
		$aclPlugin = $acl->getPluginInstance();
		$aclPlugin->setNoAuthErrorHandler('login','auth','user');
	}

	protected function _initDatabase() {
		$config = $this->getConfiguration(self::CONFIG_DATABASE);

		if (isset($config->default)) {
			$db = Zend_Db::factory(
				$config->default->adapter,
				$config->default->config->toArray()
			);
			require_once 'Zend/Db/Table/Abstract.php';
			Zend_Db_Table_Abstract::setDefaultAdapter($db);
		}
	}
	
	protected function _initForm() {
		$directory = $this->getTranslateDirectory();

		require_once 'Zend/Translate.php';
		$translate = new Zend_Translate('Tmx', "$directory/pl/validation.xml", 'pl');
		require_once 'Zend/Form.php';
		Zend_Form::setDefaultTranslator($translate);
	}

	protected function _initLocale() {
		try {
			require_once 'Zend/Locale.php';
		    $locale = new Zend_Locale('auto');
		} catch (Zend_Locale_Exception $e) {
		    $locale = new Zend_Locale('pl');
		}
		require_once 'Zend/Registry.php';
		Zend_Registry::set('Zend_Locale', $locale);
	}

	protected function _initLayout() {
		$config = $this->getConfiguration(self::CONFIG_FRAMEWORK);

		require_once 'Zend/Layout.php';
		Zend_Layout::startMvc($config->layout->toArray());
	}

	protected function _initLog() {
		$directory = $this->getLogDirectory();

		require_once 'Zend/Log.php';
		$logger = new Zend_Log();
		$logger->addWriter(new Zend_Log_Writer_Stream("$directory/application.log"));
		$loggerFramework = new Zend_Log();
		$loggerFramework->addWriter(new Zend_Log_Writer_Stream("$directory/framework.log"));
		
		// w aplikacji wykorzystywane
		require_once 'Zend/Registry.php';
		Zend_Registry::set('logger', $logger);
		Zend_Registry::set('loggerFramework', $loggerFramework);
	}
	
//    public function routeStartup(Zend_Controller_Request_Abstract $request)
//    {}
//
//    public function routeShutdown(Zend_Controller_Request_Abstract $request)
//    {}

    public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request) {
    	
    }

//    public function preDispatch(Zend_Controller_Request_Abstract $request) {
//    	
//    }
//
    public function postDispatch(Zend_Controller_Request_Abstract $request) {
    	
    }
//
//    public function dispatchLoopShutdown()
//    {}
}