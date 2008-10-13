<?php
require_once 'KontorX/DataGrid/Column/Abstract.php';
class KontorX_DataGrid_Column_Text extends KontorX_DataGrid_Column_Abstract {
	public function render() {
		return (string) $this->getName();
	}
}