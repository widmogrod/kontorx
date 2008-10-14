<?php
require_once 'KontorX/DataGrid/Filter/Abstract.php';
class KontorX_DataGrid_Filter_Text extends KontorX_DataGrid_Filter_Abstract {

	public function filter(KontorX_DataGrid_Adapter_Interface $adapter) {
		if (!$adapter instanceof KontorX_DataGrid_Adapter_DbTable) {
			require_once 'KontorX/DataGrid/Exception.php';
			throw new KontorX_DataGrid_Exception("Wrong filter adapter");
		}
		
		$this->getValues();
		$columnName = $this->getColumnName();
		$dbTable = $adapter->getData();
//		$dbTable->where()
	}

	public function render() {
		$value = $this->getValues();
		return '<input type="text" name="filter['.$this->getColumnName().']['.$this->getName().']['.$this->getColumnName().']" value="'.$value.'" />';
	}
}