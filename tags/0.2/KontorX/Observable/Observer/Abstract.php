<?php
/**
 * KontorX_Observable_Observer_Abstract
 * 
 * @category 	KontorX
 * @package 	KontorX_Observable
 * @version 	0.1.2
 * @license		GNU GPL
 * @author 		Marcin `widmogror` Habryn, widmogrod@gmail.com
 */
abstract class KontorX_Observable_Observer_Abstract implements  KontorX_Observable_Observer_Interface {
	protected $_acceptStatus = array();

	public function isAcceptStatus($status) {
		return $this->_checkAcceptStatus($status);
	}

	protected function _checkAcceptStatus($status) {
		// jezeli nie ma ustawionyk akceptowanych statusow,
		// to akceptuja obserwatora w przeciwnym razie sprawdzam
		// czy status jest akceptowany
		return count($this->_acceptStatus) > 0
			? in_array($status, $this->_acceptStatus)
			: true;
	}
}
?>