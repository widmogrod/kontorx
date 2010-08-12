<?php
/**
 * Pomocnik widoku, który dba o przystosowanie numeru produktu do Naszych standardów
 *  
 * @author $Author$
 * @version $Id$
 */
class Promotor_View_Helper_ShopNumber extends Zend_View_Helper_Abstract
{
	const PREFIX = 'PRO-';

	/**
	 * @var string
	 */
	protected $_number;
	
	/**
	 * @param string $number
	 * @return Promotor_View_Helper_ShopNumber
	 */
	public function shopNumber($number) 
	{
		$this->_number = $number;
		return $this;
	}

	/**
	 * Zakoduj numer produktu
	 * @param string $number
	 * @return string
	 */
	public function encode($number) 
	{
		$number = $this->decode($number);

		$number = trim($number);
		$number = self::PREFIX . $number;

		return $number; 
	}
	
	/**
	 * Odkoduj numer produktu
	 * @param string $number
	 * @return string
	 */
	public function decode($number) 
	{
		$number = str_replace(array(self::PREFIX),'',$number);
		return $number; 
	}

	/**
	 * @return string
	 */
	public function __toString()
	{
		return $this->encode($this->_number);
	}
}