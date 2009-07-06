<?php
require_once 'Zend/View/Helper/Abstract.php';

/**
 * @author gabriel
 *
 */
class KontorX_View_Helper_UrlParams extends Zend_View_Helper_Abstract {
	/**
	 * @var array
	 */
	protected $_params = null;

	/**
	 * @return KontorX_View_Helper_UrlParams
	 */
	public function urlParams($param = null) {
		if (null === $this->_params) {
			$this->_params = Zend_Controller_Front::getInstance()->getRequest()->getParams();
		}

		if (null !== $param) {
			return $this->get($param);
		}

		return $this;
	}

	/**
	 * @param string $name
	 * @return void
	 */
	public function __isset($name) {
		return array_key_exists($name, $this->_params);
	}

	/**
	 * @param string $name
	 * @return mixed
	 */
	public function __get($name) {
		return $this->get($name);
	}
	
	/**
	 * @param string $name
	 * @param mixed $default
	 * @return mixed
	 */
	public function get($name, $default = null) {
		return array_key_exists($name, $this->_params)
			? $this->_params[$name] : $default;
	}
}