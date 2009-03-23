<?php
/**
 * @see KontorX_Search_Semantic_Interpreter_Abstract
 */
require_once 'KontorX/Search/Semantic/Interpreter/Abstract.php';

/**
 * @author gabriel
 */
class KontorX_Search_Semantic_Interpreter_ContextToSeparator extends KontorX_Search_Semantic_Interpreter_Abstract {
	
	/**
	 * @var string
	 */
	private $_separator = ',';
	
	/**
	 * @param string $separator
	 * @return void
	 */
	public function __construct($separator = null) {
		if (is_string($separator)) {
			$this->_separator = $separator; 
		}
	}

	public function interpret(KontorX_Search_Semantic_Context_Interface $context) {
		$currentWord = null;
		$storedContext = array();

		while ($context->valid()) {
			$word = $context->current();

			// zapisz pierwsze słowo
			if (null === $currentWord) {
				$currentWord = $word;
			}

			// jest kończący separator
			if ($this->_separator == $word) {
				// połącz słowa
				$output = implode(KontorX_Search_Semantic_Context::WORD_SEPARATOR, $storedContext);
				// TODO Dodać remove
				// $context->remove();
				$context->setOutput($output);
				return true;
			}
			// nie ma separatora, dodaj do listy
			else {
				$storedContext[] = $word;
			}

			$context->next();
		}
		
		// nie znaleziono separatora, to zwróć pierwsze słowo
		if (null !== $currentWord) {
			$context->setOutput($currentWord);
			return true;
		}

		return false;
	}
}