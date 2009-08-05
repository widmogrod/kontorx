<?php
require_once 'KontorX/DataGrid/Column/Abstract.php';
abstract class KontorX_DataGrid_Column_ViewHelper extends KontorX_DataGrid_Column_Abstract {

	/**
	 * @var Zend_View
	 */
	protected static $_view;
	
	/**
	 * @param Zend_View $view
	 * @return void
	 */
	public function setView(Zend_View $view) {
		$this->_view = $view;
	}

	/**
	 * @return Zend_View
	 */
	public function getView() {
		if (null === self::$_view) {
			if (Zend_Registry::isRegistered('Zend_View')) {
				self::$_view = Zend_Registry::get('Zend_View');
			} elseif(Zend_Registry::isRegistered('view')) {
				self::$_view = Zend_Registry::get('view');
			} else {
				require_once 'Zend/View.php';
				self::$_view = new Zend_View();				
			}
		}
		return self::$_view;
	}
}