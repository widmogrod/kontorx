<?php
require_once 'Zend/View/Helper/Abstract.php';
class KontorX_View_Helper_Date extends Zend_View_Helper_Abstract {
	public function date() {
		return $this;
	}

	protected $_months = array (
		'Styczeń','Luty','Marzec','Kwiecień','Maj','Czerwiec','Lipiec','Sierpień','Wrzesień','Październik','Listopad','Grudzień'
	);

	/**
	 * Zwraca tablicę z miesiącami
	 *
	 * @return array
	 */
	public function getMonths() {
		return $this->_months;
	}

	/**
	 * Zwraca nazwę miesiąca po jego numerze
	 *
	 * @param integer $month
	 * @return string
	 */
	public function getMonthByNumber($month) {
		--$month;
		return isset($this->_months[$month])
			? $this->_months[$month]
			: false;
	}
}