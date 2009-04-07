<?php
class KontorX_Calendar_Day {
	const MONDAY = 'monday';
	const TUESDAY = 'tuesday';
	const WEDNSDAY = 'wednsday';
	const THURSDAY = 'thursday';
	const FRIDAY = 'friday';
	const SATURDAY = 'saturday';
	const SUNDAY = 'sunday'; 

	/**
	 * @var array
	 */
	private $_dayNames = array(
		self::MONDAY => 1,
		self::TUESDAY => 2,
		self::WEDNSDAY => 3,
		self::THURSDAY => 4,
		self::FRIDAY => 5,
		self::SATURDAY => 6,
		self::SUNDAY => 7
	);
	
	/**
	 * @var integer
	 */
	private $_timestamp = null;
	
	/**
	 * @var array
	 */
	private $_days = array();

	public function __construct($options = null) {
		if (is_integer($options)) {
			$this->_timestamp = $options;
		} else {
			$this->_timestamp = time();
		}
		
		$this->_pointer = $this->_startDay = date('N', $this->_timestamp);
		
	}

	/**
	 * @return string
	 */
	public function getDayName() {
		$number = date('N', $this->_timestamp);
		if (false !== ($name = array_search($number, $this->_dayNames))) {
			return $name;
		}

		require_once 'KontorX/Calendar/Exception.php';
		throw new KontorX_Calendar_Exception(sprintf('Unknown day "%s"', $number));
	}
}