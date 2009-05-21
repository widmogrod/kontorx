<?php
require_once 'KontorX/DataGrid/Row/Abstract.php';
class KontorX_DataGrid_Row_Text extends KontorX_DataGrid_Row_Abstract {

    /**
     * Return a context as a html/text string
     * @return string
     */
    public function render() {
        return ''.$this->getData($this->getColumnName());
    }
}