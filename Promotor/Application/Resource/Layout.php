<?php
class Promotor_Application_Resource_Layout extends Zend_Application_Resource_ResourceAbstract {
	/**
	 * @var Zend_View
	 */
	public $view = null;

	public function init(array $options = array()) {
		$this->getBootstrap()->bootstrap('view');
		$layout = Zend_Layout::startMvc();
	}
}