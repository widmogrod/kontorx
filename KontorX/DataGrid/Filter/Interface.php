<?php
interface KontorX_DataGrid_Filter_Interface 
{
    /**
     * Setup adapter filtering
     * @param KontorX_DataGrid_Adapter_Interface $adapter
     */
    public function filter(KontorX_DataGrid_Adapter_Interface $adapter);

    /**
     * It's going to be rendered?
     * @return boolean
     */
    public function isRenderable();
    
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
     * @param KontorX_DataGrid_Column_Interface $column
     * @return void
     */
    public function setColumn(KontorX_DataGrid_Column_Interface $column);

    /**
     * @return KontorX_DataGrid_Column_Interface
     */
    public function getColumn();
    
    /**
     * Set column displayed name
     * @return void
     */
    public function setColumnName($name);

    /**
     * Set attribs if need to rendered
     * @param array $attribs
     * @return void
     */
    public function setAttribs(array $attribs);

    /**
     * Set values
     * @param Zend_Config $values
     */
    public function setValues(Zend_Config $values);
}