<?php
class KontorX_Calendar {

	/**
	 * @var integer
	 */
	private $_timestamp = null;

	public function __construct($options = null) {
		if (is_integer($options)) {
			$this->_timestamp = $options;
		} else {
			$this->_timestamp = time();
		}
	}

	private $_months = null;
	
	public function getMonths() {
		if (null === $this->_months) {
			if (!class_exists('KontorX_Calendar_Month', false)) {
				require_once 'KontorX/Calendar/Month.php';
			}

			for ($i=1; $i<13; ++$i) {
				$timestamp = mktime(1,0,0,$i,1,date('Y', $this->_timestamp));
				$this->_months[$i] = new KontorX_Calendar_Month($timestamp);
			}
		}
		return $this->_months;		
	}
}