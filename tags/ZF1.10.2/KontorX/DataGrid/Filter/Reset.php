<?php
require_once 'KontorX/DataGrid/Filter/Abstract.php';
class KontorX_DataGrid_Filter_Reset extends KontorX_DataGrid_Filter_Abstract {

    public function filter(KontorX_DataGrid_Adapter_Interface $adapter) {}
    
	/**
     * @return string
     */
    public function render() {
        $class  = $this->getAttrib('class');
        $label  = $this->getAttrib('label', 'Reset');

    	$result = '<input type="reset" value="%s" class="%s">';
    	$result = sprintf($result, $label, $class);

        return $result;
    }
}