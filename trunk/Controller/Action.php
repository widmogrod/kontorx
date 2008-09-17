<?php
require_once 'Zend/Controller/Action.php';

/**
 * Akcja
 * 
 * @category 	KontorX
 * @package 	KontorX_Controller_Action
 * @version 	0.1.9
 * @license		GNU GPL
 * @author 		Marcin `widmogror` Habryn, widmogrod@gmail.com
 */
abstract class KontorX_Controller_Action extends Zend_Controller_Action {

	protected $_viewScriptPathSpec = ':controller:language:action.:suffix';

	/**
	 * Inicjuje podstawową konfigurację dla akcji
	 */
	public function preDispatch() {
		$front = $this->getFrontController();

		$configFramework = Zend_Registry::get('configFramework');

		$moduleName = $this->_getParam('module');
		if(null === $moduleName) {
			$moduleName = $front->getDefaultModule();
		}

		$this->_helper->loader
			//->addFormIncludePath($moduleName)
			->addModelIncludePath($moduleName);

		// view
		$helperPath = 'KontorX/View/Helper';
		$this->view->addHelperPath($helperPath, 'KontorX_View_Helper');

//		if ($this->_helper->hasHelper('viewRenderer')) {
//			$language = $this->_getLanguage();
//			if (null === $language) {
//				// nie istnieje controller/pl/action.phtml
//				// to wywolaj default
//				$language = DIRECTORY_SEPARATOR;
//			}
//			$this->_helper->viewRenderer->setViewScriptPathSpec($this->_viewScriptPathSpec);
//		}

//		$helperPath = '../application/modules/product/views/helpers/';
//		$this->view->addHelperPath($helperPath, 'Product_View_Helper');
	}

	/**
	 * @var string
	 */
	protected $_language = null;

	/**
	 * Zwraca skrót umiędzynaradawianej watość Języka np. pl, en itd. lub wartosc domyslna
	 *
	 * @return string
	 */
	public function getLanguage() {
		return $this->_getLanguage($this->_getDefaultLanguage());
	}
	
	/**
	 * Zwraca skrót umiędzynaradawianej watość Języka np. pl, en itd. lub null
	 *
	 * @param string $default
	 * @return string
	 */
	public  function _getLanguage($default = null) {
		if (null === $this->_language) {
			$front = $this->getFrontController();
			if (null === ($i18n  = $front->getParam('i18n'))) {
				$i18n = $default;
			}
    		$this->_language = $this->_getParam('language_url', $i18n);
		}

		return $this->_language;
	}

	/**
	 * Czy domyślną wartość umiędzynarodowienia
	 * // TODO Pobranie języka z konfiguracji
	 * 
	 * @return string
	 */
	protected function _getDefaultLanguage() {
		return 'pl';
	}
	
	/**
	 * Inicjalizacja layoutu
	 * 
	 * Inicjalizacja odbywa się recznie, gdyż nie każda akcja/kontroller
	 * bedzie obsugiwala layout
	 *
	 * @param string $layoutName
	 * @param string $layoutConfigName
	 */
	protected function _initLayout($layoutName = null, $layoutConfigName = null, $layoutStyle = null, $moduleName = null) {
		$front = $this->getFrontController();
		$configFramework = Zend_Registry::get('configFramework');
		
		$moduleName = null === $moduleName ? $this->_getParam('module') : $moduleName;
		if(null === $moduleName) {
			$moduleName = $front->getDefaultModule();
		}

		// ustawianie katalogu z layoutem + wczytanie odpowiednich bibliotek
		$layoutModules = $configFramework->layout->modules->toArray();
		if (array_key_exists($moduleName, $layoutModules)) {
			$layoutPath = $layoutModules[$moduleName];
			
			// TODO Może byc przypadek ze `language_url` o `i18n` jest NULL
			$i18n = $this->_frontController->getParam('i18n');
			$layoutPath = is_dir("$layoutPath/$i18n") ? "$layoutPath/$i18n" : $layoutPath;
			
			$layoutName = (null === $layoutName)
				? $this->_helper->layout->getLayout() : $layoutName;

			$layoutConfigName = (null === $layoutConfigName)
				? $layoutName : $layoutConfigName;

			// wczytanie konfguracji layputu
			$layoutConfig = new Zend_Config_Ini($layoutPath . '/config.ini', $layoutConfigName, array('allowModifications' => true));

			// ustawianie innego katalogu layoutu
			if(null !== $layoutStyle) {
				$layoutPath = dirname($layoutPath) . '/' . $layoutStyle;
			} else
			if (null !== $layoutConfig->layoutPath) {
				$layoutPath = $layoutConfig->layoutPath;
			}
			$this->_helper->layout->setLayoutPath($layoutPath);

			// ustawianie pliku layoutu
			if (null !== $layoutConfig->layout) {
				$this->_helper->layout->direct()->setLayout($layoutConfig->layout);
			}

			// title
			if (isset($layoutConfig->title)) {
				$headTitle = $this->view->headTitle();
				$headTitle->setSeparator(' - ');
				$headTitle->set($layoutConfig->title);
			}
			
			// meta
			if (isset($layoutConfig->meta)) {
				$headMeta = $this->view->headMeta();
				$headMeta->setName('keywords', $layoutConfig->meta->name->keywords);
				$headMeta->appendName('description', $layoutConfig->meta->name->description);
			}
			// script
			if (isset($layoutConfig->script)) {
				$headScript = $this->view->headScript();
				foreach ($layoutConfig->script->js as $file) {
					// TODO Dodać sprawdzenie czy sciezka jest relatywna czy nie!
					$headScript->appendFile(WEB_DIRNAME . $file->src);
				}
			}
			// link
			$headLink = $this->view->headLink();
			if (isset($layoutConfig->links)) {
				foreach ($layoutConfig->links->css as $file) {
					// TODO Dodać sprawdzenie czy sciezka jest relatywna czy nie!
					$headLink->appendStylesheet(WEB_DIRNAME . $file->href);
				}
			}
		}
	}
}
?>