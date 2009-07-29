<?php
require_once 'KontorX/DataGrid/Filter/Abstract.php';
class KontorX_DataGrid_Filter_Order extends KontorX_DataGrid_Filter_Abstract {

    public function filter(KontorX_DataGrid_Adapter_Interface $adapter) {
        if (!$adapter instanceof KontorX_DataGrid_Adapter_DbTable
        	&& !$adapter instanceof KontorX_DataGrid_Adapter_DbTableTree
            && !$adapter instanceof KontorX_DataGrid_Adapter_DbSelect) {
            require_once 'KontorX/DataGrid/Exception.php';
            throw new KontorX_DataGrid_Exception("Wrong filter adapter");
        }

        $select = $adapter->getSelect();
        $column = $this->getColumnName();
        $order = $this->getValue();

        if (null !== $order) {
            $order = ($order != 'asc') ? 'desc' : $order;
            $select->order(sprintf('%s %s', $column, $order));
        } else
    	if ($this->getColumn()->isGroup()) {
        	$select->order(sprintf('%s %s', $column, 'desc'));
        }
    }

    public function render() {
        return '';
    }
}