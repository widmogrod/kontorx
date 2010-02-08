<?php
/**
 * Promotor_View_Helper_PriceModifer
 * 
 * @version $Id$
 * @author $Author$
 */
class Promotor_View_Helper_PriceModifer extends Zend_View_Helper_Abstract {

	/**
	 * Generowanie czytelnego modyfikatora ceny produktu 
	 * 
	 * @param string $modifer	- modyfikator ceny
	 * @param float $value		- wartośc o jaka jest modyfikowana cena
	 * @param string $suffix 	- suffix dla modyfikatorów "-" "+" przeważnie będzie to waluta tj. "zł."
	 * @return string
	 */
	public function priceModifer($modifer = null, $value = null, $suffix = null)
	{
		switch ($modifer) {
			case '%':
				return $value . ' ' . $modifer;

			case '-':
			case '+':
				return $modifer . $value . ' ' . $suffix ;

			default:
				return '<!-- wrong price modifer "'. $modifer .'" -->';
		}
	}
}