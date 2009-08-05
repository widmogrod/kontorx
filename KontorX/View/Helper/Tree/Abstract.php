<?php
require_once 'Zend/View/Helper/Abstract.php';
/**
 * KontorX_View_Helper_Tree_Abstract
 * 
 * @category 	KontorX
 * @package 	KontorX_View
 * @subpackage  Helper
 * @version 	0.1.5
 * @license		GNU GPL
 * @author 		Marcin `widmogror` Habryn, widmogrod@gmail.com
 */
abstract class KontorX_View_Helper_Tree_Abstract extends Zend_View_Helper_Abstract {
	/**
	 * Przygotowanie formatu zagniezdzenia
	 *
	 * @param KontorX_Db_Table_Tree_Row_Abstract $row
	 * @return string
	 */
	abstract protected function _data(KontorX_Db_Table_Tree_Row_Abstract $row);

	/**
	 * Renderowanie stroktory drzewiastej
	 *
	 * @param KontorX_Db_Table_Tree_Rowset_Abstract $rowset
	 * @return string
	 */
	public function tree(KontorX_Db_Table_Tree_Rowset_Abstract $rowset) {
		$result  = '<ul>';
		if (count($rowset)) {
			$result .= $this->_node($rowset);
		}
		$result .= '</ul>';
		return $result;
	}
	
	/**
	 * Renderowanie zagniezdzenia
	 *
	 * @param KontorX_Db_Table_Tree_Rowset_Abstract $rowset
	 * @return string
	 */
	protected final function _node(KontorX_Db_Table_Tree_Rowset_Abstract $rowset) {
		$result = null;
		do {
			$current 	  = $rowset->current();
			$currentDepth = $current->depth;
			// przesun wskaznik
			$rowset->next();

			$result .= '<li>' . $this->_data($current) . "\n";

			// koniec iteracji
			if (!$rowset->valid()) {
				// zamykamy tag
				$result .= '</li>' . "\n";
				// ostatnie zaglebienie jest wieksze od 1 to domykam'y
				if ($currentDepth > 1) {
					$result .= str_repeat('</ul></li>' . "\n", $currentDepth-1);
				}
				return $result;
			}

			$next 		  = $rowset->current();
			$nextDepth	  = $next->depth;

			// jest zagniezdzenie
			if ($currentDepth <> $nextDepth) {
				// domykamy tagi przed renderowaniem zagniezdzenia
				if ($currentDepth > $nextDepth) {
					// poziom zagniezdzenia nastepnego rekordu jest mniejszy od aktualnego
					$result .= str_repeat('</li></ul>' . "\n", $currentDepth-$nextDepth);
				} else {
					// TODO nie powinno byc tak ze poziom skacze nagle o dwa w dól
					// ale gdy by cos to dodac str_repeat .. ??
					$result .= '<ul>';
				}

				// renderowanie zagniezdzenia
				$result .= $this->_node($rowset);

				// domykamy tagi po renderowaniem zagniezdzenia
				if ($currentDepth > $nextDepth) {
					// poziom zagniezdzenia nastepnego rekordu jest mniejszy od aktualnego
					$result .= str_repeat('</ul></li>' . "\n", $currentDepth-$nextDepth);
				} else {
					$result .= '</ul>';
				}
			} else {
				// ten samo poziom zagniezdzenia
				$result .= '</li>';
			}

		// wykonuj iteracje dopuki poziom zagniezdzenia jest taki sam
		} while ($currentDepth === $nextDepth);

		return $result;
	}
}
