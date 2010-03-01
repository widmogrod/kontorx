<?php
interface KontorX_DataGrid_Adapter_Interface extends Iterator, Countable {

    /**
     * Fetch data rowset ..
     * @return object|null
     */
    public function fetchData();

    /**
     * Return a raw data
     * @return mixed
     */
    public function getAdaptable();
    
    /**
     * @param KontorX_DataGrid $dataGrid
     */
    public function setDataGrid(KontorX_DataGrid $dataGrid);

    /**
     * @param string $cellsetClass
     * @return void
     */
    public function setCellsetClass($cellsetClass);

    /**
     * @return string
     */
    public function getCellsetClass();

    /**
	 * @return array
	 */
	public function toArray();
}