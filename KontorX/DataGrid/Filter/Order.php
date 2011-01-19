<?php
require_once 'KontorX/DataGrid/Filter/Abstract.php';
class KontorX_DataGrid_Filter_Order extends KontorX_DataGrid_Filter_Abstract {

    public function filter(KontorX_DataGrid_Adapter_Interface $adapter) {
        if ($adapter instanceof KontorX_DataGrid_Adapter_DbTable
        		|| $adapter instanceof KontorX_DataGrid_Adapter_DbTableTree
        		|| $adapter instanceof KontorX_DataGrid_Adapter_DbSelect)
        {
        	require_once 'KontorX/DataGrid/Filter/Order/Db.php';
            $filter = new KontorX_DataGrid_Filter_Order_Db($this->getAttribs());
        } 
        elseif ($adapter instanceof KontorX_DataGrid_Adapter_Array) 
        {
        	require_once 'KontorX/DataGrid/Filter/Order/Array.php';
        	$filter = new KontorX_DataGrid_Filter_Order_Array($this->getAttribs());
        }
        elseif ($adapter instanceof KontorX_DataGrid_Adapter_Doctrine)
        {
            require_once 'KontorX/DataGrid/Filter/Order/Doctrine.php';
            $filter = new KontorX_DataGrid_Filter_Order_Doctrine($this->getAttribs());
        } 
        else 
        {
        	require_once 'KontorX/DataGrid/Exception.php';
            throw new KontorX_DataGrid_Exception("Wrong filter adapter");
        }
        
        // przekaż parametry
        $filter->setClassName($this->getClassName());
        $filter->setName($this->getName());
        $filter->setColumn($this->getColumn());
        $filter->setColumnName($this->getColumnName());
        $filter->setValues($this->getValues());
        $filter->setAttribs($this->getAttribs());
        
        // filtruj
        $filter->filter($adapter);
    }

    public function render() {
    	// filter nic nie renderuje
        return '';
    }
}