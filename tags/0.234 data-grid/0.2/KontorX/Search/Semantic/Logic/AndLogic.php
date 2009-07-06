<?php
/**
 * @see KontorX_Search_Semantic_Logic_Abstract
 */
require_once 'KontorX/Search/Semantic/Logic/Abstract.php';

/**
 * Każdy interpreter musi zinterpretować (true) kontekst,
 * by logika zakończyła się poprawnie!
 * 
 * @author gabriel
 */
class KontorX_Search_Semantic_Logic_AndLogic extends KontorX_Search_Semantic_Logic_Abstract {
	public function interpret(KontorX_Search_Semantic_Context_Interface $context) {
		$logicContext = clone $context;

		$finalContext = clone $context;
		$finalContext->clearOutput();

		foreach ($this->_interpreter as $interpreterName => $interpreterInstance) {
			if (true === $interpreterInstance->interpret($logicContext)) {
				$finalContext->addOutput($interpreterName, $logicContext->getOutput());
				$logicContext->clearOutput();
			} else {
				return false;
			}
		}
		// przekazanie głównemu kontektowi, zmodyfikowanego (aktualnego) input'a
		$context->setInput($logicContext->getInput());
		// przekazanie głównemu kontekstowi, output'a
		$context->setOutput($finalContext->getOutput());
		return true;
	}
}