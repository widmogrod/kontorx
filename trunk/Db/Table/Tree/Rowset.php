<?php
require_once 'KontorX/Db/Table/Tree/Rowset/Abstract.php';
class KontorX_Db_Table_Tree_Rowset extends KontorX_Db_Table_Tree_Rowset_Abstract {
	
	public function init() {
		// jezeli tabela juz posiada odpowiednio zdefiniowane atrybuty
		// przekazywane sa do Rowset-u
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