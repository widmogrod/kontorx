<?php
require_once 'KontorX/DataGrid/Filter/Abstract.php';
class KontorX_DataGrid_Filter_Submit extends KontorX_DataGrid_Filter_Abstract {

    public function filter(KontorX_DataGrid_Adapter_Interface $adapter) {}
    
	/**
     * @return string
     */
    public function render() {
        $class  = $this->getAttrib('class');
        $label  = $this->getAttrib('label', 'Submit');

    	$result = '<input type="submit" value="%s" class="%s">';
    	$result = sprintf($result, $label, $class);

        return $result;
    }
}