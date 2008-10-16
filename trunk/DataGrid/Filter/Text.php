<?php
require_once 'KontorX/DataGrid/Filter/Abstract.php';
class KontorX_DataGrid_Filter_Text extends KontorX_DataGrid_Filter_Abstract {

	public function filter(KontorX_DataGrid_Adapter_Interface $adapter) {
		if (!$adapter instanceof KontorX_DataGrid_Adapter_DbTable) {
			require_once 'KontorX/DataGrid/Exception.php';
			throw new KontorX_DataGrid_Exception("Wrong filter adapter");
		}
		
		$text = $this->getValue($this->getName());
		$columnName = $this->getColumnName();

		if (strlen($text)) {
			$adapter->getSelect()
				->where("$columnName LIKE ?", "$text%");
		}
	}

	public function render() {
		$name = $this->getName();
		$value = $this->getValue($name);
		return '<input type="text" name="filter['.$this->getColumnName().']['.$name.']" value="'.$value.'" />';
	}
}