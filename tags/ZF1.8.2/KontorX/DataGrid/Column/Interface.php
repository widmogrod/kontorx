<?php
interface KontorX_DataGrid_Column_Interface {

	/**
     * @param string $columnName;
     */
    public function __construct($columnName);

    /**
     * Render column view
     * @return string
     */
    public function render();

    /**
     * Set column displayed name
     * @param string $name
     * @return void
     */
    public function setColumnName($name);
    
    /**
     * Get column displayed name
     * @return string
     */
    public function getColumnName();
    
    /**
     * Return class name without prefix
     * @return string
     */
    public function getClassName();

    /**
     * Set values
     * @param Zend_Config $values
     */
    public function setValues(Zend_Config $values);

    /**
     * Set filter instance @see KontorX_DataGrid_Filter_Interface
     * @param KontorX_DataGrid_Filter_Interface $filter
     */
    public function addFilter(KontorX_DataGrid_Filter_Interface $filter);

    /**
     * Return filter instance @see KontorX_DataGrid_Filter_Interface
     * @return KontorX_DataGrid_Filter_Interface
     */
    public function getFilters();

    /**
     * @param KontorX_DataGrid_Cell_Interface $filter
     */
    public function setCell(KontorX_DataGrid_Cell_Interface $cell);

    /**
     * @return KontorX_DataGrid_Cell_Interface
     */
    public function getCell();

    /**
     * @param bool $flag
     * @return void
     */
    public function setGroup($flag = true);
    
    /**
     * @return bool
     */
    public function isGroup();
}