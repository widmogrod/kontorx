<?php
require_once 'KontorX/DataGrid/Filter/Abstract.php';
class KontorX_DataGrid_Filter_Text extends KontorX_DataGrid_Filter_Abstract {

    /**
     * Filter accept adapters
     * - KontorX_DataGrid_Adapter_DbTable
     * - KontorX_DataGrid_Adapter_DbTableTree
     * - KontorX_DataGrid_Adapter_DbSelect
     * 
     * @param KontorX_DataGrid_Adapter_Interface $adapter
     * @return void
     */
    public function filter(KontorX_DataGrid_Adapter_Interface $adapter) {
     	if (!method_exists($adapter, 'getSelect')) {
        	trigger_error(sprintf('Wrong filter adapter "%s"', get_class($adapter)), E_USER_NOTICE);
        	return;
        }

        $select = $adapter->getSelect();
        if (!$select instanceof Zend_Db_Select) {
        	require_once 'KontorX/DataGrid/Exception.php';
        	throw new KontorX_DataGrid_Exception('');
        }

        $column = $this->getColumnName();
        $text = $this->getValue();

        if (strlen($text)) {
        	$where = sprintf("%s LIKE ?", $this->_prepareColumnName($column, $select));
            $select->where($where, sprintf('%%%s%%',$text));
        }
    }

    /**
     * @param string $column
     * @param Zend_Db_Select $select
     * @return string
     */
    protected function _prepareColumnName($column, $select) {
    	if (null !== ($correlationName = $this->getAttrib('correlationName'))) {
    		return sprintf('`%s`.`%s`', $correlationName, $column);
    	}

    	foreach ($select->getPart(Zend_Db_Select::COLUMNS) as $cols) {
        	list($correlationName, $col, $alias) = $cols;
        	
        	if ($column == $alias) {
        		return sprintf('`%s`.`%s`', $correlationName, $col);
        	}
        }
        return $column;
    }
    
    /**
     * @return string
     */
    public function render() {
        $text = $this->getValue();
        $column = $this->getColumnName();
        $filter = $this->getClassName();
        $class = $this->getAttrib('class');

        $format = '<input type="text" name="filter[%s][%s]" value="%s" class="%s" />';
        return sprintf($format, $column, $filter, $text, $class);
    }
}