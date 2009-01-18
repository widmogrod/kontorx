<?php
require_once 'Zend/Controller/Action/Helper/Abstract.php';
class KontorX_Controller_Action_Helper_System extends Zend_Controller_Action_Helper_Abstract {

    public function init() {
		$action  = $this->getActionController();
		$request = $action->getRequest();
		$plugin  = $this->getPluginInstance();

		$actionName = $request->getActionName();

		// setup template
		if (isset($action->skin)) {
			if (is_array($action->skin)) {
				$options = $action->skin;

				// sprawdz czy nazwa akcji nie jest zastrzezona
				// i czy istnieje jako oddzielna konfiguracja skórki dla akcji
				if (!in_array($actionName, array('layout', 'dynamic', 'template'))
						&& array_key_exists($actionName, $options)) {
					$options = $action->skin[$actionName];
				}

				// layout name
				if (isset($options['layout'])) {
					$plugin->setLayoutName($options['layout']);
				} else
				// dynamic layout name
				if (isset($options['dynamic'])) {
					$dynamicName = $request->getControllerName() . '_' . $request->getActionName();
					$plugin->setLayoutName($dynamicName);
				}

				// template name
				if (isset($options['template'])) {
					$plugin->setTemplateName($options['template']);
				}

				// dodatkowa konfiguracja
				if (isset($options['config']) && is_array($options['config'])) {
					$plugin->setConfig(
						array('config' => $options['config']),
						KontorX_Controller_Plugin_System::TEMPLATE);
				}
			} else
			if (is_string($action->skin)){
				// template name
				$this->getPluginInstance()->setTemplateName($action->skin);
			}
		}

		// setup cache
		if (isset($action->cache) &&
				is_array($action->cache) &&
					array_key_exists($actionName, $action->cache)) {
			// cache options
			$plugin->setCacheActionOptions($action->cache[$actionName], $request);
		}		
    }

    public function preDispatch() {
    	$action  = $this->getActionController();
    	$request = $action->getRequest();
    	$plugin  = $this->getPluginInstance();

    	// init cache for action!
		$options = $plugin->getCacheActionOptions($request, true);
		if (is_array($options)) {
			$cache   = $plugin->getCacheInstance($options);
			$cacheId = $plugin->getCacheActionId($request, $options);
			// czy widok jest w cache?
			if (!($view = $cache->load($cacheId))) {
				$plugin->setCached(false);
				$plugin->setCacheView(true);
			} else {
				$plugin->setCached(true);
				$plugin->setCacheView(false);
				// ustawiamy inforamcje żeby nie wykonywać akcji!
				// bo po co jak jest w cache!
				$request->setDispatched(false);

				$this->getResponse()->appendBody($view);
			}
		}
    }
    
    public function postDispatch() {
    	$action = $this->getActionController();
    	$request = $action->getRequest();
    	$plugin  = $this->getPluginInstance();

    	if (!$request->isDispatched()) {
    		return;
    	}

    	// setup view module/controller/action additional path!
    	
    	$view = $action->initView();

    	// setup
		$helperPath = 'KontorX/View/Helper';
		$view->addHelperPath($helperPath, 'KontorX_View_Helper');

		// poszerzenie możliwości przeszukiwania katalogów widoku akcji!
		// specyfikacja szablonów mieści się w katalogu z templatem!
		$plugin = $this->getPluginInstance();
		$templateName = $plugin->getTemplateName(true);
		// pobieramy katalogi szablonu
		list($path, $pathI18n) = $plugin->getTemplatePaths($templateName);
		// nazwa kontrollera
		$module = $request->getModuleName();
		// tworzenie scieżki
		$scriptPath = "$path/ext/$module/scripts/";

		$view->addScriptPath($scriptPath);
				// TODO narazie wielojezykowosc jest off!
//				->addScriptPath("$pathI18n/scripts");
    }
    
    /**
     * @return KontorX_Controller_Plugin_System
     */
	public function direct() {
		return $this->getPluginInstance();
	}

	/**
	 * @var KontorX_Controller_Plugin_System
	 */
	protected $_pluginInstance = null;

	/**
	 * @return KontorX_Controller_Plugin_System
	 */
	public function getPluginInstance() {
		if (null === $this->_pluginInstance) {
			$front = $this->getFrontController();
			if (!$front->hasPlugin('KontorX_Controller_Plugin_System')) {
				throw new Zend_Controller_Exception('Plugin `KontorX_Controller_Plugin_System` is no exsists');
			}
			$this->_pluginInstance = $front->getPlugin('KontorX_Controller_Plugin_System');
		}
		return $this->_pluginInstance;
	}

	/**
	 * Ustawia instancje obiektu
	 *
	 * @param KontorX_Controller_Plugin_System $plugin
	 */
	public function setPluginInstance(KontorX_Controller_Plugin_System $plugin) {
		$this->_pluginInstance = $plugin;
	}

	/**
	 * @return string
	 */
	public  function language() {
		return $this->getPluginInstance()->getLanguage();
	}

	/**
	 * @return KontorX_Controller_Action_Helper_System
	 */
    public function template($template) {
        $this->getPluginInstance()->setTemplateName($template);
        return $this;
    }

    /**
	 * @return KontorX_Controller_Action_Helper_System
	 */
    public function layout($layout) {
    	$this->getPluginInstance()->setLayoutName($layout);
    	return $this;
    }

    /**
     * Dodaje do include path sciezkę do katalogu z modelem
     *
     * @return KontorX_Controller_Action_Helper_System
     */
    public function addModelIncludePath() {
    	$action = $this->getActionController();
    	$request = $action->getRequest();
    	// 
    	$this->_addModuleIncludeDirname($request->getModuleName(), 'models');
    	
    	return $this;
    }

	/**
	 * Dodaje do "include_path" określone położenie w module!
	 *
	 * @param string $module
	 * @param string $dirname
	 */
	protected function _addModuleIncludeDirname($module, $dirname) {
		$applicationPath = $this->getPluginInstance()->getApplicationPath(
			KontorX_Controller_Plugin_System::APPLICATION_MODULES_DIRNAME);

		$path = $applicationPath . basename($module) . '/' . $dirname;

		if (is_dir($path)) {
			if (strstr(get_include_path(), $path) === false) {
				set_include_path(
					get_include_path() . PATH_SEPARATOR .
					$path
				);
			}
		}
	}
}