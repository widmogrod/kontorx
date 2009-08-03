<?php
require_once 'Zend/Validate/Abstract.php';
class KontorX_Validate_Hour extends Zend_Validate_Abstract {
	public function isValid($value) {
		return false !== preg_match('/^(\d{1,2}):(\d{1,2})([:]*)(\d{0,2})$/', $value);
	}
}