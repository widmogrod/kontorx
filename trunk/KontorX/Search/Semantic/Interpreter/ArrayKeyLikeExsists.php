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
			// @todo Exception?
			if (is_array($data)) {
				foreach ($this->_getDataKey($data) as $key) {
					$this->_arrayKey[] = $key;
					// XXX Czy wymagane jest sprawdzanie? .. napewno posypią się NOTICE..
					$this->_arrayValue[] = @$data[self::VALUE];
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
			if (false !== ($key = array_search($word, $this->_arrayKey))) {
				$finded = true;
				// kolejne dopasowanie
				if ($this->_multi) {
					$context->addOutput($this->_arrayValue[$key]);
				}
				// tylko jedno dopasowanie
				else {
					$context->setOutput($this->_arrayValue[$key]);
					$context->remove();
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