<?php
/**
 * @see KontorX_Search_Semantic_Logic_Abstract
 */
require_once 'KontorX/Search/Semantic/Logic/Abstract.php';

/**
 * Każdy interpreter musi byc true, by wynik poszedł do output!
 * @author gabriel
 */
class KontorX_Search_Semantic_Logic_AndLogic extends KontorX_Search_Semantic_Logic_Abstract {
	public function interpret(KontorX_Search_Semantic_Context_Interface $context) {
		$logicContext = clone $context;
		$finalContext = clone $context;
		foreach ($this->_interpreter as $interpreterName => $interpreterInstance) {
			if (true === $interpreterInstance->interpret($logicContext)) {
				$finalContext->addOutput($interpreterName, $logicContext->getOutput());
			} else {
				return false;
			}
		}
		$context->setOutput($finalContext->getOutput());
		return true;
	}
}