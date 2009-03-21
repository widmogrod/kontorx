<?php
interface KontorX_DataGrid_Row_Interface {
	
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
     * Set data
     * @param mixed $data
     * @return void
     */
    public function setData($data);
}