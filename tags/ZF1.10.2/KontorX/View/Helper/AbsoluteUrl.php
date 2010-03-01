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
	
	/**
	 * @var Zend_View_Helper_Url
	 */
	protected $_url;
	
	public function setView(Zend_View_Interface $view) {
		$this->view = $view;
		$this->_url = $view->getHelper('Url');
		$this->_baseUrl = $view->getHelper('baseUrl')->getBaseUrl();
	}

	/**
	 * @param array $params
	 * @param string $router
	 * @param bool $reset
	 * @param bool $encode
	 */
	public function absoluteUrl(array $params, $router = null, $reset = false, $encode = true) {
		return $this->_baseUrl . $this->_url->url($params, $router, $reset, $encode);
	}
	
	/**
	 * @param array $params
	 * @param string $router
	 * @param bool $reset
	 * @param bool $encode
	 */
	public function direct(array $params = array(), $router = null, $reset = false, $encode = true) {
		return $this->absoluteUrl($params, $router, $reset, $encode);
	}
}