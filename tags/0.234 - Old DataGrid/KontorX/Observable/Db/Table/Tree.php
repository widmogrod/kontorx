<?php
class KontorX_Observable_Db_Table_Tree extends KontorX_Observable_Db_Table {
	public function __construct(KontorX_Db_Table_Tree_Abstract $table) {
		$this->_table = $table;
	}

	public function newRoot(array $data){
		try {
			$this->_table->newRoot($data);
			$this->notify($this->_table);
		} catch (KontorX_Db_Table_Tree_Exception $e) {
			throw new KontorX_Observable_Exception($e->getMessage());
		}
	}

	public function newNode($pkParent, array $data){
		try {
			$this->_table->newNode($pkParent, $data);
			$this->notify($this->_table);
		} catch (KontorX_Db_Table_Tree_Exception $e) {
			throw new KontorX_Observable_Exception($e->getMessage());
		}
	}
}
?>