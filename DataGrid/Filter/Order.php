<?php
require_once 'KontorX/DataGrid/Filter/Abstract.php';
class KontorX_DataGrid_Filter_Order extends KontorX_DataGrid_Filter_Abstract {

	public function filter(KontorX_DataGrid_Adapter_Interface $adapter) {
		if (!$adapter instanceof KontorX_DataGrid_Adapter_DbTable) {
			require_once 'KontorX/DataGrid/Exception.php';
			throw new KontorX_DataGrid_Exception("Wrong filter adapter");
		}

		$select = $adapter->getSelect();

		$columnName = $this->getColumnName();
		
		$order = $this->getValue($this->getName(), 'asc');
		$order = ($order == 'asc') ? 'desc' : 'asc';

		$select->order("$columnName $order");
	}

	public function render() {
		return '';
	}
}