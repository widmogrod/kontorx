<?php
/**
 * @see KontorX_Search_Semantic_Interpreter_Interface
 */
require_once 'KontorX/Search/Semantic/Interpreter/Interface.php';

/**
 * @author gabriel
 */
interface KontorX_Search_Semantic_Logic_Interface extends KontorX_Search_Semantic_Interpreter_Interface {
	/**
	 * @param KontorX_Search_Semantic_Interpreter_Interface $interpreter
	 * @param string $name
	 * @return void
	 */
	public function addInterpreter(KontorX_Search_Semantic_Interpreter_Interface $interpreter, $name);
}