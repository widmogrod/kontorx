<?php
require_once 'KontorX/DataGrid/Filter/Abstract.php';
class KontorX_DataGrid_Filter_Group extends KontorX_DataGrid_Filter_Abstract {

	protected $_group = array(
		// pierwszy element zawsze pusty!
		null => null
	);
	
    public function filter(KontorX_DataGrid_Adapter_Interface $adapter) {
        if ($adapter instanceof KontorX_DataGrid_Adapter_DbTable
        		|| $adapter instanceof KontorX_DataGrid_Adapter_DbTableTree
        		|| $adapter instanceof KontorX_DataGrid_Adapter_DbSelect)
        {

        	// możliwość zdefiniowania własnej kolumny
        	$column = $this->getAttrib('column', $this->getColumnName());

        	$select = clone $adapter->getSelect();
        	$select->order($column);
        	$select->group($column);

        	$stmt = $select->query();

        	$key   = $this->getAttrib('key', 'id');
        	$label = $this->getAttrib('label', 'name');

        	while($row = $stmt->fetch()) {
        		$_key = $row[$key];
        		$_label = $row[$label];
        		$this->_group[$_key] = $_label;
        	}
        	
        	// filtrowanie
        	$value = $this->getValue();
        	if ($value != '') {
        		$select = $adapter->getSelect();
        		$where = sprintf('%s = ?', $this->_prepareColumnName($key, $select));
        		$select->where($where, $value);
        	}

        } else {
        	require_once 'KontorX/DataGrid/Exception.php';
            throw new KontorX_DataGrid_Exception("Not implementet yet");
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
        $column = $this->getColumnName();
        $name   = $this->getClassName();
        $class  = $this->getAttrib('class');

    	$result = '<select name="filter[%s][%s]">';
    	$result = sprintf($result, $column, $name);

    	$value = $this->getValue();

    	foreach ($this->_group as $val => $label) {
    		if ($value == $val) {
    			$result .= sprintf('<option value="%s" selected="selected">%s</option>', $val, $label);
    		} else {
    			$result .= sprintf('<option value="%s">%s</option>', $val, $label);
    			
    		}
    	}
    	
    	$result .= '</select>';

        return $result;
    }
}