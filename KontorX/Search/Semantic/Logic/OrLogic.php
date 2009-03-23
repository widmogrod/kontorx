<?php
/**
 * @see KontorX_Search_Semantic_Logic_Abstract
 */
require_once 'KontorX/Search/Semantic/Logic/Abstract.php';

/**
 * Wynik pierwszego poprawnego interpretera, idzie do output!
 * @author gabriel
 */
class KontorX_Search_Semantic_Logic_OrLogic extends KontorX_Search_Semantic_Logic_Abstract {
	public function interpret(KontorX_Search_Semantic_Context_Interface $context) {
		$logicContext = clone $context; 
		foreach ($this->_interpreter as $interpreterName => $interpreterInstance) {
			// co najmniej jedno jest ok
			if (true === $interpreterInstance->interpret($logicContext)) {
				$context->addOutput($interpreterName, $logicContext->getOutput());
				return true;
			}
		}
		return false;
	}
}