<?php
require_once 'Zend/Controller/Plugin/Abstract.php';

/**
 * KontorX_Controller_Plugin_System
 *
 * @version 0.2.0
 * @author widmogrod
 */
class KontorX_Controller_Plugin_System extends Zend_Controller_Plugin_Abstract {

	/**
	 * Konstruktor
	 *
	 * @param Zend_Config $config
	 */
	public function __construct(Zend_Config $config) {
		$this->setConfig($config);
	}

	public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request) {
		$this->_initHelper();
	}
	
	/**
	 * @Overwrite
	 *
	 * @param Zend_Controller_Request_Abstract $request
	 */
	public function postDispatch(Zend_Controller_Request_Abstract $request) {
		if (!$request->isDispatched()) {
			// tylko finalny loop
			return;
		}

		// inicjujemy layout
		$this->_initLayout();
	}

	/**
     * Initialize action helper
     * 
     * @return void
     */
    protected function _initHelper(){
        $helperClass = $this->getHelperClass();
        require_once 'Zend/Controller/Action/HelperBroker.php';
        if (!Zend_Controller_Action_HelperBroker::hasHelper('system')) {
            require_once 'Zend/Loader.php';
            Zend_Loader::loadClass($helperClass);
            $this->_helperInstance = new $helperClass();
            $this->_helperInstance->setPluginInstance($this);
            Zend_Controller_Action_HelperBroker::getStack()->offsetSet(-20, $this->_helperInstance);
        }
    }

    /**
     * @var string
     */
    protected $_helperClass = 'KontorX_Controller_Action_Helper_System';
    
    /**
     * @return string
     */
    public function getHelperClass(){
        return $this->_helperClass;
    }

    /**
     * @param  string $helperClass
     * @return KontorX_Controller_Action_Helper_System
     */
    public function setHelperClass($helperClass){
        $this->_helperClass = (string) $helperClass;
        return $this;
    }

    /**
     * @var KontorX_Controller_Action_Helper_System
     */
    protected $_helperInstance = null;

    /**
     * @return KontorX_Controller_Action_Helper_System
     */
    public function getHelperInstance() {
    	return $this->_helperInstance;
    }

	/**
	 * Inicjowanie skórowania ;]
	 *
	 * @return void
	 */
	protected function _initLayout() {
		require_once 'Zend/Layout.php';
		$layout = Zend_Layout::getMvcInstance();
		if (null === $layout) {
			// TODO To jeszczeni nie działa!
			// problem z filtrami będę chciał się w to zagłębić!
//			$layout = Zend_Layout::startMvc();
			require_once 'Zend/Controller/Exception.php';
			throw new Zend_Controller_Exception("Zend_Layout is not initializet with MVC");
		}

		// czy jest wyłączony layout
		if (!$layout->isEnabled()) {
			return;
		}

		// jezeli layout włączony, ale nie jest ustawiony template ..
		if (!$this->isLayout()) {
			// to wyłanczam layout!
			$layout->disableLayout();
			return;
		}
		
		$config 		= $this->getConfig();
		$configDefault  = $this->getDefaultConfig();
		
		// ustawiamy nazwę skórki
		$templateName = $this->getTemplateName();
		if(null === $templateName) {
			$templateName = $config['template']['name'];
		}

		// katalog z szablonem default
		list($layoutPathDefault, $layoutPathDefaultI18n) = $this->getTemplatePaths($configDefault['template']['name']);
		// katalog z szablonem template
		list($layoutPathTemplate, $layoutPathTemplateI18n) = $this->getTemplatePaths($templateName);
		// ustawianie przeszukiwania katalogów
		// zasada LIFO stąd ta kolejność
		// możliwości nadpisywania tworzenia niepełnego szablonu!
		$view = $layout->getView()
			->addScriptPath($layoutPathDefault)
			->addScriptPath($layoutPathDefaultI18n)
			->addScriptPath($layoutPathTemplate)
			->addScriptPath($layoutPathTemplateI18n);

		$templatePaths = $view->getScriptPaths();

		// ustawienia nazwy layoutu
		$layoutName = $this->getLayoutName();
		if($layoutName == '') {
			$layoutName = $config['template']['layout'];
		}
		$layout->setLayout($layoutName);

		// szukanie konfiguracji
		$templateConfig 		= null;
		$templateConfigType 	= $config['template']['config']['type'];
		$templateConfigFilename = $config['template']['config']['filename'];
		foreach ($templatePaths as $templatePath) {
			$templateConfigPath = $templatePath . '/' . $templateConfigFilename;
			if (is_readable($templateConfigPath)) {
				$templateConfig = new Zend_Config_Ini($templateConfigPath, $layoutName, array('allowModifications' => true));
				break;
			}
		}

		if (null === $templateConfig) {
			// nie ma konfiguracji layoutu
			return;
		}

		// ustawianie nowego pliku layoutu
		// a co!? przecież mogę ;]
		if (null !== $templateConfig->layout) {
			$layout->setLayout($templateConfig->layout);
		}
		// title
		if (isset($templateConfig->title)) {
			$headTitle = $view->headTitle();
			$headTitle->setSeparator(' - ');
			$headTitle->set($templateConfig->title);
		}
		// meta
		if (isset($templateConfig->meta)) {
			$headMeta = $view->headMeta();
			$headMeta->setName('keywords', $templateConfig->meta->name->keywords);
			$headMeta->appendName('description', $templateConfig->meta->name->description);
		}
		// script
		if (isset($templateConfig->script)) {
			$headScript = $view->headScript();
			foreach ($templateConfig->script->js as $file) {
				// TODO Dodać sprawdzenie czy sciezka jest relatywna czy nie!
				$headScript->appendFile($file->src);
			}
		}
		// link
		$headLink = $view->headLink();
		if (isset($templateConfig->links)) {
			foreach ($templateConfig->links->css as $file) {
				// TODO Dodać sprawdzenie czy sciezka jest relatywna czy nie!
				$headLink->appendStylesheet($file->href);
			}
		}
	}

	/**
	 * @var string
	 */
	protected $_templatePath = null;
	
	/**
	 * Zwraca tablicę z katalogami z szablonu.
	 *
	 * @param string $templateName
	 * @return array()
	 */
	public function getTemplatePaths($templateName) {
		if (null === $this->_templatePath) {
			$config 		= $this->getConfig();
			$this->_templatePath = $config['template']['path'];
		}

		$path 	= $this->_templatePath . '/' . $templateName;
		$path 	= $this->getPublicHtmlPath($path);
		$pathI18n	= $path . '/' . $this->getLanguage();

		return array($path, $pathI18n);
	}
	
	/**
	 * @var array
	 */
	protected $_config = null;
	
	/**
	 * @var Zend_Config
	 */
	protected $_configRaw = null;

	/**
	 * Domyślna konfiguracja systemu
	 *
	 * @var array
	 */
	protected $_defaultConfig = array(
		'template' => array(
			'name' 	=> 'default',
			'layout'=> 'index',
			'path'	=> './layout',
			'config'=> array(
				'filename' => 'config.ini',
				'type' 	   => 'ini'
			)
		),
		'language' => array(
			'default' => 'pl'
		)
	);
	
	/**
	 * Ustawia konfigurację
	 *
	 * @param Zend_Config $config
	 */
	public function setConfig(Zend_Config $config) {
		$this->_config 	  = null;
		$this->_configRaw = $config;
	}

	/**
	 * Zwraca konfigurację
	 *
	 * Parametr $raw false zwraca konfiguracje połączoną
	 * z konfiguracją domyślną, tj. jeżeli jakaś wartość
	 * nie została ustawiona przyjmuje wartość domyślną
	 * 
	 * @param bool $raw
	 * @return Zend_Config
	 */
	public function getConfig($raw = false) {
		if (false === $raw) {
			if (null === $this->_config) {
				$this->_config = array_merge(
					$this->_defaultConfig,
					$this->_configRaw->toArray());
			}
			return $this->_config;
		}
		return $this->_configRaw;
	}

	/**
	 * Zwraca domyślną konfigurację
	 *
	 * @return array
	 */
	public function getDefaultConfig() {
		return $this->_defaultConfig;
	}

	const APPLICATION_MODULES_DIRNAME = 'modules';
	const APPLICATION_CONFIGURATION_DIRNAME = 'configuration';
	
	/**
	 * @var string
	 */
	protected $_applicationPath = null;

	/**
	 * Ustawia ścieżkę do katalogu application
	 *
	 * @param string $path
	 */
	public function setApplicationPath($path) {
		$this->applicationPath = (string) $path;
	}

	/**
	 * Zwraca ścieżkę do katalogu application
	 *
	 * @param string $type
	 * @return string
	 */
	public function getApplicationPath($type = null) {
		$return = $this->applicationPath;
		switch ($type) {
			case self::APPLICATION_MODULES_DIRNAME:
				$return .= '/modules/';
				break;
			case self::APPLICATION_CONFIGURATION_DIRNAME:
				$return .= '/configuration/';
				break;
		}
		return $return;
	}
	
	/**
	 * @var string
	 */
	protected $_publicHtmlPath = null;

	/**
	 * Ustawia ścieżkę do katalogu publi_html
	 *
	 * @param string $path
	 */
	public function setPublicHtmlPath($path) {
		$this->_publicHtmlPath = (string) $path;
	}

	/**
	 * Zwraca ścieżkę do katalogu publi_html
	 *
	 * @param string $append
	 * @return string
	 */
	public function getPublicHtmlPath($append = null) {
		if (null !== $append) {
			return $this->_publicHtmlPath . '/' . $append;
		}
		return $this->_publicHtmlPath;
	}
	
	/**
	 * @var string
	 */
	protected $_language = null;

	/**
	 * Ustawia język
	 *
	 * @param string $language
	 */
	public function setLanugage($language) {
		$this->_language = (string) $language;
	}

	/**
	 * Zwraca skrót umiędzynaradawianej watość Języka np. pl, en itd. lub null
	 * @return string
	 */
	public  function getLanguage() {
		if (null === $this->_language) {
			$front 	 = $this->getFrontController();
			$request = $front->getRequest();
    		$this->_language = $request->getParam('language_url', $front->getParam('i18n'));
		}

		return $this->_language;
	}

	/**
	 * @var string
	 */
	protected $_templateName = null;

	/**
	 * Ustawia nazwę skórki
	 *
	 * @param string $name
	 */
	public function setTemplateName($name) {
		$this->_templateName = (string) $name;
	}

	/**
	 * Zwraca nazwę skórki
	 *
	 * @param bool $forse
	 * @return string
	 */
	public function getTemplateName($forse = false) {
		if (true === $forse && null === $this->_templateName) {
			$config = $this->getConfig();
			$this->_templateName = $config['template']['name'];
		}
		return $this->_templateName;
	}

	/**
	 * @var string
	 */
	protected $_layoutName = null;
	
	/**
	 * Ustawia nazwę szablonu
	 *
	 * @param string $name
	 */
	public function setLayoutName($name) {
		$this->_layoutName = (string) $name;
	}

	/**
	 * Zwraca nazwę layotu
	 *
	 * @return string
	 */
	public function getLayoutName() {
		return $this->_layoutName;
	}

	/**
	 * Sprawdza czy jest używany layout
	 *
	 * @return bool
	 */
	public function isLayout() {
		return (null !== $this->_layoutName || null !== $this->_templateName); 
	}
	
	/**
	 * @var Zend_Controller_Front
	 */
	protected $_frontController;

    /**
     * @return Zend_Controller_Front
     */
    public function getFrontController() {
        if (null === $this->_frontController) {
            $this->_frontController = Zend_Controller_Front::getInstance();
        }
        return $this->_frontController;
    }
}