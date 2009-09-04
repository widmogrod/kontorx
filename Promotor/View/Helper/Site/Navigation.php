<?php
class Promotor_View_Helper_Site_Navigation extends Promotor_View_Helper_Site_Abstract {

	/**
	 * @var string
	 */
	protected $_proxy;
	
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
	 * @param string $partial
	 * @return string  
	 */
	public function render($proxy = null) {
		if (null !== $proxy) {
			$this->setDefaultProxy($proxy);
		}

		$navigation = $this->getNavigation();
		return $this->_site->view->getHelper('navigation')->navigation($navigation);
	}
}