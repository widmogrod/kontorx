<?php
require_once 'KontorX/Db/Table/Tree/Row/Abstract.php';
class KontorX_Db_Table_Tree_Row extends KontorX_Db_Table_Tree_Row_Abstract {

	public function init() {
		// jezeli tabela juz posiada odpowiednio zdefiniowane atrybuty
		// przekazywane sa do Row
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