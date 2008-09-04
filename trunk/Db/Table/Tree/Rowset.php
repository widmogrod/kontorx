<?php
require_once 'KontorX/Db/Table/Tree/Rowset/Abstract.php';
class KontorX_Db_Table_Tree_Rowset extends KontorX_Db_Table_Tree_Rowset_Abstract {
	
	public function init() {
		// jezeli tabela juz posiada odpowiednio zdefiniowane atrybuty
		// przekazywane sa do Rowset-u
		$level = $this->_table->getLevel();
		if (null !== $level) {
			$this->_level = $level;
		}
		$separator = $this->_table->getSeparator();
		if (null !== $separator) {
			$this->_separator = $separator;
		}
		parent::init();
	}
}