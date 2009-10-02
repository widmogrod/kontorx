<?php
class Promotor_View_Helper_Admin extends Zend_View_Helper_Abstract {

	/**
	 * @var Admin_Model_Modules
	 */
	protected static $_modules;
	
	public function __construct() {
		if (null === self::$_modules) {
			self::$_modules = Admin_Model_Modules::getInstance();
		}
	}
	
	public function admin(){
		return $this;
	}

	public function navigation() {
		self::$_modules->getNavigation();
	}
}