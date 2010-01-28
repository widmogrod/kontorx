<?php
require_once 'KontorX/DataGrid/Filter/Abstract.php';
class KontorX_DataGrid_Filter_FromToNumeric extends KontorX_DataGrid_Filter_Abstract {

	const FROM_KEY = 'from';
	const TO_KEY = 'to';
	
    public function filter(KontorX_DataGrid_Adapter_Interface $adapter) {
        if ($adapter instanceof KontorX_DataGrid_Adapter_DbTable
        		|| $adapter instanceof KontorX_DataGrid_Adapter_DbTableTree
        		|| $adapter instanceof KontorX_DataGrid_Adapter_DbSelect)
        {

        	// możliwość zdefiniowania własnej kolumny
        	$column = $this->getAttrib('column', $this->getColumnName());

			// pobieranie wartości
        	list($fromValue, $toValue) = $this->_getValues();

        	$select = $adapter->getSelect();

        	// filtrowanie!
        	if (is_numeric($fromValue) && is_numeric($toValue)) {
        		$where = sprintf('%s BETWEEN %s AND %s', $column, $fromValue, $toValue);
        		$select->where($where);
        	} else
        	if (is_numeric($fromValue)) {
        		$select->where($column . ' > ?', $fromValue);
        	} else
        	if (is_numeric($toValue)) {
        		$select->where($column . ' < ?', $toValue);
        	}

        } else {
        	require_once 'KontorX/DataGrid/Exception.php';
            throw new KontorX_DataGrid_Exception("Not implementet yet");
        }
    }

    /**
     * @return multitype:string 
     */
    protected function _getValues() {
    	$value     = $this->getValue();
    	$fromValue = isset($value->{self::FROM_KEY}) ? $value->{self::FROM_KEY} : '';
    	$toValue   = isset($value->{self::TO_KEY}) ? $value->{self::TO_KEY} : '';

    	return array($fromValue, $toValue);
    }
    
	/**
     * @return string
     */
    public function render() {
        $column = $this->getColumnName();
        $name   = $this->getClassName();
        $class  = $this->getAttrib('class');

    	// nazwy etykiet
    	$fromLabelName = $this->getAttrib('fromName', 'Od:');
    	$toLabelName   = $this->getAttrib('toName', 'Do:');

    	// wartości ..
    	list($fromValue, $toValue) = $this->_getValues();

        // from
        $result = '<div class="kx_filter_inline">';

	        $format  = '<label for="%s" />%s</label>';
	        $result .= sprintf($format, self::FROM_KEY, $fromLabelName);
	        $format  = '<input type="text" name="filter[%s][%s][%s]" value="%s" class="%s kx_data_grid_filter_from" id="%s"/>';
	        $result .= sprintf($format, $column, $name, self::FROM_KEY, $fromValue, $class, self::FROM_KEY);

        $result .= '</div>';
        

        // to
        $result .= '<div class="kx_filter_inline">';

	        $format  = '<label for="%s" />%s</label>';
	        $result .= sprintf($format, self::TO_KEY, $toLabelName);
	        $format  = '<input type="text" name="filter[%s][%s][%s]" value="%s" class="%s kx_data_grid_filter_to" id="%s"/>';
	        $result .= sprintf($format, $column, $name, self::TO_KEY, $toValue, $class, self::TO_KEY);

        $result .= '</div>';
        
        return $result;
    }
}