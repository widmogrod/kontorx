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
				$this->$method($value);
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
			$this->_layout = Zend_Layout::startMvc(array());
		}
		return $this->_layout;
	}

	protected $_disableTemplate = null;
	
	/**
	 * @param bool $flag
	 * @return void
	 */
	public function disableTemplate($flag = true) {
		$this->_disableTemplate = (bool) $flag;
	}
	
	/**
	 * @return bool
	 */
	public function isStartedLayout() {
		/**
		 * @todo Nie jest to jednoznacze że jest zainicjowany @see Zend_Layout? 
		 */
		$layoutEnabled = true;
		if (class_exists('Zend_Layout',false)) {
			$layoutEnabled = $this->getLayout()->isEnabled();
		}

		if (true === $this->_disableTemplate) {
			if ($layoutEnabled) {
				$this->getLayout()->disableLayout();
			}

			return false;
		} elseif (!$layoutEnabled) {
			return false;
		} else {
			return null !== $this->_layoutName;	
		}
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

	/**
	 * @var array
	 */
	protected $_templatePaths;

	/**
	 * @param string $path
	 * @return KontorX_Template
	 */
	public function setTemplatePath($path) {
		$this->clearTemplatePaths();
		$this->addTemplatePath($path);
		return $this;
	}

	/**
	 * @param arary $paths
	 * @return KontorX_Template
	 */
	public function setTemplatePaths(array $paths) {
		$this->clearTemplatePaths();
		foreach ($paths as $path) {
			$this->addTemplatePath($path);
		}
		return $this;
	}
	
	/**
	 * @param string $path
	 * @return KontorX_Template
	 */
	public function addTemplatePath($path) {
		$this->_templatePaths[] = rtrim((string) $path, DIRECTORY_SEPARATOR);
		return $this;
	}
	
	/**
	 * @param bool $inflect
	 * @param string $inflectorTarget
	 * @param array $sourceMerge
	 * @return array 
	 */
	public function getTemplatePaths($inflect = false, $inflectorTarget = null, array $sourceMerge = array()) {
		if (!$inflect) {
			return $this->_templatePaths;
		}

		$target = (null === $inflectorTarget)
			? $this->getTemplateTargetInflector()
			: $inflectorTarget;
		
		$source = array(
			'templateName' => $this->getTemplateName(),
			'styleName' => $this->getStyleName()
		);

		$result = array();
		$inflector = $this->getInflector();
		foreach ($this->_templatePaths as $path) {
			$source['templatePath'] = $path;
			$source = array_merge($source, $sourceMerge);
			$inflector->setTarget($target);
			$result[] = $inflector->filter($source);
		}
		return $result;
	}
	
	/**
	 * @param string $file
	 * @param string $inflectorTarget
	 * @return string 
	 */
	public function getTemplatePathToFile($file, $inflectorTarget = null) {
		if (!empty($file)) {
			foreach ($this->getTemplatePaths(true, $inflectorTarget) as $path) {
				$path .= (string) $file;
				if (is_file($path)) {
					return $path;
				}
			}
		}
		return null;
	}

	/**
	 * @return KontorX_Template
	 */
	public function clearTemplatePaths() {
		$this->_templatePaths = array();
		return $this;
	}

	/**
	 * @var string
	 */
	protected $_templateName = 'default';
	
	/** 
	 * @param string $template
	 * @return KontorX_Template
	 */
	public function setTemplateName($template) {
		$this->_templateName = basename((string) $template);
		return $this;
	}

	/**
	 * @return string
	 */
	public function getTemplateName() {
		return $this->_templateName;
	}

	/**
	 * @var string
	 */
	protected $_layoutName;
	
	/** 
	 * @param string $name
	 * @return KontorX_Template
	 */
	public function setLayoutName($name) {
		if (!$this->_lockLayoutName) {
			$this->_layoutName = basename((string) $name);
		}
		return $this;
	}

	/**
	 * @return string
	 */
	public function getLayoutName() {
		return $this->_layoutName;
	}

	/**
	 * @var bool
	 */
	protected $_lockLayoutName = false;

	/** 
	 * @param bool $flag
	 * @return KontorX_Template
	 */
	public function lockLayoutName($flag = true) {
		$this->_lockLayoutName = (bool) $flag;
		return $this;
	}
	
	/**
	 * @return bool
	 */
	public function lockedLayoutName() {
		return $this->_lockLayoutName;
	}
	
	/**
	 * @var string
	 */
	protected $_styleName = 'default';
	
	/** 
	 * @param string $template
	 * @return KontorX_Template
	 */
	public function setStyleName($style) {
		$this->_styleName = basename((string) $style);
		return $this;
	}

	/**
	 * @return string
	 */
	public function getStyleName() {
		return $this->_styleName;
	}

	/**
	 * @var string
	 */
	protected $_styleDirName = 'styles';

	/**
	 * @param string $style
	 * @return KontorX_Template
	 */
	public function setStyleDirName($style) {
		$this->_styleDirName = trim((string) $style, '/');
		return $this;
	}

	/**
	 * @return string
	 */
	public function getStyleDirName() {
		return $this->_styleDirName;
	}

	/**
	 * @var bool
	 */
	protected $_allowStyleConfig = false;
	
	/**
	 * @param bool $flag
	 * @return KontorX_Template
	 */
	public function setAllowStyleConfig($flag = true) {
		$this->_allowStyleConfig = (bool) $flag;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function isAllowedStyleConfig() {
		return $this->_allowStyleConfig;
	}
	
	/**
	 * @var string
	 */
	protected $_styleConfigFilename = 'config.ini';

	/**
	 * @param string $name
	 * @return KontorX_Template
	 */
	public function setStyleConfigFilename($name) {
		$this->_styleConfigFilename = basename((string) $name);
		return $this;
	}
	
	/**
	 * @return string
	 */
	public function getStyleConfigFilename() {
		return $this->_styleConfigFilename;
	}

	/**
	 * @return Zend_Config
	 * @throws KontorX_Template_Exception
	 */
	public function getStyleConfig() {
		if (!strlen($this->_styleConfigFilename)) {
			require_once 'KontorX/Template/Exception.php';
			throw new KontorX_Template_Exception('template style config filename is not set');
		}

		$path  = $this->getTemplatePathToFile($this->_styleConfigFilename, $this->getStyleTargetInflector());
		if (null === $path) {
			require_once 'KontorX/Template/Exception.php';
			throw new KontorX_Template_Exception(sprintf(
				'config filename "%s" do not exsists in template paths',
				$this->_styleConfigFilename));
		}

		// definiowanie GLOBAL..
		if (!defined('KX_TEMPLATE_SKIN')) {
			$skinpath = str_replace($this->getTemplatePaths(),
									null,
									dirname($path));
			define('KX_TEMPLATE_SKIN', $skinpath);
		}

		$type = strtolower(pathinfo($this->_styleConfigFilename, PATHINFO_EXTENSION));
		switch ($type) {
			case 'ini':
				require_once 'Zend/Config/Ini.php';
				$conf = new Zend_Config_Ini($path); break;
			case 'xml':
				require_once 'Zend/Config/Xml.php';
				$conf = new Zend_Config_Xml($path); break;
			case 'php':
				$conf = include $path;
				if (!is_array($conf)) {
					require_once 'KontorX/Template/Exception.php';
					throw new KontorX_Template_Exception(sprintf('config file "%s" is not array', $this->_templateConfigFilename));
				}
				require_once 'Zend/Config.php';
				$conf = new Zend_Config($conf); break;
			default:
				require_once 'KontorX/Template/Exception.php';
				throw new KontorX_Template_Exception(sprintf('undefinded config type "%s"', $type));
		}
		
		return $conf;
	}

	

	/**
	 * @var array
	 */
	protected $_findTemplates;
	
	/**
	 * @return array
	 */
	public function findTemplates() {
		if (null === $this->_findTemplates) {
			$this->_findTemplates = array();
			foreach ($this->getTemplatePaths() as $path) {
				$iterator = new DirectoryIterator($path);
				foreach ($iterator as $file) {
					/* @var $file DirectoryIterator */
					if (!$file->isDot() && $file->isDir()) {
						$filename = $file->getFilename();
						// katalogi tylko alfa-numeryczne
						if (1 == preg_match('/^[a-z0-9]+$/i', $filename)) {
							$filename = ltrim($filename, '.');
							$this->_findTemplates[$filename] = array(
								'name' => $filename
							);
						}
					}
				}
			}
		}
		return $this->_findTemplates;
	}

	/**
	 * @var array
	 */
	protected $_findStyles = array();
	
	/**
	 * @param string $template
	 * @return array
	 */
	public function findStyles($template) {
		$template = basename($template);
		if (!isset($this->_findStyles[$template])) {
			$this->_findStyles[$template] = array();
			$source = array('templateName' => $template);
			$paths = $this->getTemplatePaths(true,
							$this->getStylesTargetInflector(),
							$source);
	
			foreach ($paths as $path) {
				try {
					$iterator = new DirectoryIterator($path);
				} catch (RuntimeException $e) {
					continue;
				}

				foreach ($iterator as $file) {
					/* @var $file DirectoryIterator */
					if (!$file->isDot() && $file->isDir()) {
						$filename = $file->getFilename();
						// katalogi tylko alfa-numeryczne
						if (1 == preg_match('/^[a-z0-9]+$/i', $filename)) {
							$this->_findStyles[$template][$filename] = array(
								'name' => $filename
							);
						}
					}
				}
			}
		}
		return $this->_findStyles[$template];
	}

/**
	 * @var string
	 */
	protected $_templateTargetInflector = ':templatePath/:templateName/';
	
	/**
	 * @param string $target
	 * @return KontorX_Template
	 */
	public function setTemplateTargetInflector($target) {
		$this->_templateTargetInflector = (string) $target;
		return $this;
	}
	
	/**
	 * @return string
	 */
	public function getTemplateTargetInflector() {
		return $this->_templateTargetInflector;
	}
	
	/**
	 * @var string
	 */
	protected $_styleTargetInflector = ':templatePath/:templateName/:stylesDir/:styleName/';

	/**
	 * @param string $target
	 * @return KontorX_Template
	 */
	public function setStyleTargetInflector($target) {
		$this->_styleTargetInflector = (string) $target;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getStyleTargetInflector() {
		return $this->_styleTargetInflector;
	}
	
	/**
	 * @var string
	 */
	protected $_stylesTargetInflector = ':templatePath/:templateName/:stylesDir/';

	/**
	 * @param string $target
	 * @return KontorX_Template
	 */
	public function setStylesTargetInflector($target) {
		$this->_stylesTargetInflector = (string) $target;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getStylesTargetInflector() {
		return $this->_stylesTargetInflector;
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
			require_once 'Zend/Filter/Inflector.php';
			$this->_inflector = new Zend_Filter_Inflector();
            $this->_inflector->setThrowTargetExceptionsOn(false);
            $this->_inflector->addRules(array(
            	':templatePath' => array(),
				':templateName' => array('Word_CamelCaseToDash', 'StringToLower'),
				':styleName' => array('Word_CamelCaseToDash', 'StringToLower'),
            	':stylesDir' => array()
			))
			->setStaticRuleReference('templateName', $this->_templateName)
			->setStaticRuleReference('styleName', $this->_styleName)
			->setStaticRuleReference('stylesDir', $this->_styleDirName);
		}

		return $this->_inflector;
	}
}