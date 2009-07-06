<?php
class KontorX_Observable_Db_Table_Tree_Row extends KontorX_Observable_Db_Table_Row {
	public function __construct(KontorX_Db_Table_Tree_Row_Abstract $row) {
		$this->_row = $row;
	}
}
?>