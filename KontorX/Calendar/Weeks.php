<?php
class KontorX_Calendar_Weeks implements Iterator, Countable {
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
	private $_startWeek = null;

	/**
	 * @var array
	 */
	private $_weeks = array();

	public function __construct($options = null) {
		if (is_integer($options)) {
			$this->_timestamp = $options;
		} else {
			$this->_timestamp = time();
		}
		
		$this->_pointer = $this->_startWeek = date('W', $this->_timestamp);
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
		return ($this->_pointer <= $this->count() || $this->_pointer >= 1);
	}

	public function rewind() {
		// przewin do pierwszego tygodnia w roku
		$this->_pointer = 1;
	}

	public function current() {
		if (!isset($this->_weeks[$this->_pointer])) {
			if (!class_exists('KontorX_Calendar_Week', false)) {
				require_once 'KontorX/Calendar/Week.php';
			}
			// przesuń znacznik czasu 'n' tydzień
			$move = ($this->_startWeek - $this->_pointer);
			// określa w którą stronę przesunąć czas
			$strtime = ($move < 0) ? '+%d week' : '-%d week';
			// przesuń znacznik czasu 'n' dzień
			$timestamp = strtotime(sprintf($strtime, abs($move)), $this->_timestamp);
			// tworzenie obiektu tygodnia
			$this->_weeks[$this->_pointer] = new KontorX_Calendar_Week($timestamp);
		}
		return $this->_weeks[$this->_pointer];
	}

	/**
	 * @var integer
	 */
	private $_count = null;
	
	public function count() {
		if (null === $this->_count) {
			// Lazy Load
			$this->_count = date('W', mktime(0,0,0,1,0,(date('Y', $this->_timestamp)+1)));
		}
		return $this->_count;
	}
}