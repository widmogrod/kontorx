<?php
require_once 'KontorX/DataGrid/Column/Abstract.php';
class KontorX_DataGrid_Column_Text extends KontorX_DataGrid_Column_Abstract {
    public function render() {
        $displayNone = $this->getAttrib('displayNone');
        if (null === $displayNone || (false === (bool) $displayNone)) {
            return (string) $this->getName();
        }
        return "";
    }
}