<?php
/**
 * Kastrowanie polskich znaków diakrytycznych
 * 
 * @author $Author$
 * @version $Id$
 */
class KontorX_View_Helper_Castration extends Zend_View_Helper_Abstract
{
	protected $_search   = array('á','â','ä','é','ë','í','î','ó','ô','ö','ú','ü','ý','ą','ę','ś','ź','ż','ń','ł','ć','"', '\'');
	protected $_replace  = array('a','a','a','e','e','i','i','o','o','o','u','u','y','a','e','s','z','z','n','l','c','',  '\'');

	protected $_search2   = array('Ą','Ę','Ś','Ź','Ż','Ń','Ł','Ć','Ó');
	protected $_replace2  = array('A','R','S','Z','Z','N','L','C','O');

	protected $_value;
	
	/**
	 * @param string $value
	 * @return KontorX_View_Helper_Castration
	 */
	public function castration($value)
	{
		$this->_value = $value;
		return $this;
	}

	/**
	 * @return KontorX_View_Helper_Castration
	 */
	public function toLower()
	{
		$this->_value = strtolower($this->_value);
		return $this;
	}

	public function __toString()
	{
		$this->_value = str_replace($this->_search, $this->_replace, $this->_value);
		$this->_value = str_replace($this->_search2, $this->_replace2, $this->_value);
		return $this->_value;
	}
}