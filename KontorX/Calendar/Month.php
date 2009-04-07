<?php
/**
 * @author gabriel
 */
class KontorX_Calendar_Month {

	const JANUARY = 'january';
	const FEBRUARY = 'february';
	const MARCH = 'march';
	const APRIL = 'april';
	const MAY = 'may';
	const JUNE = 'june';
	const JULY = 'july';
	const AUGUST = 'august';
	const SEPTEMBER = 'september';
	const OCTOBER = 'october';
	const NOVEMBER = 'november';
	const DECEMBER = 'december';
	
	/**
	 * @var array
	 */
	private $_monthsNames = array(
		self::JANUARY => 1,
		self::FEBRUARY => 2,
		self::MARCH => 3,
		self::APRIL => 4,
		self::MAY => 5,
		self::JUNE => 6,
		self::JULY => 7,
		self::AUGUST => 8,
		self::SEPTEMBER => 9,
		self::OCTOBER => 10,
		self::NOVEMBER => 11,
		self::DECEMBER => 12
	);
	
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
	
	/**
	 * @var array of @see KontorX_Calendar_Week
	 */
	private $_weeks = null;
	
	/**
	 * @param KontorX_Calendar_Week $week
	 * @param integer $number
	 * @return void
	 */
	public function setWeek(KontorX_Calendar_Week $week, $number) {
		
	}
	
	/**
	 * @param integer $number
	 * @return KontorX_Calendar_Week
	 */
	public function getWeek($number) {
		
	}
	
	/**
	 * @return array od @see KontorX_Calendar_Week
	 */
	public function getWeeks() {
		if (null === $this->_weeks) {
			if (!class_exists('KontorX_Calendar_Week', false)) {
				require_once 'KontorX/Calendar/Week.php';
			}

			$this->_weeks = array();

			$weeks = $this->getWeeksCount();
			for($i=1; $i <= $weeks; ++$i) {
				$this->_weeks[$i] = new KontorX_Calendar_Week($this->_timestamp);
			}
		}
	}
	
	/**
	 * @return string
	 */
	public function getMonthName() {
		$number = date('n', $this->_timestamp);
		if (false !== ($name = array_search($number, $this->_monthsNames))) {
			return $name;
		}

		require_once 'KontorX/Calendar/Exception.php';
		throw new KontorX_Calendar_Exception(sprintf('Unknown month "%s"', $number));
	}

	/**
	 * Numer dnia
	 * @return integer
	 */
	public function getDay() {
		return date('j', $this->_timestamp);
	}
	
	/**
	 * Numer dnia tygodnia (0-poniedziałek....6-niedziela)
	 * @return integer
	 */
	public function getWeekDay() {
		return (date('N', $this->_timestamp)-1);
	}
	
	/**
	 * Liczba dni w miesiącu
	 * @return integer
	 */
	public function getDays() {
		return date('t', $this->_timestamp);
	}
	
	/**
	 * Numer dnia (pon[0],wt[1],śr[2],czw[3],pi[4],so[5],ni[6]) początku miesiąca
	 * @return integer
	 */
	public function getMonthStartFromDay() {
		/**
		 * @see getCurrentWeek() - gdy dzień zaczyna się od poniedziałku to + 0 a nie 1.. dlatego pomniejszam
		 */ 
		return (date('N',mktime(0,0,0,date('n', $this->_timestamp),1, date('Y', $this->_timestamp)))-1);
	}
	
	/**
	 * Aktualny numer tygodnia
	 * @return integer
	 */
	public function getCurrentWeek() {
		return ceil(($this->getDay() + $this->getMonthStartFromDay()) / 7);
	}
	
	/**
	 * Liczba tygodni w miesiącu
	 * @return integer
	 */
	public function getWeeksCount() {
		return ceil(($this->getDays() + $this->getMonthStartFromDay()) / 7);
	}
}