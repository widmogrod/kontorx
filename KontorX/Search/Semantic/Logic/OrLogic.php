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
				$context->addOutput($interpreterName, $logicContext->getOutput());
				return true;
			}
			// Nie rozpoznanie kontekstu, przez interpreter
			else {
				// Kontekst, będzie interpretowany od początku! 
				$logicContext->rewind();
			}
		}
		return false;
	}
}