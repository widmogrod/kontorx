<?php
/**
 * @author gabriel
 */
class Promotor_Application_Resource_Template extends Zend_Application_Resource_ResourceAbstract {

	public function init() {
		return $this->getTemplate();
	}

	/**
	 * @var KontorX_Template
	 */
	protected $_template;
	
	/**
	 * @return KontorX_Template
	 */
	public function getTemplate() {
		if (null === $this->_template) {
			$this->_template = KontorX_Template::getInstance($this->getOptions());
		}
		return $this->_template;
	}
}