<?php
/**
 * @author gabriel
 */
class KontorX_Application_Resource_Template extends Zend_Application_Resource_ResourceAbstract {
	public function init() {
		return KontorX_Template::getInstance($this->getOptions());
	}
}