<?php
interface KontorX_DataGrid_Adapter_Interface {

	/**
	 * Fetch data rowset ..
	 * 
	 * @return object|null
	 */
	public function fetchData();

	/**
	 * Return a raw data
	 *
	 * @return mixed
	 */
	public function getData();
	
	/**
	 * Set array of @see KontorX_DataGrid_Column_Interface objects
	 *
	 * @param array $columns
	 */
	public function setColumns(array $columns);
	
	/**
	 * Pagination on!!!
	 *
	 * @param integer $pageNumber
	 * @param integer $itemCountPerPage
	 */
	public function setPagination($pageNumber, $itemCountPerPage);
}