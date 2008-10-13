<?php
interface KontorX_DataGrid_Row_Interface {
	
	/**
	 * Return a context as a html/text string
	 *
	 * @return string
	 */
	public function render();
	
	/**
	 * Set data to rendered
	 *
	 * @param mixed $data
	 * @return void
	 */
	public function setData($data);

	/**
	 * Set column name
	 * 
	 * @param string $name
	 * @return void
	 */
	public function setColumnName($name);
}