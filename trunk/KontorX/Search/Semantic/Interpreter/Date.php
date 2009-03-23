<?php
/**
 * @see Zend_Date 
 */
require_once 'Zend/Date.php';

require_once 'KontorX/Search/Semantic/Interpreter/Abstract.php';
class KontorX_Search_Semantic_Interpreter_Date extends KontorX_Search_Semantic_Interpreter_Abstract {

	public function interpret(KontorX_Search_Semantic_Context_Interface $context) {
		while ($context->valid()) {
			$word = $context->current();
			
			// moze jest liczba - traktuje jako godzinę
			if (is_numeric($word)) {
				$hour = (int) $word;
				if ($hour >= 0 && $hour <= 24) {
					$context->setOutput($word);
					return true;
				}
			} else
			if (Zend_Date::isDate($word)) {
				$context->setOutput($word);
				return true;
			} else
			// sprawdzanie formatu a'la godzina lub data
			if (false !== strstr($word,':') || false !== strstr($word,'-')) {
				$context->setOutput($word);
				return true;
			}

			$context->next();
		}
		return false;
	}
}