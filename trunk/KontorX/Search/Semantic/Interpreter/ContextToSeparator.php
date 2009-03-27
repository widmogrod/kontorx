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
		$storedContext = array();
		
		$cloneContext = clone $context;
		$cloneContext->clearOutput();

		while ($cloneContext->valid()) {
			$word = $cloneContext->current();

			// jest kończący separator
			if ($this->_separator == $word) {
				// aktualizuje kontekst
				foreach ($storedContext as $cloneContext) {
					$context->remove();
					$context->next();
				}
				
				// usowam też separator
				$context->remove();

				// połącz słowa
				$output = implode(KontorX_Search_Semantic_Context::WORD_SEPARATOR, $storedContext);

				// ustaw output
				$context->setOutput($output);
				
				return true;
			}
			// nie ma separatora, dodaj do listy
			else {
				$storedContext[] = $word;
			}

			$cloneContext->remove();
			$cloneContext->next();
		}
		
//		// nie znaleziono separatora
//		if ($context->valid()) {
//			// pierwsze słowo
//			$context->setOutput($context->current());
//			// usuń je
//			$context->remove();
//			// przesuń kursor
//			$context->next();
//			return true;
//		}

		return false;
	}
}