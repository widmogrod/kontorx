<?php
/**
 * KontorX_Controller_Plugin_i18n
 * 
 * Proste zarządzanie zmianą jezyka
 * 
 * @uses 		Zend_Controller_Plugin_Abstract
 * @category 	KontorX
 * @package 	KontorX_Controller
 * @subpackage  Plugin
 * @version 	0.1.0
 * @license		GNU GPL
 */
class KontorX_Controller_Plugin_i18n extends Zend_Controller_Plugin_Abstract {

	const DEFAULT_REGISTRY_KEY = 'Zend_Locale';

	/**
	 * Instancja Zend_Locale
	 * @var Zend_Locale
	 */
	protected $_locale = null;

	/**
	 * @return Zend_Locale
	 */
	protected function _getLocale() {
		if (null === $this->_locale) {
			if (!Zend_Registry::isRegistered(self::DEFAULT_REGISTRY_KEY)) {
				$this->_locale = new Zend_Locale('auto');
				Zend_Registry::set(self::DEFAULT_REGISTRY_KEY, $locale);
			} else {
				$this->_locale = Zend_Registry::get(self::DEFAULT_REGISTRY_KEY);
			}
		}
		return $this->_locale;
	}

	public function routeShutdown(Zend_Controller_Request_Abstract $request) {
		$locale = $request->getCookie('locale');
		$locale = $request->getParam('locale', $locale);
		if (null === $locale) {
			$locale = $this->_getLocale()->getLanguage();
		}
		Zend_Controller_Front::getInstance()->setParam('locale', $locale);
	}
}