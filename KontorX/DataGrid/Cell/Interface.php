<?php
interface KontorX_DataGrid_Cell_Interface {
	
    /**
     * Return a context as a html/text string
     * @return string
     */
    public function render();

    /**
     * Return class name without prefix
     * @return string
     */
    public function getClassName();
    
    /**
     * Set column displayed name
     * @return void
     */
    public function setColumnName($name);

    /**
     * Set data
     * @param mixed $data
     * @return void
     */
    public function setData($data);
    
    /**
     * @return mixed
     */
    public function getValue();
    
    /**
     * @param KontorX_DataGrid_Column_Interface $column
     * @return void
     */
    public function setColumn(KontorX_DataGrid_Column_Interface $column);

    /**
     * @return KontorX_DataGrid_Column_Interface
     */
    public function getColumn();
}