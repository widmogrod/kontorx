<?php
require_once 'KontorX/DataGrid/Column/Abstract.php';
class KontorX_DataGrid_Column_Order extends KontorX_DataGrid_Column_Abstract {
	protected function _init() {
		require_once 'KontorX/DataGrid/Filter/Order.php';
		$filter = new KontorX_DataGrid_Filter_Order($this->getOptions());

		$this->addFilter($filter);
	}

	protected function _setupValues() {
		$name = $this->getName();
		$column = $this->getColumnName();

		$values = $this->getValues()->filter;
		if (!isset($values->$column)) {
			$values->$column = new Zend_Config(array(), true);
		}
	}

	public function render() {
		$name = $this->getName();
		$column = $this->getColumnName();

		// cloning allow to change column values for only this column! 
		$values = clone $this->getValues();
		// switching type of order..
		$orderTypeCurrent = $values->filter->$column->$name;
		switch ($orderTypeCurrent) {
			case null:
			case 'null':
				$orderTypeNext    = 'asc';
				$orderTypeCurrent = 'null';
				break;
			case 'asc':
				$orderTypeNext = 'desc';
				break;
			case 'desc':
				$orderTypeNext = 'null';
				break;
		}
		$values->filter->$column->$name = $orderTypeNext;

		$options = $this->getOptions();
		// prepare and build href
		$href = @$options['href'];
		if (substr($href, -1, 1) != '?') {
			$href .= '?';
		}
		$href .= http_build_query($values->toArray());

		$column = $this->getColumnMainName();
		return "<a class=\"column column_order\" href=\"$href\">$column <span class=\"order order-$orderTypeCurrent\">$orderTypeCurrent</span></a>";
	}
}