<?php
require_once 'KontorX/DataGrid/Column/Abstract.php';
class KontorX_DataGrid_Column_Order extends KontorX_DataGrid_Column_Abstract {
	public function render() {
		$column = $this->getColumnName();

		$options = $this->getOptions();
		$href = @$options['href'];

		$order = $this->getValue('order', 'asc');
		$order = ($order == 'asc') ? 'asc' : 'desc';
		
		$query = array(
			$column => array(
				'Order' => array(
					$column => $order
				)
			)
		);
		
		if (substr($href, -1, 1) != '?') {
			$href .= '?';
		}
		
		$href .= http_build_query($query);

		$column = $this->getColumnMainName();
		return "<a href=\"$href\">$column - $order</a>";
	}
}