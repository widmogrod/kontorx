<?php
require_once 'KontorX/DataGrid/Filter/Abstract.php';
class KontorX_DataGrid_Filter_Order extends KontorX_DataGrid_Filter_Abstract {

    public function filter(KontorX_DataGrid_Adapter_Interface $adapter) {
        if (!$adapter instanceof KontorX_DataGrid_Adapter_DbTable
            && !$adapter instanceof KontorX_DataGrid_Adapter_DbSelect) {
            require_once 'KontorX/DataGrid/Exception.php';
            throw new KontorX_DataGrid_Exception("Wrong filter adapter");
        }

        $select = $adapter->getSelect();

        $order = $this->getValue();
        if (null !== $order) {
            $order = ($order != 'asc') ? 'desc' : $order;
            $column = $this->getColumnName();
            $select->order("$column $order");
        }
    }

    public function render() {
        return '';
    }
}