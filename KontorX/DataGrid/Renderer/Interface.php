<?php
/**
 * @author gabriel
 *
 */
interface KontorX_DataGrid_Renderer_Interface {

	/**
	 * @param KontorX_DataGrid $dataGrid
	 * @return KontorX_DataGrid_Renderer_Interface
	 */
	public function setDataGrid(KontorX_DataGrid $dataGrid);

	/**
	 * @return string
	 */
	public function render();
}