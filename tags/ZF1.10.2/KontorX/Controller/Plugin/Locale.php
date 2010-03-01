<?php
class KontorX_Controller_Plugin_Locale extends Zend_Controller_Plugin_Abstract {

	public function routeShutdown(Zend_Controller_Request_Abstract $request) {
		$locale = $request->getCookie('locale');
		$locale = $request->getParam('locale', $locale);

		if (!strlen($locale)) {
			$locale = $this->_getLocale()->getLanguage();
		} else {
			try {
				Zend_Locale::setDefault($locale);
				$this->_getLocale()->setLocale($locale);
			} catch (Zend_Locale_Exception $e) {
				$locale = 'pl';

				Zend_Locale::setDefault($locale);
				$this->_getLocale()->setLocale($locale);
			}
		}

		// sprzężone z translacja routera ;]
		Zend_Controller_Front::getInstance()
			->getRouter()
			->setGlobalParam('locale', $locale);
	}

	/**
	 * Instancja Zend_Locale
	 * @var Zend_Locale
	 */
	protected $_locale;

	/**
	 * @return Zend_Locale
	 */
	protected function _getLocale() {
		if (null === $this->_locale) {
			if (!Zend_Registry::isRegistered(Zend_Application_Resource_Locale::DEFAULT_REGISTRY_KEY)) {
				$this->_locale = new Zend_Locale('auto');
				Zend_Registry::set(Zend_Application_Resource_Locale::DEFAULT_REGISTRY_KEY, $locale);
			} else {
				$this->_locale = Zend_Registry::get(Zend_Application_Resource_Locale::DEFAULT_REGISTRY_KEY);
			}
		}
		return $this->_locale;
	}
}