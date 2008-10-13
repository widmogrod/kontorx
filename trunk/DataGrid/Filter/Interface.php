<?php
interface KontorX_DataGrid_Filter_Interface {

	/**
	 * Enter description here...
	 *
	 */
	public function filter();

	/**
	 * Render filter visualization i.e. "input text"
	 * 
	 * @return string
	 */
	public function render();
}