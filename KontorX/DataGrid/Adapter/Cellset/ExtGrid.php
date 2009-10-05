<?php
require_once 'KontorX/DataGrid/Adapter/Cellset/Abstract.php';
class KontorX_DataGrid_Adapter_Cellset_ExtGrid extends KontorX_DataGrid_Adapter_Cellset_Abstract {
	public function toArray() {
		$result = array();
		foreach ($this as $i => $cell) {
			/* @var $cell KontorX_DataGrid_Cell_Interface */			
			$name = $cell->getColumn()->getColumnName();
			$type = $cell->getClassName();
			switch(strtolower($type)) {
				case 'url':
					$result[$name] = (string) $cell->render();
					break;

				default:
					 $result[$name] = $cell->getValue();
			}			
		}
		return $result;
	}
}