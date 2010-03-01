<?php
require_once 'Zend/View/Helper/Abstract.php';
class KontorX_View_Helper_DataGrid extends Zend_View_Helper_Abstract {

    public function dataGrid(KontorX_DataGrid $grid = null, $partial = null, $module = null) {
        if (null === $grid) {
            if (!isset($this->view->dataGrid) || $this->view->dataGrid instanceof KontorX_DataGrid) {
                require_once 'Zend/View/Exception.php';
                throw new Zend_View_Exception('No data grid instance provided nor found');
            } else {
                /* @var $grid KontorX_DataGrid */
                $grid = $this->view->dataGrid;
            }
        }

        if (null === $partial) {
        	/* @var $renderer KontorX_DataGrid_Renderer_HtmlTable */
        	$renderer = $grid->getRenderer();
        	if ($renderer instanceof KontorX_DataGrid_Renderer_Interface) {
        		$partial = $renderer->getPartial();
        	}

        	if (null === $partial) {
                require_once 'Zend/View/Exception.php';
                throw new Zend_View_Exception('No view partial provided and no default set');
            }    
        }

        $vars = $grid->getVars();
        return $this->view->getHelper('partial')->partial($partial, $module, $vars);
    }
}