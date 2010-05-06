<?php
require_once 'KontorX/DataGrid/Filter/Abstract.php';
class KontorX_DataGrid_Filter_GroupLink extends KontorX_DataGrid_Filter_Abstract {

	protected $_group = array();
	
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

        	while($row = $stmt->fetch())
        	{
        		$_key = $row[$key];
        		$_label = $row[$label];
        		$this->_group[$_key] = $_label;
        	}
        	
        	reset($this->_group);
        	
        	// ustawienie pierwszej wartosci
        	$value = $this->getValue();
        	if ($value == '' && $this->getAttrib('forceGrouping', true))
        	{
        		// brak wartości, ustaw pierwszą z stosu!
        		$value = current($this->_group);
        	}

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
	 * @var Zend_View
	 */
	protected static $_view;
	
	/**
	 * @param Zend_View $view
	 * @return void
	 */
	public function setView(Zend_View $view) {
		$this->_view = $view;
	}

	/**
	 * @return Zend_View
	 */
	public function getView() {
		if (null === self::$_view) {
			if (Zend_Registry::isRegistered('Zend_View')) {
				self::$_view = Zend_Registry::get('Zend_View');
			} elseif(Zend_Registry::isRegistered('view')) {
				self::$_view = Zend_Registry::get('view');
			} else {
				require_once 'Zend/View.php';
				self::$_view = new Zend_View();				
			}
		}
		return self::$_view;
	}
    
	/**
     * @return string
     */
    public function render()
    {
        $column = $this->getColumnName();
        $name   = $this->getClassName();
        $class  = $this->getAttrib('class');

//    	$result = '<select name="filter[%s][%s]">';
//    	$result = sprintf($result, $column, $name);

        $href = $this->getView()->url(array());
        $href .= '?filter[%s][%s]=';
    	$href = sprintf($href,$column, $name);
        
    	$result = '<ul>';
    	
    	$value = $this->getValue();

    	foreach ($this->_group as $val => $label)
    	{
    		if ($value == $val || $value == '')
    		{
    			$result .= '<li class="kontorc_datagrid_filter_grouplink selected">';
    		} else {
    			$result .= '<li class="kontorc_datagrid_filter_grouplink">';
    		}
    		
    		$_href = $href . $val;
    		$result .= sprintf('<a href="%s"">%s</a>', $_href, $label);

    		$result .= '</li>';
    	}
    	
    	$result .= '</ul>';

        return $result;
    }
}