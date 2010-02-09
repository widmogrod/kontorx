<?php
require_once 'KontorX/Search/Semantic/Interpreter/Interface.php';
abstract class KontorX_Search_Semantic_Interpreter_Abstract implements KontorX_Search_Semantic_Interpreter_Interface {

	const SEPARATOR = ' ';

	public function __toString() {
		return end(explode('_', get_class($this)));
	}
}
