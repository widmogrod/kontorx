<?php
require_once 'KontorX/JavaScript/Interface.php';

/**
 * @author gabriel
 *
 */
class KontorX_JavaScript implements KontorX_JavaScript_Interface {

	const JS_VAR = 'JS_VAR';
	const JS_CALL_METHOD = 'JS_CALL_METHOD';
	
	/**
	 * @var ArrayObject
	 */
	protected $_content = array();

	/**
	 * @return void
	 */
	public function __construct() {
		$this->_content = new ArrayObject(array());
	}

	/**
	 * @param string $var
	 * @param KontorX_JavaScript_Interface $object
	 * @return KontorX_JavaScript
	 */
	public function addVar($var, KontorX_JavaScript_Interface $object) {
		$this->_content->append(array(
			self::JS_VAR,
			$object,
			array(
				'var' => $var
			)
		));
		return $this;
	}

	public function callMethod($name) {
		$this->_content->append(array(
			self::JS_CALL_METHOD,
			null,
			array(
				'method' => $name
			)
		));
	}
	
	/**
	 * @return string
	 */
	public function toJavaScript() {
		$result = array();

		foreach ($this->_content as $info) {
			/* @var $js KontorX_JavaScript_Interface */
			list($type, $js, $options) = $info;

			switch ($type) {
				case self::JS_VAR:
					$result[] = sprintf('var %s = %s', $options['var'], $js->toJavaScript());
					break;
				case self::JS_CALL_METHOD:
					$result[] = $options['method'];
					break;
			}
		}

		return implode("\n", $result);
	}
}