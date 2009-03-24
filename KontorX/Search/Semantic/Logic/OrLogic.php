<?php
/**
 * @see KontorX_Search_Semantic_Logic_Abstract
 */
require_once 'KontorX/Search/Semantic/Logic/Abstract.php';

/**
 * Pierwszy interpreter który rozpozna kontekst,
 * powoduje zakończenie logiki (true)
 * 
 * @author gabriel
 */
class KontorX_Search_Semantic_Logic_OrLogic extends KontorX_Search_Semantic_Logic_Abstract {
	public function interpret(KontorX_Search_Semantic_Context_Interface $context) {
		$logicContext = clone $context;
		$logicContext->clearOutput();
		foreach ($this->_interpreter as $interpreterName => $interpreterInstance) {
			// Interpreter rozpoznał kontekst
			if (true === $interpreterInstance->interpret($logicContext)) {
				// przekazanie głównemu kontektowi, zmodyfikowanego (aktualnego) input'a
				$context->setInput($logicContext->getInput());
				// przekazanie głównemu kontekstowi, output'a dla interpretatora
				$context->addOutput($interpreterName, $logicContext->getOutput());
				return true;
			}
			// Nie rozpoznanie kontekstu, przez interpreter
			else {
				/**
				 * Kontekst, będzie interpretowany od początku!
				 * XXX Pointer nie będzie w miejscu otrzymanym na starcie początkowym..
				 */  
				//$logicContext->rewind();
				
				// Teraz interpreter dostaje ten sam kontekst!
				$logicContext = clone $context;
			}
		}
		return false;
	}
}