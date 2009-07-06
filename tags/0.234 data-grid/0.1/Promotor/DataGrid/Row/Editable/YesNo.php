<?php
require_once 'KontorX/DataGrid/Row/Editable/FormSelect.php';
class Promotor_DataGrid_Row_Editable_YesNo extends KontorX_DataGrid_Row_Editable_FormSelect {
	
	public function getMultiOptions() {
		// TODO Dodać translate!
		return array(1 => 'Tak', 0 => 'Nie');
	}
}