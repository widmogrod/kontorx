<?php
require_once 'KontorX/DataGrid/Row/Abstract.php';
abstract class KontorX_DataGrid_Row_ViewHelper extends KontorX_DataGrid_Row_Abstract {

	protected $_prefix = 'editable';

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