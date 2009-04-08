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
		$this->_timestamp = $timestamp;
		$this->_pointer = $this->_startDay = (int) date('N', $this->_timestamp);
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
	 * @param KontorX_Calendar_Day $day
	 * @return bool
	 */
	public function hasDay(KontorX_Calendar_Day $day) {
		return (date('N', $this->getTimestamp()) === date('N', $day->getTimestamp()));
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
		return ($this->_pointer >= 1 && $this->_pointer <= 7);
	}

	public function rewind() {
		// przewin do pierwszego tygodnia w roku
		$this->_pointer = 1;
	}
	
	/**
	 * @return KontorX_Calendar_Day
	 */
	public function current() {
		if (!isset($this->_days[$this->_pointer])) {
			if (!class_exists('KontorX_Calendar_Day', false)) {
				require_once 'KontorX/Calendar/Day.php';
			}

			$move = ($this->_startDay - $this->_pointer);
			// określa w którą stronę przesunąć czas
			$strtime = ($move < 0) ? '+%d day' : '-%d day';
			// przesuń znacznik czasu 'n' dzień
			$timestamp = strtotime(sprintf($strtime, abs($move)), $this->getTimestamp());
			// tworzenie obiektu tygodnia
			$this->_days[$this->_pointer] = new KontorX_Calendar_Day($timestamp);
		}
		return $this->_days[$this->_pointer];
	}
}