<?php
require_once 'KontorX/DataGrid/Cell/Abstract.php';
class KontorX_DataGrid_Cell_Text extends KontorX_DataGrid_Cell_Abstract {

    /**
     * Return a context as a html/text string
     * @return string
     */
    public function render() {
    	$name = $this->getAttrib('mappedColumn', $this->getColumnName());
        return '' . $this->getData($name);
    }
}