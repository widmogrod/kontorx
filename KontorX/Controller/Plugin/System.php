<?php
require_once 'Zend/Controller/Plugin/Abstract.php';

/**
 * KontorX_Controller_Plugin_System
 *
 * @version 0.2.0
 * @author widmogrod
 */
class KontorX_Controller_Plugin_System extends Zend_Controller_Plugin_Abstract {

    const CACHE	   = 'CACHE';
    const TEMPLATE = 'TEMPLATE';
    const LANGUAGE = 'LANGUAGE';

    const CACHE_PREFIX = 'KontorX_Controller_Plugin_System';

    /**
     * @var array
     */
    protected $_configTypes = array(
        self::CACHE,
        self::TEMPLATE,
        self::LANGUAGE
    );

    /**
     * Konstruktor
     *
     * @param Zend_Config $config
     */
    public function __construct(Zend_Config $config) {
        // ustawienie domyslenej konfiguracji!
        $this->setConfig($config);
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
        self::TEMPLATE => array(
            'name' 	=> 'default',
            'layout'=> 'index',
            'path'	=> './template',
            'config'=> array(
                'filename' => 'config.ini',
                'type' 	   => 'ini'
        )),
        self::LANGUAGE => array(
            'default' => 'pl'
        ),
        self::CACHE => array(
            'id' => 'params',
            'frontendName' => 'Core',
            'backendName' => 'File',
            'frontendOptions' => array(
                'lifetime' => 120,
                'automatic_serialization' => true),
            'backendOptions' => array(
                    'cache_dir' => './tmp/'))
    );

    /**
     * Ustawia konfigurację
     *
     * @param Zend_Config|array $options
     * @param string $type
     */
    public function setConfig($options, $type = null) {
        if ($options instanceof Zend_Config) {
            $config = $options->toArray();
        } else
        if (is_array($options)) {
            $config = $options;
        } else {
            $message = "Config is not array or instance of Zend_Config";
            require_once 'Zend/Controller/Exception.php';
            throw new Zend_Controller_Exception($message);
        }

        switch ($type) {
            case self::TEMPLATE:
            case self::LANGUAGE:
            case self::CACHE:
                if (!isset($this->_config[$type])) {
                    $this->_config[$type] = $this->_createConfig($this->_defaultConfig[$type], $config);
                    //					Zend_Debug::dump($this->_config[$type],1);
                } else {
                    $this->_config[$type] = $this->_createConfig($this->_config[$type], $config);
                    //					Zend_Debug::dump($this->_config[$type],2);
                }
                break;
            default:
                $this->_config = $this->_createConfig($this->_defaultConfig, $config);
                //				$this->_configRaw = $options;
        }
    }

    /**
     * Alternatywa array_merge
     *
     * @param $defaultConfig array
     * @param $config array
     * @return array
     */
    private function _createConfig(array $defaultConfig, array $config) {
        $result = array();
        foreach ($defaultConfig as $key => $array) {
            if (array_key_exists($key, $config)) {
                if (is_array($config[$key])) {
                    $result[$key] = $this->_createConfig($array, $config[$key]);
                } else {
                    $result[$key] = $config[$key];
                }

            } else {
                $result[$key] = $array;
            }
        }
        return $result;
    }

    /**
     * Zwraca konfigurację
     *
     * Parametr $raw false zwraca konfiguracje połączoną
     * z konfiguracją domyślną, tj. jeżeli jakaś wartość
     * nie została ustawiona przyjmuje wartość domyślną
     * Parametr $raw może również być typu Zend_Config|array
     *
     * @param Zend_Config|array|bool $raw
     * @return Zend_Config|array
     */
    public function getConfig($config = null, $type = null) {
        $merge = null;
        if ($config instanceof Zend_Config) {
            $merge = $raw->toArray();
        } else
        if (is_array($config)) {
            $merge = $config;
        }
        // pierwszy parametr w takim razie jest typem!
        if (null === $type && is_string($config)) {
            $type = $config;
        }
        $result = (in_array($type, $this->_configTypes))
        ? $this->_config[$type] : $this->_config ;

        $result = (null === $merge)
        ? $result
        : array_merge($result, $merge);

        //		Zend_Debug::dump($result);
        return $result;
    }

    /**
     * Zwraca domyślną konfigurację
     *
     * @return array
     */
    public function getDefaultConfig() {
        return $this->_defaultConfig;
    }

