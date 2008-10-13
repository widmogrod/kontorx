<?php
interface KontorX_DataGrid_Adapter_Interface {

	/**
	 * Fetch data rowset ..
	 * 
	 * @return object|null
	 */
	public function fetchData(array $columns = null, array $filters = null);
	
	/**
	 * Set array of @see KontorX_DataGrid_Column_Interface objects
	 *
	 * @param array $columns
	 */
	public function setColumns(array $columns);
	
	/**
	 * Set array of @see KontorX_DataGrid_Filter_Interface objects
	 *
	 * @param array $filters
	 */
	public function setFilters(array $filters);
	
	/**
	 * Set array of @see KontorX_DataGrid_Row_Interface objects
	 *
	 * @param array $rows
	 */
	public function setRows(array $rows);

	/**
	 * Pagination on!!!
	 *
	 * @param integer $pageNumber
	 * @param integer $itemCountPerPage
	 */
	public function setPagination($pageNumber, $itemCountPerPage);
}