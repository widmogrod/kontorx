<?php
/**
 * KontorX_Util_Monitor
 * 
 * @category 	KontorX
 * @package 	KontorX_Util
 * @version 	0.1.0
 * @license		GNU GPL
 * @author 		Marcin `widmogror` Habryn, widmogrod@gmail.com
 */
class KontorX_Util_Monitor {
	/**
	 * Monitorowany obiekt
	 * 
	 * @var object
	 */
	protected $_monitored = null;

	/**
	 * Zbierane informacje o monitorowanym obiekcie
	 *
	 * @var unknown_type
	 */
	protected $_info = array();

	public function __construct($object) {
		if (!is_object($object)) {
			$error = "Argument must be a object";
			throw new Exception($error);
		}

		$this->_monitored = $object;
	}

	public function __call($name, array $arguments) {
		$exists = false;
		if (method_exists($this->_monitored, $name)) {
			$exists = true;
			$result = call_user_method_array($name, $this->_monitored, $arguments);
		} else {
			$result = "method not exsists";
		}

		$this->_info[] = array(
			'method' => $name,
			'arguments' => $arguments,
			'result' => $result
		);

		if ($exists) {
			return $result;
		}
	}

//	public function __isset($name) {}

//	public function __get($name) {}

//	public function __set($name, $value) {}
	
//	public function __toString() {}

	public function getInfo() {
		return $this->_info;
	}
}
?>