<?php
class Promotor_View_Helper_Site_Navigation extends Promotor_View_Helper_Site_Abstract {

	public function init() {
		return $this;
	}
	
	/**
	 * @var string
	 */
	protected $_proxy = 'menu';
	
	/**
	 * @param string $proxy
	 * @return Promotor_View_Helper_Site_Navigation
	 */
	public function setDefaultProxy($proxy) {
		$this->_proxy = (string) $proxy;
		return $this;
	}

	/**
	 * @var Zend_Navigation
	 */
	protected $_navigation;

	/**
	 * @return Zend_Navigation
	 */
	public function getNavigation() {
		if (null === $this->_navigation) {
			$model = $this->_site->getModel();
			$this->_navigation = $model->getNavigation();
		}
		return $this->_navigation;
	}

	/**
	 * @var Zend_View_Helper_Navigation_Helper
	 */
	protected $_getViewHelperNavigation;
	
	/**
	 * @param string $proxy
	 * @return Zend_View_Helper_Navigation_Helper
	 */
	public function getViewHelperNavigation($proxy = null) {
		if (null !== $proxy) {
			$this->setDefaultProxy($proxy);
		}

		$navigation = $this->getNavigation();
		/* @var $viewHelperNavigation Zend_View_Helper_Navigation */
		$viewHelperNavigation = $this->_site->view->getHelper('navigation');

		return $viewHelperNavigation->findHelper($this->_proxy)
									->setContainer($navigation);
	}
	
	/**
	 * @param string $partial
	 * @return Zend_View_Helper_Navigation_Helper  
	 */
	public function render($proxy = null) {
		return $this->getViewHelperNavigation($proxy);
	}

	/**
	 * @param string $name
	 * @param array $params
	 * @return mixed
	 */
	public function __call($name, array $params = null) {
		$helper = $this->getViewHelperNavigation();
		return call_user_func_array(array($helper, $name), $params);
	}
}