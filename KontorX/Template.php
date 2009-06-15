<?php
/**
 * @author gabriel
 */
class KontorX_Template {
	
	/**
     * Helper class
     * @var string
     */
    protected $_helperClass = 'KontorX_Template_Controller_Action_Helper_Template';
	
	/**
     * Plugin class
     * @var string
     */
    protected $_pluginClass = 'KontorX_Template_Controller_Plugin_Template';

	/**
	 * @param Zend_Config|array
	 */
	protected function __construct($options = null) {
		if (is_array($options)) {
			$this->setOptions($options);
		} elseif ($options instanceof Zend_Config) {
			$this->setOptions($options->toArray());
		}

		$this->_initPlugin();
		$this->_initHelper();
	}
	
	/**
     * @return string
     */
    public function getHelperClass() {
        return $this->_helperClass;
    }

    /**
     * @param  string $helperClass
     * @return KontorX_Template
     */
    public function setHelperClass($helperClass) {
        $this->_helperClass = (string) $helperClass;
        return $this;
    }

    /**
     * @return string
     */
    public function getPluginClass() {
        return $this->_pluginClass;
    }

    /**
     * @param  string $pluginClass
     * @return KontorX_Template
     */
    public function setPluginClass($pluginClass) {
        $this->_pluginClass = (string) $pluginClass;
        return $this;
    }
	
	protected function _initPlugin() {
		$pluginClass = $this->getPluginClass();
        $front = Zend_Controller_Front::getInstance();
        if (!$front->hasPlugin($pluginClass)) {
            if (!class_exists($pluginClass)) {
                Zend_Loader::loadClass($pluginClass);
            }
            $front->registerPlugin(
                new $pluginClass($this),
                // before Zend_Layout 
                98
            );
        }
	}
	
	protected function _initHelper() {
		$helperClass = $this->getHelperClass();
        if (!Zend_Controller_Action_HelperBroker::hasHelper('template')) {
            if (!class_exists($helperClass)) {
                Zend_Loader::loadClass($helperClass);
            }
            Zend_Controller_Action_HelperBroker::getStack()->offsetSet(-80, new $helperClass($this));
        }
	}
	
	/**
	 * @var KontorX_Template
	 */
	protected static $_instance;
	
	/**
	 * @param Zend_Config|array
	 * @return KontorX_Template 
	 */
	public static function getInstance($options = null) {
		if (null === self::$_instance) {
			self::$_instance = new self($options);
		}
		return self::$_instance;
	}

	/**
	 * @param array $options 
	 */
	public function setOptions(array $options) {
		foreach ($options as $name => $value) {
			$method = 'set' . ucfirst($name);
			if(method_exists($this, $method)) {
				call_user_func_array(array($this, $method), $value);
			}
		}
	}
	
	/**
	 * @var Zend_Layout
	 */
	protected $_layout;

	/**
	 * @param Zend_Layout $layout 
	 * @return KontorX_Template
	 */
	public function setLayout(Zend_Layout $layout) {
		$this->_layout = $layout;
		return $this;
	}
	
	/**
	 * @return Zend_Layout
	 */
	public function getLayout() {
		if (null === $this->_layout) {
			$this->_layout = Zend_Layout::getMvcInstance();
		}
		return $this->_layout;
	}
	
	public function isStartedLayout() {
		return (!$this->_layout instanceof Zend_Layout);
	}
	
	/**
	 * @var Zend_View_Interface
	 */
	protected $_view;

	/**
	 * @param Zend_View_Interface $view 
	 * @return KontorX_Template
	 */
	public function setView(Zend_View_Interface $view) {
		$this->_view = $view;
		return $this;
	}
	
	/**
	 * @return Zend_View_Interface
	 */
	public function getView() {
		if (null === $this->_view) {
            $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
            if (null === $viewRenderer->view) {
                $viewRenderer->initView();
            }
            $this->setView($viewRenderer->view);
        }
		return $this->_view;
	}

	protected $_templatePath;

	public function setTemplatePath($path) {
		$this->_templatePath = (string) $path;
	}
	
	public function getTemplatePath($spec = null) {
		if (true === $spec) {
			return $this->getInflector()->filter(array(
				'theme' => $this->getThemeName(),
				'style' => $this->getStyleName()
			));
		}
		return $this->_templatePath;
	}
	
	/*protected $_viewPath;
	
	public function setViewPath($path) {
		$this->_viewPath = (string) $path;
	}

	public function getViewPath() {
		return $this->_viewPath;
	}*/

	protected $_layoutName;
	
	public function setLayoutName($layout) {
		$this->getLayout()->setLayout($layout);
	}

	protected $_themeName;
	
	public function setThemeName($theme) {
		$this->_themeName = basename((string) $theme);
	}

	public function getThemeName() {
		return $this->_themeName;
	}

	protected $_styleName;
	
	public function setStyleName($style) {
		$this->_styleName = basename((string) $style);
	}

	public function getStyleName() {
		return $this->_styleName;
	}
	
	protected $_themeDirName = 'themes';
	
	public function setThemeDirName($theme) {
		$this->_themeDirName = rtrim((string) $theme,'/');
	}

	public function getThemeDirName() {
		return $this->_themeDirName;
	}
	
	protected $_styleDirName = 'styles';
	
	public function setStyleDirName($style) {
		$this->_styleDirName = rtrim((string) $style, '/');
	}

	public function getStyleDirName() {
		return $this->_styleDirName;
	}

	protected $_alloweThemeConfig = true;
	
	public function setAlloweThemeConfig($flag = true) {
		$this->_alloweThemeConfig = (bool) $flag;
	}

	public function isAllowedThemeConfig() {
		return $this->_alloweThemeConfig;
	}
	
	protected $_themeConfigName = 'config.ini';
	
	public function setThemeConfigName($name) {
		$this->_themeConfigName = (string) $name;
	}
	
	public function getThemeConfigName() {
		return $this->_themeConfigName;
	}

	protected $_inflectorTarget = ':themesDir/:theme/:stylesDir/:style';

	/**
     * @return string
     */
	public function getInflectorTarget() {
		return $this->_inflectorTarget;
	}

    /**
     * @param  string $inflectorTarget
     * @return Zend_Layout
     */
	public function setInflectorTarget($inflectorTarget) {
		$this->_inflectorTarget = (string) $inflectorTarget;
		return $this;
	}
	
	/**
	 * @var Zend_Filter_Inflector
	 */
	protected $_inflector;
	
	/**
     * @param  Zend_Filter_Inflector $inflector
     * @return Zend_Layout
     */
	public function setInflector(Zend_Filter_Inflector $inflector) {
		$this->_inflector = $inflector;
		return $this;
	}

    /**
     * @return Zend_Filter_Inflector
     */
	public function getInflector() {
		if (null === $this->_inflector) {
			$inflector = new Zend_Filter_Inflector();
            $inflector->setTargetReference($this->_inflectorTarget)
                      ->addRules(array(
                      	':theme' => array('Word_CamelCaseToDash', 'StringToLower'),
                      	':style' => array('Word_CamelCaseToDash', 'StringToLower')
                      ))
                      ->setStaticRuleReference('themesDir', $this->_themeDirName)
                      ->setStaticRuleReference('stylesDir', $this->_styleDirName);
			$this->setInflector($inflector);
		}

		return $this->_inflector;
	}
}