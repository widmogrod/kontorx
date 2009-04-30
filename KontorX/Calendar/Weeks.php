<?php
/**
 * @author gabriel
 *
 */
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
		if (is_array($options)) {
			$this->setOptions($options);
		} else
		if ($options instanceof Zend_Config) {
			$this->setOptions($options->toArray());
		} else
		if (is_integer($options)) {
			$this->setTimestamp($options);
		}
	}
	
	/**
     * @param array $options
     * @return void
     */
    public function setOptions(array $options) {
    	// bo timestamp musi być ustawiany zawsze jako pierwszy
    	if (isset($options['timestamp'])) {
    		$this->setTimestamp($options['timestamp']);
    		unset($options['timestamp']);
    	}

        foreach ($options as $name => $value) {
            $method = 'set'.ucfirst($name);
            if (method_exists($this, $method)) {
                 call_user_func_array(array($this, $method), (array) $value);
            }
        }
    }
	
	/**
	 * @param integer $timestamp
	 * @return void
	 */
	public function setTimestamp($timestamp) {
		$this->_timestamp = (int) $timestamp;
//		$this->_timestamp = ($timestamp - (60*60*24*(date('N',$timestamp)-1)));
		$this->_pointer = $this->_startWeek = (int) date('W', $timestamp);
	}
	
	/**
	 * @return integer
	 */
	public function getTimestamp() {
		if (null === $this->_timestamp) {
			$this->_timestamp = time();
		}
		return $this->_timestamp;
	}

	/**
	 * Ustaw ograniczenie tygodni tylko do 'tego' miesiąca.
	 * @param bool $flag
	 * @return void
	 */
	public function setMonthLimit($flag = true) {
		if (true === $flag) {
			// numer pierwszego tygodnia miesiąca
			$this->setMinWeek(date('W', mktime(0,0,0, date('n',$this->getTimestamp())   ,1,date('Y', $this->getTimestamp()))));
			// numer ostatniego tygodnia miesiąca
			$this->setMaxWeek(date('W', mktime(0,0,0,(date('n',$this->getTimestamp())+1),0,date('Y', $this->getTimestamp()))));
		} else {
			// reset
			$this->resetMaxWeek();
			$this->resetMinWeek();
		}
	}
	
	/**
	 * @var integer
	 */
	private $_min = null;
	
	/**
	 * @param integer $min
	 * @return void
	 */
	public function setMinWeek($min) {
		$this->_min = (int) $min;
	}
	
	/**
	 * @return integer
	 */
	public function getMinWeek() {
		if (null === $this->_min) {
			$this->_min = 1;
		}
		return $this->_min;
	}
	
	/**
	 * @return void
	 */
	public function resetMinWeek() {
		$this->_min = null;
	}
	
	/**
	 * @var integer
	 */
	private $_max = null;
	
	/**
	 * @param integer $max
	 * @return void
	 */
	public function setMaxWeek($max) {
		$this->_max = (int) $max;
	}
	
	/**
	 * @return integer
	 */
	public function getMaxWeek() {
		if (null === $this->_max) {
			// pobiera liczbę tygodni
			$this->_max = (int) date('W', mktime(0,0,0,1,0,(date('Y', $this->getTimestamp())+1)));
		}
		return $this->_max;
	}
	
	/**
	 * @return void
	 */
	public function resetMaxWeek() {
		$this->_max = null;
	}
	
	/**
	 * @param KontorX_Calendar_Week $week
	 * @return bool
	 */
	public function hasWeek(KontorX_Calendar_Week $week) {
		$timestamp = $week->getTimestamp();
		if (date('Y', $this->getTimestamp()) != date('Y', $timestamp)) {
			return false;
		}
		$weekNumber = date('W', $timestamp);
		return ($weekNumber >= $this->getMinWeek() && $weekNumber <= $this->getMaxWeek());
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
		return ($this->_pointer >= $this->getMinWeek() && $this->_pointer <= $this->getMaxWeek());
	}

	public function rewind() {
		// przewin do pierwszego tygodnia w roku
		$this->_pointer = $this->getMinWeek();
	}

	/**
	 * @return KontorX_Calendar_Week
	 */
	public function current() {
		if (!isset($this->_weeks[$this->_pointer])) {
			if (!class_exists('KontorX_Calendar_Week', false)) {
				require_once 'KontorX/Calendar/Week.php';
			}

			/**
			 * TODO Problem tkwi w tym ze przesunięcie pierwszego tygodnia o tydzień
			 * zmiania rok.. nalerzy przesunąć o liczbę dni do 1g stycznia
			 * Podobnie może być z ostatnim miesiącem..  
			 */
			
			// przesuń znacznik czasu 'n' tydzień			
			if ($this->_pointer == 1) {
				// masara! taki hack.. i nie wiem czy zawsze będzie działał
				$move = -$this->_startWeek;
				$timestamp = $this->getTimestamp() - (60*60*24*7*$move);
				$timestamp -= 60*60*24*date('N', $timestamp);
			} else {
				$move = ($this->_pointer - $this->_startWeek);
				$timestamp = strtotime(sprintf('%d week', $move),$this->getTimestamp());
			}
			// tworzenie obiektu tygodnia
			$this->_weeks[$this->_pointer] = new KontorX_Calendar_Week($timestamp);
		}
		return $this->_weeks[$this->_pointer];
	}

	public function count() {
		return $this->getMaxWeek();
	}
}