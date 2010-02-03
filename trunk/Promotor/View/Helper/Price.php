<?php
/**
 * Promotor_View_Helper_Price
 * 
 * @version $Id$
 * @author $Author$
 */
class Promotor_View_Helper_Price extends Zend_View_Helper_Abstract {

	/**
	 * Generowanie ceny produktu 
	 * 
	 * @param float $price		- cena
	 * @param string $modifer	- modyfikator ceny
	 * @param float $value		- wartośc o jaka jest modyfikowana cena
	 * @return float
	 */
	public function price($price, $modifer = null, $value = null)
	{
		return Shop_Model_Promotion::price($price, $modifer, $value);
	}
}