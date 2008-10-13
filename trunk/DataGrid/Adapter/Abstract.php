<?php
require_once 'KontorX/DataGrid/Adapter/Interface.php';

abstract class KontorX_DataGrid_Adapter_Abstract implements KontorX_DataGrid_Adapter_Interface {

	/**
	 * @var array
	 */
	private $_columns = array();
	
	/**
	 * Set array of @see KontorX_DataGrid_Column_Interface objects
	 *
	 * @param array $columns
	 */
	public function setColumns(array $columns) {
		$this->_columns = $columns;
	}
	
	/**
	 * Return columns
	 *
	 * @return array
	 */
	public function getColumns(array $columns = null) {
		return empty($this->_columns) ? $columns : $this->_columns;
	}
	
	/**
	 * @var array
	 */
	private $_filters = array();
	
	/**
	 * Set array of @see KontorX_DataGrid_Filter_Interface objects
	 *
	 * @param array $filters
	 */
	public function setFilters(array $filters) {
		$this->_filters = $filters;
	}

	/**
	 * Return filters array
	 *
	 * @return array
	 */
	public function getFilters(array $filters = null) {
		return empty($this->_filters) ? $filters : $this->_filters;
	}
	
	/**
	 * @var array
	 */
	private $_rows = array();
	
	/**
	 * Set array of @see KontorX_DataGrid_Row_Interface objects
	 *
	 * @param array $rows
	 */
	public function setRows(array $rows) {
		$this->_rows = $rows;
	}

	/**
	 * Return rows array
	 *
	 * @return array
	 */
	public function getRows(array $rows = null) {
		return empty($this->_rows) ? $rows : $this->_rows;
	}

	/**
	 * @var array
	 */
	private $_pagination = array();
	
	/**
	 * Set pagination parameters
	 *
	 * @param integer $limit
	 * @param integer $rowCount
	 */
	public function setPagination($pageNumber, $itemCountPerPage) {
		$this->_pagination = array($pageNumber, $itemCountPerPage);
	}

	/**
	 * Return pagination controls
	 *
	 * @return array
	 */
	public function getPagination() {
		return $this->_pagination;
	}
	
	/**
	 * Return true if pagination is set (has parameters) otherway false
	 *
	 * @return bool
	 */
	public function isPagination() {
		return (count($this->_pagination) == 2);
	}
}