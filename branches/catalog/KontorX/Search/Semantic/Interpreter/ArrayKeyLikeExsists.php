<?php
/**
 * @see KontorX_Search_Semantic_Interpreter_Abstract
 */
require_once 'KontorX/Search/Semantic/Interpreter/Abstract.php';

/**
 * @author gabriel
 *
 */
class KontorX_Search_Semantic_Interpreter_ArrayKeyLikeExsists extends KontorX_Search_Semantic_Interpreter_Abstract {
	
	const KEY = 'key';
	
	const VALUE = 'value';
	
	private $_multi = false;
	
	/**
	 * @var array
	 */
	private $_arrayKey = array();
	
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
			// @todo Exception?
			if (is_array($data)) {
				foreach ($this->_getDataKey($data) as $key) {
					if (!array_key_exists($key, $this->_arrayKey)) {
						$this->_arrayKey[$key] = array();
					}
					// XXX Czy wymagane jest sprawdzanie? .. napewno posypią się NOTICE..
					$this->_arrayKey[$key][] = @$data[self::VALUE];
				}
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
			$word = $this->_getWord($context);
			if (array_key_exists($word, $this->_arrayKey)) {
				$finded = true;
				
				$context->remove();
				
				// kolejne dopasowanie
				if ($this->_multi) {
					// każda wartośc z osobana jest dodawana
					array_map(array($context,'addOutput'), $this->_arrayKey[$word]);
				}
				// tylko jedno dopasowanie
				else {
					// Dla zgodności wstecz
					$value = (count($this->_arrayKey[$word]) > 1)
						? $this->_arrayKey[$word]
						: current($this->_arrayKey[$word]);

					$context->setOutput($value);
					$context->next();
					return true;
				}
			}
			$context->next();
		}
		
		return ($this->_multi && $finded) ? true : false;			
	}
	
	/**
	 * @param array $data
	 * @return mixed
	 */
	protected function _getDataKey(array $data) {
		if (!array_key_exists(self::KEY, $data)) {
			return array();
		}

		$key = $data[self::KEY];
		$key = $this->_sanitize($key);
		$key = explode(' ', $key);
		return (array) array_filter($key);
	}
	
	/**
	 * @param KontorX_Search_Semantic_Context_Interface $context
	 * @return string
	 */
	protected function _getWord(KontorX_Search_Semantic_Context_Interface $context) {
		return $this->_sanitize($context->current());
	}
	
	/**
	 * @var KontorX_Filter_Word_Rewrite
	 */
	private $_sanitize = null;
	
	/**
	 * @param string $string
	 * @return string
	 */
	protected function _sanitize($string) {
		if (null === $this->_sanitize) {
			require_once 'KontorX/Filter/Word/Rewrite.php';
			$this->_sanitize = new KontorX_Filter_Word_Rewrite();
		}
		
		return $this->_sanitize->filter((string) $string, ' ');
	}
}