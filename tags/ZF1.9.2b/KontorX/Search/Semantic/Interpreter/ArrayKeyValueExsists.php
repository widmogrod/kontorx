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
	
	private $_multi = false;
	
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
		if (array_key_exists('multi', $array)) {
			$this->setMulti($array['multi']);
			unset($array['multi']);
		}

		foreach ($array as $data) {
			// XXX Czy wymagane jest sprawdzanie? .. napewno posypią się NOTICE..
			// @todo Exception?
			if (is_array($data)) {
				$this->_arrayKey[] = @$data[self::KEY];
				$this->_arrayValue[] = @$data[self::VALUE];
			}
		}
	}
	
	/**
	 * @param bool $flag
	 * @return void
	 */
	public function setMulti($flag = true) {
		$this->_multi = (bool) $flag;
	}

	public function interpret(KontorX_Search_Semantic_Context_Interface $context) {
		$finded = false;
		while ($context->valid()) {
			$word = $context->current();
			if (false !== ($key = array_search($word, $this->_arrayKey))) {
				$finded = true;
				
				$context->remove();

				// kolejne dopasowanie
				if ($this->_multi) {
					$context->addOutput($key, $this->_arrayValue[$key]);
				}
				// tylko jedno dopasowanie
				else {
					$context->setOutput($this->_arrayValue[$key]);
					$context->next();
					return true;
				}
			}
			$context->next();
		}
		
		return ($this->_multi && $finded) ? true : false;			
	}
}