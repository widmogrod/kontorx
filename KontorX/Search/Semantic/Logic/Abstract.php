<?php
/**
 * @see KontorX_Search_Semantic_Logic_Interface
 */
require_once 'KontorX/Search/Semantic/Logic/Interface.php';

/**
 * @author gabriel
 */
abstract class KontorX_Search_Semantic_Logic_Abstract implements KontorX_Search_Semantic_Logic_Interface {
	/**
	 * @var array
	 */
	protected $_interpreter = array();

	/**
	 * @param KontorX_Search_Semantic_Interpreter_Interface $left
	 * @param string $name
	 * @return void
	 */
	public function addInterpreter(KontorX_Search_Semantic_Interpreter_Interface $interpreter, $name) {
		$this->_interpreter[(string)$name] = $interpreter;
	}
}