<?php
class Promotor_Application_Resource_Router extends Zend_Application_Resource_Router {

	protected $_globalParams = array();
	
	public function init() {
		$result = parent::init();
		$this->_initGlobalParams();
		return $result;
	}

	/**
	 * @param array $params
	 * @return void
	 */
	public function setGlobalParams(array $params) {
		$this->_globalParams = $params;
	}
	public function getGlonalParams(){
		return $this->_globalParams;
	}
	/**
	 * @return void
	 */
	protected function _initGlobalParams() {
		foreach ($this->_globalParams as $name => $value) {
			if (!is_numeric($name)) {
				$this->_router->setGlobalParam($name, $value);
			}
		}
	}
}
