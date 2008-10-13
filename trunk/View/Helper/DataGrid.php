<?php
require_once 'Zend/View/Helper/Abstract.php';
class KontorX_View_Helper_DataGrid extends Zend_View_Helper_Abstract {
	
	public function DataGrid(KontorX_DataGrid $grid = null, $partial = null) {
		if (null === $grid) {
			if (!isset($this->view->dataGrid) ||  $this->view->dataGrid instanceof KontorX_DataGrid) {
				require_once 'Zend/View/Exception.php';
	            throw new Zend_View_Exception('No data grid instance provided nor found');
			} else {
				$grid = $this->view->dataGrid;
			}
		}

		if (null === $partial) {
			$partial = $grid->getDefaultPartial();
			if (null === $partial) {
				require_once 'Zend/View/Exception.php';
	            throw new Zend_View_Exception('No view partial provided and no default set');
			}
		}

		$vars = $grid->getVars();
		return $this->view->partial($partial, $vars);
	}
}