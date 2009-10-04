<?php
require_once 'KontorX/JavaScript/Interface.php';

/**
 * @author gabriel
 *
 */
class KontorX_JavaScript_Expresion implements KontorX_JavaScript_Interface {
	protected $_expresion;

	public function __construct($expresion) {
		$this->_expresion = $expresion;
	}

	public function toJavaScript() {
		return $this->_expresion;
	}
}