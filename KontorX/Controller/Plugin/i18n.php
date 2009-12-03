<?php
require_once 'Zend/Controller/Plugin/Abstract.php';

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
 * @author 		Marcin `widmogror` Habryn, widmogrod@gmail.com
 */
class KontorX_Controller_Plugin_i18n extends Zend_Controller_Plugin_Abstract {

	/**
	 * Instancja Zend_Locale
	 *
	 * @var Zend_Locale
	 */
	protected $_locale = null;

	public function __construct() {
		if (!Zend_Registry::isRegistered('Zend_Locale')) {
			require_once 'Zend/Locale.php';
			try {
			    $locale = new Zend_Locale('auto');
			} catch (Zend_Locale_Exception $e) {
				// TODO umożliwić konfigurowanie parametru..
				$locale = new Zend_Locale('pl');
			}
			Zend_Registry::set('Zend_Locale', $locale);
		} else {
			$locale = Zend_Registry::get('Zend_Locale');
		}

		$this->_locale = $locale;
	}

    /**
     * Overwrite
     */
	public function routeShutdown(Zend_Controller_Request_Abstract $request) {
		$locale = @$_COOKIE['locale'];
		$language_url = $request->getParam('language_url');
		if ($language_url == '') {
			$language = ($locale == '')
				? $this->_locale->getLanguage()
				: $locale;
		} else {
			$language = ($locale == '')
				? $language_url
				: $locale;
		}
		Zend_Controller_Front::getInstance()
			->setParam('i18n', $language)
			->setParam('locale', $language);
//		$request->setParam('i18n', $language);
	}
}