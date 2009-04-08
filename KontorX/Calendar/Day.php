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
	 * @return string
	 */
	public function getDayName() {
		$number = date('N', $this->getTimestamp());
		if (false !== ($name = array_search($number, $this->_dayNames))) {
			return $name;
		}

		require_once 'KontorX/Calendar/Exception.php';
		throw new KontorX_Calendar_Exception(sprintf('Unknown day "%s"', $number));
	}
	
	/**
	 * @return integer
	 */
	public function getDayNumber() {
		return date('j', $this->getTimestamp());
	}

	/**
	 * @return string
	 */
	public function render() {
		return $this->getDayNumber();
	}
	
	public function __toString() {
		return $this->render();
	}
}