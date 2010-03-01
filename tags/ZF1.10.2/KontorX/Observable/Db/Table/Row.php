<?php
class KontorX_Observable_Db_Table_Row extends KontorX_Observable_Db_Table {
	/**
	 * @var Zend_Db_Table_Row_Abstract
	 */
	protected $_row = null;

	public function __construct(Zend_Db_Table_Row_Abstract $row) {
		$this->_row = $row;
	}

	public function save(){
		try {
			$this->_row->save();
			$this->notify($this->_row);
		} catch (KontorX_Db_Table_Tree_Exception $e) {
			throw new KontorX_Observable_Exception($e->getMessage());
		}
	}

	public function delete(){
		try {
			$this->_row->delete();
			$this->notify($this, $this->_row);
		} catch (KontorX_Db_Table_Tree_Exception $e) {
			throw new KontorX_Observable_Exception($e->getMessage());
		}
	}
}
?>