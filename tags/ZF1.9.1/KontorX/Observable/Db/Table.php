<?php
class KontorX_Observable_Db_Table extends KontorX_Observable_Abstract {
	/**
	 * @var Zend_Db_Tabel_Abstract
	 */
	protected $_table = null;

	public function __construct(Zend_Db_Table $table) {
		$this->_table = $table;
	}

	public function insert(array $data) {
		try {
			$this->_table->insert($data);
			$this->notify($this->_table);
		} catch (Zend_Db_Table_Exception $e) {
			throw new KontorX_Observable_Exception($e->getMessage());
		}
	}

	public function update(array $data, $where = null) {
		try {
			$this->_table->update($data, $where);
			$this->notify($this->_table);
		} catch (Zend_Db_Table_Exception $e) {
			throw new KontorX_Observable_Exception($e->getMessage());
		}
	}

	public function delete($where) {
		try {
			$this->_table->delete($where);
			$this->notify($this->_table);
		} catch (Zend_Db_Table_Exception $e) {
			throw new KontorX_Observable_Exception($e->getMessage());
		}
	}

	public function deleteIfExsists($parentKey) {
		try {
			$rowset = $this->_table->find($parentKey);

			if(!$rowset->count()) {
				return false;
			}

			$rowset->current()->delete();
			$this->notify($this->_table);
		} catch (Zend_Db_Table_Exception $e) {
			throw new KontorX_Observable_Exception($e->getMessage());
		}
	}
}
?>