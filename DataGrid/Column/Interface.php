<?php
interface KontorX_DataGrid_Column_Interface {
	/**
	 * Render column view
	 * 
	 * @return string
	 */
	public function render();

	/**
	 * Return class name without prefix
	 *
	 * @return string
	 */
	public function getName();

	/**
	 * Set values
	 *
	 * @param array $values
	 */
	public function setValues(array $values);
	
	/**
	 * Set filter instance @see KontorX_DataGrid_Filter_Interface
	 *
	 * @param KontorX_DataGrid_Filter_Interface $filter
	 */
	public function addFilter(KontorX_DataGrid_Filter_Interface $filter);
	
	/**
	 * Return filter instance @see KontorX_DataGrid_Filter_Interface
	 * 
	 * @return KontorX_DataGrid_Filter_Interface
	 */
	public function getFilters();

	/**
	 * Set filter instance @see KontorX_DataGrid_Row_Interface
	 *
	 * @param KontorX_DataGrid_Row_Interface $filter
	 */
	public function setRow(KontorX_DataGrid_Row_Interface $row);

	/**
	 * Return filter instance @see KontorX_DataGrid_Filter_Interface
	 * 
	 * @return KontorX_DataGrid_Row_Interface
	 */
	public function getRow();
}