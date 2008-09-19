<?php
require_once 'Zend/Controller/Plugin/Abstract.php';
class KontorX_Controller_Plugin_System extends Zend_Controller_Plugin_Abstract {

	public function postDispatch(Zend_Controller_Request_Abstract $request) {
		// inicjujemy layout
		if ($this->isLayout()) {
			$templateName = $this->getTemplateName();
			$layoutName   = $this->getLayoutName();

			require_once 'Zend/Layout.php';
			$layout = Zend_Layout::getMvcInstance();
			if (null === $layout) {
				// TODO Dodać konfiguracje!
				$layout = Zend_Layout::startMvc();
			}

			// template name
			$templateName = (null === $templateName)
				? 'default' : $templateName;
			$layoutPath = $templateName;
			$layout->setLayoutPath($layoutPath);
			
			// layout name
			$layoutName = (null === $layoutName)
				? 'index' : $layoutName;
			$layout->setLayout($layoutName);
		}
	}

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

	protected $_templateName = null;

	public function setTemplateName($name) {
		$this->templateName = (string) $name;
	}

	public function getTemplateName() {
		return $this->templateName;
	}

	protected $_layoutName = null;
	
	public function setLayoutName($name) {
		$this->_layoutName = (string) $name;
	}

	public function getLayoutName() {
		return $this->_layoutName;
	}

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