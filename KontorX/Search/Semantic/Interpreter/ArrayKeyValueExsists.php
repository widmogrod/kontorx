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
	private $_array = array();

	/**
	 * @param array $array
	 * @return void
	 */
	public function __construct(array $array) {
		foreach ($array as $data) {
			// XXX Czy wymagane jest sprawdzanie? .. napewno posypią się NOTICE..
			$this->_array[(string)$data[self::KEY]] = $data[self::VALUE];
		}
		
		var_dump($this->_array);
	}

	public function interpret(KontorX_Search_Semantic_Context_Interface $context) {
		while ($context->valid()) {
			$word = $context->current();
			if (array_key_exists($word, $this->_array)) {
				$context->setOutput($this->_array[$word]);
				$context->remove();
				$context->next();
				return true;
			}
			$context->next();
		}
		return false;
	}
}