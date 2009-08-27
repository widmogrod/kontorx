<?php
require_once 'Promotor/Observable/Observer/Abstract.php';

class Observable_Test_Success extends Promotor_Observable_Observer_Abstract {
	public function update(Promotor_Observable_Interface $observable) {
		$this->_setStatus(self::SUCCESS);
	}
}

class Observable_Test_Failure extends Promotor_Observable_Observer_Abstract {
	public function update(Promotor_Observable_Interface $observable) {
		$this->_setStatus(self::FAILURE);
	}
}