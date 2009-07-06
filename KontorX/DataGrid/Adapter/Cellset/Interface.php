<?php
interface KontorX_DataGrid_Adapter_Cellset_Interface extends Iterator, Countable {
	/**
	 * @param KontorX_DataGrid_Cell_Interface $row
	 * @return void
	 */
	public function addCell($column);

	/**
	 * @param KontorX_DataGrid_Cell_Interface $cell
	 * @return void
	 */
	public function setGroupCell($cell);

	/**
	 * @return KontorX_DataGrid_Cell_Interface
	 */
	public function getGroupCell();
	
	/**
	 * @return bool
	 */
	public function hasGroupedCell();
}