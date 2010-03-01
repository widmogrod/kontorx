<?php
/**
 * Abstract class for JavaScript view helpers..
 * @author gabriel
 *
 */
class KontorX_View_Helper_JsAbstract extends Zend_View_Helper_Abstract {

	/**
	 * @var array
	 */
	protected $_jsOptions = array();

	/**
	 * @var array
	 */
	protected $_jsOptionsSchema = array();

	/**
	 * @var arary
	 */
	protected $_jsDefaultOptions = array(); 

	/**
	 * @param array $options
	 * @return KontorX_View_Helper_JsAbstract
	 */
	public function setJsOptions(array $options) {
		$this->_jsOptions = $options;
		return $this;
	}
	
	/**
	 * @param string $key 
	 * @param string $value
	 * @return KontorX_View_Helper_JsAbstract
	 */
	public function setJsOption($key, $value) {
		$this->_jsOptions[$key] = $value;
		return $this;
	}

	/**
	 * @param array $array1
	 * @param array $array2
	 * @return array
	 */
	private function array_intersec_key_recursive($array1, $array2) {
		$result = array();
		foreach ($array1 as $key => $value) {
			if (array_key_exists($key, $array2)) {
				if (is_array($value)) {
					if (is_array($array2[$key])) {
						$result[$key] = $this->array_intersec_key_recursive($value, $array2[$key]);
						continue;
					}
				}
				$result[$key] = $array2[$key];
			}
		}
		return $result;
	}
	
	/**
	 * @param array $options
	 * @return string
	 */
	private function _dataToJSON(array $options) {
		$result = array();
		foreach ($options as $key => $val) {
			if (is_bool($val)) {
				$val = $val ? 'true' : 'false';
			} else
			if (is_string($val)) {
				if (substr($val,0,strlen('function')) != 'function') {
					$val = "'$val'";
				}
			} else
			if (is_array($val)) {
				$val = $this->_dataToJSON($val);
			}

			$result[] = is_int($key)
				? $val
				: ($key . ':' . $val);
		}
		if (!count($options)) {
			$wrapper = '[%s]';
		} elseif (array_keys($options) === array_keys(array_fill(0, count($options), null))) {
			$wrapper = '[%s]';
		} else {
			$wrapper = '{%s}';
		}
		return sprintf($wrapper, implode(',',$result));
	}
	
	/**
	 * @return string as JSON
	 */
	protected function _getJsOptions() {
		// only setings keys
		$options = $this->array_intersec_key_recursive($this->_jsOptionsSchema, $this->_jsOptions);
		// set default
		$options += $this->_jsDefaultOptions;

		return $this->_dataToJSON($options);
	}
}