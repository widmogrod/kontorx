<?php
require_once 'KontorX/JavaScript/Interface.php';

/**
 * @author gabriel
 *
 */
abstract class KontorX_Ext_Abstract implements KontorX_JavaScript_Interface {

	/**
	 * @param array $options
	 * @return KontorX_DataGrid_Renderer_ExtGrid
	 */
	public function setOptions(array $options) {
		foreach ($options as $key => $value) {
			$method = 'set' . ucfirst($key);
			if (method_exists($this, $method)) {
				$this->$method($value);
			}
		}
		return $this;
	}

	/**
	 * @return string
	 */
	public function __toString() {
		return $this->toJavaScript();
	}
	
	/**
	 * @param array $options
	 * @return string
	 */
	protected function _toJavaScript(array $options, $depth = 1) {
		$result = array();
		foreach ($options as $key => $val) {
			if (is_bool($val)) {
				$val = $val ? 'true' : 'false';
			} elseif (is_string($val)) {
				if (substr($val,0,strlen('function')) != 'function') {
					$val = "'$val'";
				}
			} elseif (is_array($val)) {
				$val = $this->_toJavaScript($val, $depth + 1);
			} elseif($val instanceof KontorX_JavaScript_Interface) {
				$val = rtrim($val->toJavaScript(), ';');
			}

			$result[] = is_int($key)
				? $val
				: ($key . ':' . $val);
		}
		if (!count($options)) {
			$wrapper = "[\n" . str_repeat("\t", $depth).'%s]';
		} elseif (array_keys($options) === array_keys(array_fill(0, count($options), null))) {
			$wrapper = "[\n" . str_repeat("\t", $depth).'%s]';
		} else {
			$wrapper = "{\n" . str_repeat("\t", $depth).'%s}';
		}

		return sprintf($wrapper, implode(",\n" . str_repeat("\t", $depth),$result));
	}
}