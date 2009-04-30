<?php
require_once 'Zend/View/Helper/Abstract.php';
/**
 * @author gabriel
 */
class KontorX_View_Helper_Cycle extends Zend_View_Helper_Abstract {

	/**
	 * @var integer
	 */
	private static $_cycle = -1;

	/**
	 * @param array $data
	 * @return string
	 */
	public function cycle(array $data) {
		if (count($data) <= ++self::$_cycle) {
			self::$_cycle = 0;
		}
		return $data[self::$_cycle];
	}
}