<?php
require_once 'KontorX/DataGrid/Filter/Abstract.php';
class KontorX_DataGrid_Filter_Text extends KontorX_DataGrid_Filter_Abstract {

	public function filter(KontorX_DataGrid_Adapter_Interface $adapter) {
		if (!$adapter instanceof KontorX_DataGrid_Adapter_DbTable) {
			require_once 'KontorX/DataGrid/Exception.php';
			throw new KontorX_DataGrid_Exception("Wrong filter adapter");
		}
		
		$select = $adapter->getSelect();

		$name 		= $this->getName();
		$column 	= $this->getColumnName();
		$columnName = $this->getColumnName();

		$values = $this->getValues()->filter;
		$text = $values->$column->$name;

		if (strlen($text)) {
			$adapter->getSelect()
				->where("$columnName LIKE ?", "%$text%");
		}
	}

	public function render() {
		$name 		= $this->getName();
		$column 	= $this->getColumnName();
		
		$values = $this->getValues()->filter;
		$text = $values->$column->$name;

		return '<input type="text" name="filter['.$this->getColumnName().']['.$name.']" value="'.$text.'" />';
	}
}