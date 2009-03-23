<?php
/**
 * @see KontorX_Search_Semantic_Interpreter_Abstract
 */
require_once 'KontorX/Search/Semantic/Interpreter/Abstract.php';

/**
 * @author gabriel
 *
 */
class KontorX_Search_Semantic_Interpreter_ArrayKeyExsists extends KontorX_Search_Semantic_Interpreter_Abstract {
	/**
	 * @var array
	 */
	private $_array = array();

	/**
	 * @param array $array
	 * @return void
	 */
	public function __construct(array $array) {
		$this->_array = $array;
	}

	public function interpret(KontorX_Search_Semantic_Context_Interface $context) {
		while ($context->valid()) {
			$word = $context->current();
			if (array_key_exists($word, $this->_array)) {
				$context->setOutput($this->_array[$word]);
				return true;
			}
			$context->next();
		}
		return false;
	}
}