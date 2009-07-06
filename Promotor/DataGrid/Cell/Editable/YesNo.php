<?php
class Promotor_DataGrid_Cell_Editable_YesNo extends KontorX_DataGrid_Cell_Editable_FormSelect {
	
	public function getMultiOptions() {
		// TODO Dodać translate!
		return array(1 => 'Tak', 0 => 'Nie');
	}
}