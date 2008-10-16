<?php
require_once 'KontorX/DataGrid/Column/Abstract.php';
class KontorX_DataGrid_Column_Order extends KontorX_DataGrid_Column_Abstract {

	public function init() {
		require_once 'KontorX/DataGrid/Filter/Order.php';
		$this->addFilter(new KontorX_DataGrid_Filter_Order($this->getOptions()));
	}

	public function render() {
		$name = $this->getName();
		$column = $this->getColumnName();

		$options = $this->getOptions();
		$href = @$options['href'];

		$order = $this->getValue($name, 'asc');
		$order = ($order == 'asc') ? 'desc' : 'asc';

		$query = array('filter' => array(
			$column => array(
				'Order' => $order
			)
		));
		
		if (substr($href, -1, 1) != '?') {
			$href .= '?';
		}
		
		$href .= http_build_query($query);

		$column = $this->getColumnMainName();
		return "<a href=\"$href\">$column - $order</a>";
	}
}