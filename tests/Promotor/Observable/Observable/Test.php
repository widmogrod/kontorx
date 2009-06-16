<?php
require_once 'Promotor/Observable/Observer/Abstract.php';
class Observable_Test extends Promotor_Observable_Observer_Abstract {
	public function update(Promotor_Observable_Interface $observable) {
		var_dump(1);
		$this->_setStatus(self::SUCCESS);
		var_dump(2);
	}
}