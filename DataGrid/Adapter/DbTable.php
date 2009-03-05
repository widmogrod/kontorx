<?php
require_once 'KontorX/DataGrid/Adapter/Abstract.php';

class KontorX_DataGrid_Adapter_DbTable extends KontorX_DataGrid_Adapter_Abstract {

	/**
	 * Konstruktor
	 *
	 * @param Zend_Db_Table_Abstract $table
	 */
	public function __construct(Zend_Db_Table_Abstract $table) {
		$this->_table = $table;
	}
	
	/**
	 * @var Zend_Db_Table_Abstract
	 */
	protected $_table = null;

	/**
	 * Zwraca @see Zend_Db_Table_Abstract
	 * 
	 * @return Zend_Db_Table_Abstract
	 */
	public function getTable() {
		return $this->_table;
	}

	/**
	 * @var Zend_Db_Select
	 */
	private $_select = null;
	
	/**
	 * Return select statment mini. singletone
	 *
	 * @return Zend_Db_Select
	 */
	public function getSelect() {
		if (null === $this->_select) {
			$this->_select = $this->getTable()->select();
		}
		return $this->_select;
	}
	
	/**
	 * Wyławia szukane kolumny spełniające warunek ..
	 *
	 * @param array $columns
	 * @param array $filters
	 * @return array
	 */
	public function fetchData() {
		$table = $this->getTable();
		$select = $this->getSelect();

		// czy jest paginacja
		if ($this->isPagination()) {
			list($pageNumber, $itemCountPerPage) = $this->getPagination();
			$select
				->limitPage($pageNumber, $itemCountPerPage);
		}

		$dataset = $table->fetchAll($select);
		$columns = $this->getColumns();
		$rows   = $this->getRows();

		$result = array();
		foreach ($dataset as $i => $data) {
			$rawData = $data->toArray();
			// tworzymy tablice wielowymiarowa rekordow 
			foreach ($columns as $columnName => $columnInstance) {
				// jest dekorator rekordu @see KontorX_DataGrid_Row_Interface
				if (count($rows)
						&& isset($rows[$columnName])
							&& $rows[$columnName] instanceof KontorX_DataGrid_Row_Interface) {
					$cloneRowInstance = clone $rows[$columnName];
					$cloneRowInstance->setData($rawData, $columnName);
					$result[$i][$columnName] = $cloneRowInstance;
				}
				// surowy rekord!
				else {
					$result[$i][$columnName] = isset($rawData[$columnName])
						? $rawData[$columnName] : null;
				}
			}
		}
		return $result;
	}
}