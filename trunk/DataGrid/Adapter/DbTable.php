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

		// przygotowanie zapytania {@see Zend_Db_Select}
		if (null !== ($columns = $this->getColumns())) {
			$this->_prepareColumns($columns, $select);
		}
		if ($this->isPagination()) {
			list($pageNumber, $itemCountPerPage) = $this->getPagination();
			$select
				->limitPage($pageNumber, $itemCountPerPage);
		}

		$dataset = $table->fetchAll($select);
		$rows   = $this->getRows();
		$result = array();

		foreach ($dataset as $data) {
			$data = $rawData = $data->toArray();
			// czy są jakieś obiekty @see KontorX_DataGrid_Row_Interface
			if (count($rows)) {
				foreach ($rows as $rowName => $rowInstance) {
					$cloneRowInstance = clone $rowInstance;
					$cloneRowInstance->setData($rawData, $rowName);
					$data[$rowName] = $cloneRowInstance;
				}
			}
			$result[] = $data;
		}
		
		return $result;
	}

	/**
	 * Prepare @see Zend_Db_Select for select columns
	 *
	 * @param array $columns
	 * @param Zend_Db_Select $select
	 */
	private function _prepareColumns(array $columns, Zend_Db_Select $select) {
		$table = $this->getTable();
		// przygotowanie kolumn do wyciągnięcia
		$columns = array_intersect(
						$table->info(Zend_Db_Table::COLS), array_keys($columns));
		$select
			->from($table->info(Zend_Db_Table::NAME), $columns);
	}
}