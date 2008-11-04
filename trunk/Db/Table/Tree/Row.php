<?php
require_once 'KontorX/Db/Table/Tree/Row/Abstract.php';
class KontorX_Db_Table_Tree_Row extends KontorX_Db_Table_Tree_Row_Abstract {

	public function init() {
		// jezeli tabela juz posiada odpowiednio zdefiniowane atrybuty
		// przekazywane sa do Row
		$table = $this->getTable();
		if ($table instanceof KontorX_Db_Table_Tree_Abstract) {
			$level = $table->getLevel();
			if (null !== $level) {
				$this->_level = $level;
			}
			$separator = $table->getSeparator();
			if (null !== $separator) {
				$this->_separator = $separator;
			}
		}

		parent::init();
	}
}