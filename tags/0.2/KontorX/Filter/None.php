<?php
/**
 * @see Zend_Filter_Interface
 */
require_once 'Zend/Filter/Interface.php';

/**
 * Description of Pathname
 *
 * @author gabriel
 */
class KontorX_Filter_None implements Zend_Filter_Interface {
	 public function filter($value) {
	 	return $value;
	 }
}