    public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request) {
        // inicjujemy pomocnik akcji!
        $this->_initHelper();
    }

    /**
     * @Overwrite
     * @param Zend_Controller_Request_Abstract $request
     */
    public function postDispatch(Zend_Controller_Request_Abstract $request) {
        // inicjujemy cache
        $this->_initCache($request);

        // tylko finalny loop
        if ($request->isDispatched()) {
            // inicjujemy layout
            $this->_initLayout();
        }
    }

    /**
     * Initialize action helper
     *
     * @return void
     */
    protected function _initHelper() {
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
    public function getHelperClass() {
        return $this->_helperClass;
    }

    /**
     * @param  string $helperClass
     * @return KontorX_Controller_Action_Helper_System
     */
    public function setHelperClass($helperClass) {
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
            $templateName = $config[self::TEMPLATE]['name'];
        }

        // katalog z szablonem default
        list($layoutPathDefault, $layoutPathDefaultI18n) = $this->getTemplatePaths($configDefault[self::TEMPLATE]['name']);
        // katalog z szablonem template
        list($layoutPathTemplate, $layoutPathTemplateI18n) = $this->getTemplatePaths($templateName);
        // ustawianie przeszukiwania katalogów
        // zasada LIFO stąd ta kolejność
        // możliwości nadpisywania tworzenia niepełnego szablonu!
        $view = $layout->getView()
        ->addScriptPath($layoutPathDefault)
//        ->addScriptPath($layoutPathDefaultI18n)
        ->addScriptPath($layoutPathTemplate);
//        ->addScriptPath($layoutPathTemplateI18n);

        $templatePaths = $view->getScriptPaths();

        // ustawienia nazwy layoutu
        $layoutName = $this->hasLayoutName()
        ? $this->getLayoutName()
        : $config[self::TEMPLATE]['layout'];

        $layout->setLayout($layoutName);

        $layoutSection = $this->hasLayoutSectionName()
        ? $this->getLayoutSectionName()
        : $layoutName;

        // szukanie konfiguracji
        $templateConfig 		= null;
        $templateConfigType 	= $config[self::TEMPLATE]['config']['type'];
        $templateConfigFilename = $config[self::TEMPLATE]['config']['filename'];
        foreach ($templatePaths as $templatePath) {
            $templateConfigPath = $templatePath . '/' . $templateConfigFilename;
            if (is_readable($templateConfigPath)) {
                $templateConfig = new Zend_Config_Ini($templateConfigPath, null, true);
                if (isset($templateConfig->$layoutSection)
                		&& $templateConfig->$layoutSection instanceof Zend_Config) {
                	$templateConfig = $templateConfig->$layoutSection;
                }                
                break;
            }
        }

        if (null === $templateConfig) {
            // nie ma konfiguracji layoutu
            return;
        }

		// hack, for new version
		$options = $templateConfig;

        // ustawianie nowego pliku layoutu gdy nie był wcześniej ustawiony "ręcznie"
        // z poziomu kodu
        // a co!? przecież mogę ;]
        if (!$this->isLockLayoutName() && null !== $templateConfig->layout) {
            $layout->setLayout($templateConfig->layout);
        }

		// doctype
		if (isset($options->doctype)) {
			$view->doctype($options->doctype);
		}

        // title
		if (isset($options->title)) {
            $headTitle = $view->getHelper('HeadTitle');
            $title = $options->title;
            $separator = ' ';
            if (isset($title->title)) {
            	$title = $title->title;
            	$separator = isset($title->separator)
            		? $title->separator : $separator;
            } 

            $headTitle->append($title);
            $headTitle->setSeparator($separator);
        }

        // meta
        if (isset($templateConfig->meta)) {
            // sprawdzanie czy są już ustawione meta dane
            // TODO czy podwojne metadane przeszkadzają??
            // bo wlasnie po to jest sprawdzanies
            $meta = array();
            $headMeta = $view->headMeta();
            foreach ($headMeta->getContainer() as $key) {
            	if (isset($key->name)) {
            		if ($key->name == 'keywords') {
	                    $meta['keywords'] = true;
	                } else
	                if ($key->name == 'description') {
	                    $meta['description'] = true;
	                }
            	}
            }

            if (!isset($meta['keywords'])) {
                $headMeta->appendName('keywords', $templateConfig->meta->name->keywords);
            }
            if (!isset($meta['description'])) {
                $headMeta->appendName('description', $templateConfig->meta->name->description);
            }
            
        	if (isset($options->meta->httpEquiv)
            		&& $options->meta->httpEquiv instanceof Zend_Config) {
	            foreach ($options->meta->httpEquiv as $obj) {
	            	$headMeta->setHttpEquiv($obj->key,
	            							$obj->content,
	            							isset($obj->modifiers) ? $obj->modifiers->toArray() : array());
	            }
            }
        }

    	// script
        if (isset($options->script)) {
        	/* @var $headScript Zend_View_Helper_HeadScript */
            $headScript = $view->getHelper('HeadScript');
            $i = 0;
            foreach ($options->script->js as $file) {
                $headScript->offsetSetFile(++$i,
            								$file->src,
            								isset($file->type) ? $file->type : null,
            								isset($file->attribs) ? $file->attribs->toArray() : array());
            }
        }
        
		// inlineScript
        if (isset($options->inlineScript)) {
        	/* @var $inlineScript Zend_View_Helper_InlineScript */
            $inlineScript = $view->getHelper('InlineScript');
            $i = 0;
            foreach ($options->inlineScript->js as $file) {
            	$inlineScript->offsetSetFile(++$i,
            								$file->src,
            								isset($file->type) ? $file->type : null,
            								isset($file->attribs) ? $file->attribs->toArray() : array());
            }
        }

        // link
        if (isset($options->links)) {
        	$headLink = $view->getHelper('HeadLink');
            foreach ($options->links->css->toArray() as $file) {
                if (!isset($file['rel'])) {
                	$file['rel'] = 'stylesheet';
                }
            	if (!isset($file['media'])) {
					$file['media'] = 'screen';
				}
                $headLink->headLink($file);
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
            $config = $this->getConfig(false, self::TEMPLATE);
            $this->_templatePath = $config['path'];
        }

        $path 	= $this->_templatePath . '/' . $templateName;
//        $path 	= $this->getTemplateHtmlPath($path);
        $pathI18n	= $path . '/' . $this->getLanguage();

        return array($path, $pathI18n);
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
//    protected $_templateHtmlPath = null;

    /**
     * Ustawia ścieżkę do katalogu publi_html
     *
     * @param string $path
     */
    /*public function setTemplateHtmlPath($path) {
        $this->_templateHtmlPath = (string) $path;
    }*/

    /**
     * Zwraca ścieżkę do katalogu template
     *
     * @param string $append
     * @return string
     */
    /*public function getTemplateHtmlPath($append = null) {
        if (null !== $append) {
            return $this->_templateHtmlPath . '/' . $append;
        }
        return $this->_templateHtmlPath;
    }*/

    /**
     * @var string
     */
    protected $_tempPath = null;

    /**
     * Ustawia ścieżkę do katalogu temp
     *
     * @param string $path
     */
    public function setTempPath($path) {
        $this->_tempPath = (string) $path;
    }

    /**
     * Zwraca ścieżkę do katalogu temp
     *
     * @param string $append
     * @return string
     */
    public function getTempPath($append = null) {
        if (null !== $append) {
            return $this->_tempPath . '/' . $append;
        }
        return $this->_tempPath;
    }

    private $_cacheInstance = array();

    /**
     * @param $options
     * @return Zend_Cache_Core
     */
    public function getCacheInstance(array $options) {
        // marge options with default
        $options = $this->getConfig($options, self::CACHE);
        //		Zend_Debug::dump($options, 123);

        $key = sha1(serialize($options));
        if (!array_key_exists($key, $this->_cacheInstance)) {
            if (!class_exists('Zend_Cache',false)) {
                require_once 'Zend/Cache.php';
            }
            $this->_cacheInstance[$key] = Zend_Cache::factory(
                $options['frontendName'],
                $options['backendName'],
                $options['frontendOptions'],
                $options['backendOptions']
            );
        }
        return $this->_cacheInstance[$key];
    }

    /**
     * @param $request
     * @param $options
     * @return string
     */
    public function getCacheActionId(Zend_Controller_Request_Abstract $request, array $options) {
        // marge options with default
        $options = $this->getConfig($options, self::CACHE);
        // base cacheIdName
        $key = $this->_getKeyName($request);

        $type = $options['id'];
        if (is_string($type)) {
            switch ($type) {
                case 'params':
                    $append = spl_object_hash($request);
                    break;
            }
        } else
        if (is_array($type)) {
            if (isset($type['param'])) {
                $append = md5($request->getParam($type['param']));
            }
        }

        return self::CACHE_PREFIX . $key . '_' . $append;
    }

    /**
     * @var bool
     */
    private $_cached = false;

    /**
     * @param $flag
     * @return void
     */
    public function setCached($flag = true) {
        $this->_cached = (bool) $flag;
    }

    /**
     * @return bool
     */
    public function isCached() {
        return $this->_cached;
    }

    /**
     * @var bool
     */
    private $_cacheView = false;

    /**
     * @param $flag
     * @return void
     */
    public function setCacheView($flag = true) {
        $this->_cacheView = (bool) $flag;
    }

    /**
     * @return bool
     */
    public function cacheView() {
        return $this->_cacheView;
    }

    /**
     * @var array
     */
    private $_cacheActionOptions = array();

    /**
     * @param array $options
     * @param Zend_Controller_Request_Abstract $request
     * @return void
     */
    public function setCacheActionOptions(array $options, Zend_Controller_Request_Abstract $request) {
        $keyName = $this->_getKeyName($request);
        $this->_cacheActionOptions[$keyName] = $options;
    }

    /**
     * @param Zend_Controller_Request_Abstract $request
     * @return array
     */
    public function getCacheActionOptions(Zend_Controller_Request_Abstract $request, $margeDefault = false) {
        $keyName = $this->_getKeyName($request);
        $options = array_key_exists($keyName, $this->_cacheActionOptions)
        ? $this->_cacheActionOptions[$keyName]
        : null;

        if (!isset($options['backendOptions']['cache_dir'])) {
            $options['backendOptions']['cache_dir'] = $this->getTempPath('cache');
        }

        if (null !== $options || $margeDefault) {
            $options = $this->getConfig($options, self::CACHE);
        }

        return $options;
    }

    /**
     * @param Zend_Controller_Request_Abstract $request
     * @return bool
     */
    public function hasCacheActionOptions(Zend_Controller_Request_Abstract $request) {
        $keyName = $this->_getKeyName($request);
        return array_key_exists($keyName, $this->_cacheActionOptions);
    }

    /**
     * @param Zend_Controller_Request_Abstract $request
     * @return string
     */
    private function _getKeyName(Zend_Controller_Request_Abstract $request) {
        return $request->getModuleName() . '_' .
        $request->getControllerName() . '_' .
        $request->getActionName();
    }

    /**
     * @param $request
     * @return unknown_type
     */
    private function _initCache(Zend_Controller_Request_Abstract $request) {
        if (!$this->hasCacheActionOptions($request)) {
            return;
        }

        if ($this->isCached()) {
            // ustawienie flagi ze akcja wykonana!
            // bo powodujemy ominięcie egzekucji akcji w @see *Helper_System::preDispatch()
            $request->setDispatched(true);
        } else
        if ($this->cacheView()) {
            $options = $this->getCacheActionOptions($request);
            // bindowanie domyslnych opcji dla `cache`
            $options = $this->getConfig($options, self::CACHE);

            $cacheInstance = $this->getCacheInstance($options);

            $cacheData = array();

            // dodajemy widok do cache!
            $body = $this->getResponse()->getBody();
            $cacheData['body'] = $body;
//            if (Zend_Controller_Action_HelperBroker::hasHelper('viewRenderer')) {
//                $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
//                if (null !== $viewRenderer->view) {
//                    $zendView = clone $viewRenderer->view;
//                    $zendView->clearVars();
//                    $cacheData['Zend_View'] = $zendView;
//                }
//            }

            $cacheId = $this->getCacheActionId($request, $options);
            $cacheInstance->save($cacheData, $cacheId);
        }
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
            $this->_templateName = $config[self::TEMPLATE]['name'];
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
     * Czy został ustawiony nazwa pliku szablonu (layput)
     *
     * W domyśle nalerzy rozumieć, czy została ustawiona ręcznie
     * Ustawienie ręczne stanowi najwyższy priorytet!
     *
     * @return bool
     */
    public function hasLayoutName() {
        return !empty($this->_layoutName);
    }

    /**
     * @var bool
     */
    protected $_lockLayoutName = false;

    /**
     * @return bool
     */
    public function isLockLayoutName() {
        return $this->_lockLayoutName;
    }

    /**
     * @param bool $flag
     * @return void
     */
    public function lockLayoutName($flag = true) {
        $this->_lockLayoutName = (bool) $flag;
    }

    /**
     * @var string
     */
    protected $_layoutSectionName = null;

    /**
     * @param string $name
     * @return void
     */
    public function setLayoutSectionName($name) {
        $this->_layoutSectionName = (string) $name;
    }

    /**
     * @return string|null
     */
    public function getLayoutSectionName() {
        return $this->_layoutSectionName;
    }

    /**
     * @return bool
     */
    public function hasLayoutSectionName() {
        return null !== $this->_layoutSectionName;
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
