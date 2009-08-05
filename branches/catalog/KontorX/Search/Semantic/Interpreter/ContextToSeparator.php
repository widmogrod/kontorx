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
	
	private $_separatorRequired = true;
	
	/**
	 * @param string $separator
	 * @return void
	 */
	public function __construct($separator = null) {
		if (is_string($separator)) {
			$this->_separator = $separator; 
		} elseif (is_array($separator)) {
			if (isset($separator['separatorRequired'])) {
				$this->setSeparatorRequired($separator['separatorRequired']);
			}
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
				$context->next();

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
		
		// nie znaleziono separatora
		if (!$this->_separatorRequired) {
			if ($context->valid()) {
				// pierwsze słowo
				$context->setOutput($context->current());
				// usuń je
				$context->remove();
				// przesuń kursor
				$context->next();
				return true;
			}
		}

		return false;
	}
	
	/**
	 * @param string $separator
	 * @return void
	 */
	public function setSeparator($separator) {
		$this->_separator = (string) $separator;
	}
	
	/**
	 * @param string $separatorRequired
	 * @return void
	 */
	public function setSeparatorRequired($flag = true) {
		$this->_separatorRequired = (bool) $flag;
	}
}