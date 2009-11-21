<?php
/**
 * @see Zend_Filter_Interface
 */
require_once 'Zend/Filter/Interface.php';

/**
 * Word_Rewrite
 * 
 * @category 	KontorX
 * @package 	KontorX_Filter
 * @license		GNU GPL
 */
class KontorX_Filter_Word_Rewrite implements Zend_Filter_Interface {
	protected $_search   = array('á','â','ä','é','ë','í','î','ó','ô','ö','ú','ü','ý','ą','ę','ś','ź','ż','ń','ł','ć');
	protected $_replace  = array('a','a','a','e','e','i','i','o','o','o','u','u','y','a','e','s','z','z','n','l','c');

	public function filter($value, $regxpAllow = null) {
		$value = (string) $value;
		$value = str_ireplace($this->_search, $this->_replace, $value);
		$regxpAllow = preg_quote($regxpAllow, '#');
		return preg_replace("#[^\wd\-_$regxpAllow]+#iu", '-', $value);
	}
}