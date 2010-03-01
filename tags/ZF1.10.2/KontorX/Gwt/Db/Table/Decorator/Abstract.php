<?php
abstract class KontorX_Gwt_Db_Table_Decorator_Abstract {
	/**
	 * @var Zend_Db_Table_Abstract
	 */
	private $_table = null;

	public function __construct(Zend_Db_Table_Abstract $table) {
		$this->_table = $table;
		$this->_table->setRowClass('KontorX_Db_Table_Row_Json');
		$this->_table->setRowsetClass('KontorX_Db_Table_Rowset_Json');
	}
	
	/**
	 * @return Gallery
	 */
	private function _getTable() {
		return $this->_table;
	}

	/**
	 * @return string|null
	 * @throws GWTException
	 */
	public function findAll() {
		$table = $this->_getTable();

		try {
			$rowset = $table->fetchAll();
			return $rowset->toJson();
		} catch (Zend_Db_Exception $e) {
			throw new Exception("Cannot find all records ({$e->getMessage()})");
		}
	}

	
	/**
	 * @param integer $id
	 * @return string|null
	 * @throws Exception
	 */
	public function findById($id) {
		$data = $this->_filterData($data);

		$table = $this->_getTable();

		try {
			$select = $table->select()->where("id = ?", $id, Zend_Db::INT_TYPE);
			$row = $table->fetchRow($select);
			if ($row instanceof KontorX_Db_Table_Row_Json) {
				return $row->toJson();
			}
		} catch (Zend_Db_Exception $e) {
			throw new Exception("Cannot find record ({$e->getMessage()})");
		}
	}


	/**
	 * @param struct $data
	 * @return int|array
	 * @throws Exception
	 */
	public function insert($data) {
		$data = $this->_filterData($data);
		
		$table = $this->_getTable();
		// wszystkie klucze <> od primary!
		$data = array_diff_key($data, array_flip($table->info(Zend_Db_Table::PRIMARY)));

		try {
			$result = $table->insert($data);
			if (!is_array($result)) {
				$result = (int) $result;
			}
			return $result;
		} catch (Zend_Db_Exception $e) {
			throw new Exception("Cannot insert ({$e->getMessage()})");
		}		
	}

	/**
	 * @param integer|array $id
	 * @param struct $data
	 * @return null
	 * @throws Exception
	 */
	public function update($id, array $data) {
		$data = $this->_filterData($data);

		$table = $this->_getTable();
		// wszystkie klucze <> od primary!
		$data = array_diff_key($data, array_flip($table->info(Zend_Db_Table::PRIMARY)));

		try {
			$rowset = $table->find($id);
			foreach ($rowset as $row) {
				$row->setFromArray($data);
				$row->save();
			}
		} catch (Zend_Db_Exception $e) {
			throw new Exception("Cannot update ({$e->getMessage()})");
		}
	}

	/**
	 * @param integer|array $id
	 * @return null
	 * @throws Exception
	 */
	public function delete($id) {
		$table = $this->_getTable();

		try {
			$rowset = $table->find($id);
			foreach ($rowset as $row) {
				$row->delete();
			}
		} catch (Zend_Db_Exception $e) {
			throw new Exception("Cannot delete ({$e->getMessage()})");
		}
	}
	
	/**
	 * @param mixed $data
	 * @return mixed
	 */
	private function _filterData($data) {
		if (is_array($data)) {
			$data = array_diff($data, array(null,''));
			$data = get_magic_quotes_gpc() ? array_map('stripslashes', $data) : $data;
		}
		return $data;
	}
}