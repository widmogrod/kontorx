<?php
interface KontorX_DataGrid_Filter_Interface {
    /**
     * Setup adapter filtering
     * @param KontorX_DataGrid_Adapter_Interface $adapter
     */
    public function filter(KontorX_DataGrid_Adapter_Interface $adapter);

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
     * Set attribs if need to rendered
     * @param array $attribs
     * @return void
     */
    public function setAttribs(array $attribs);

    /**
     * Set values
     *
     * @param Zend_Config $values
     */
    public function setValues(Zend_Config $values);
}