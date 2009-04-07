<?php
/**
 * @author gabriel
 */
class KontorX_Calendar_Week implements Iterator {

	/**
	 * @var integer
	 */
	private $_timestamp = null;
	
	/**
	 * @var integer
	 */
	private $_pointer = null;

	/**
	 * @var integer
	 */
	private $_startDay = null;
	
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

	public function key() {
		return $this->_pointer;
	}
	
	public function preview() {
		--$this->_pointer;
	}

	public function next() {
		++$this->_pointer;
	}

	public function valid() {
		return ($this->_pointer <= 7 || $this->_pointer >= 1);
	}

	public function rewind() {
		// przewin do pierwszego tygodnia w roku
		$this->_pointer = 1;
	}
	
	public function current() {
		if (!isset($this->_weeks[$this->_pointer])) {
			if (!class_exists('KontorX_Calendar_Day', false)) {
				require_once 'KontorX/Calendar/Day.php';
			}

			$move = ($this->_startDay - $this->_pointer);
			// określa w którą stronę przesunąć czas
			$strtime = ($move < 0) ? '+%d day' : '-%d day';
			// przesuń znacznik czasu 'n' dzień
			$timestamp = strtotime(sprintf($strtime, abs($move)), $this->_timestamp);
			// tworzenie obiektu tygodnia
			$this->_weeks[$this->_pointer] = new KontorX_Calendar_Day($timestamp);
		}
		return $this->_weeks[$this->_pointer];
	}
}