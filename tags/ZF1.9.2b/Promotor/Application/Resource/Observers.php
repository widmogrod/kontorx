<?php
class Promotor_Application_Resource_Observers extends Zend_Application_Resource_ResourceAbstract {

	public function init() {
		$options = $this->getOptions();
		Promotor_Observable_Manager::setConfig($options);
	}
}