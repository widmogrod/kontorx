<?php
/**
 * @see KontorX_Search_Semantic_Interpreter_Abstract
 */
require_once 'KontorX/Search/Semantic/Interpreter/Abstract.php';

/**
 * @author gabriel
 *
 */
class KontorX_Search_Semantic_Interpreter_ArrayKeyValueExsists extends KontorX_Search_Semantic_Interpreter_Abstract {
	
	const KEY = 'key';
	
	const VALUE = 'value';
	
	/**
	 * @var array
	 */
	private $_arrayKey = array();
	
	/**
	 * @var array
	 */
	private $_arrayValue = array();

	/**
	 * @param array $array
	 * @return void
	 */
	public function __construct(array $array) {
		foreach ($array as $data) {
			// XXX Czy wymagane jest sprawdzanie? .. napewno posypią się NOTICE..
			$this->_arrayKey[] = @$data[self::KEY];
			$this->_arrayValue[] = @$data[self::VALUE];
		}
	}

	public function interpret(KontorX_Search_Semantic_Context_Interface $context) {
		while ($context->valid()) {
			$word = $context->current();
			if (false !== ($key = array_search($word, $this->_arrayKey))) {
				$context->setOutput($this->_arrayValue[$key]);
				$context->remove();
				$context->next();
				return true;
			}
			$context->next();
		}
		return false;
	}
}