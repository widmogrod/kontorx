<?php
require_once 'Zend/View/Helper/Interface.php';

/**
 * @author gabriel
 */
class KontorX_View_Helper_AbsoluteUrl implements Zend_View_Helper_Interface {
	
	/**
	 * @var Zend_View_Interface
	 */
	public $view;
	
	/**
	 * @var string
	 */
	protected $_baseUrl;
	
	public function setView(Zend_View_Interface $view) {
		$this->view = $view;
		$this->_baseUrl = $view->getHelper('baseUrl')->getBaseUrl();
	}
	
	/**
	 * @param array $params
	 * @param string $router
	 */
	public function absoluteUrl(array $params, $router = null) {
		return $this->_baseUrl . $this->view->url($params, $router);
	}
}