<?php
require_once 'KontorX/DataGrid/Filter/Abstract.php';

/**
 * @author g.habryn (widmogrod@gmail.com)
 */
class KontorX_DataGrid_Filter_Order_Doctrine extends KontorX_DataGrid_Filter_Abstract 
{
    public function filter(KontorX_DataGrid_Adapter_Interface $adapter) 
    {
        if (!$adapter instanceof KontorX_DataGrid_Adapter_Doctrine)
		{
            require_once 'KontorX/DataGrid/Exception.php';
            throw new KontorX_DataGrid_Exception("Wrong filter adapter");
        }

        /* @var $adapter KontorX_DataGrid_Adapter_Doctrine */
        $query = $adapter->getQuery();
        $column = $this->getColumnName();

        // Doctrine adapter is hydrated as HYDRATE_SCALAR so.. first '_' replaced to '.' will act as alias
        $column = explode('_', $column);
        $column = array_shift($column).'.'.implode('_', $column);

        // set default order type
        $order = $this->getValue($this->getAttrib('order'));

        if ($order == 'group') {
        	$this->getColumn()->getDataGrid()->setGroupColumn($column);
        }

        if (null !== $order) {
            $order = ($order != 'asc') ? 'desc' : $order;
            $query->addOrderBy(sprintf('%s %s', $column, $order));
        } else
        // grupowanie po tej koumnie - ustawia domyślne sortowanie
    	if ($this->getColumn()->isGroup()) {
        	$query->addOrderBy(sprintf('%s %s', $column, 'desc'));
        }
    }

    public function render() {
    	// filter nic nie renderuje
        return '';
    }
    
	/**
     * @var string
     */
    protected $_className;
    
    /**
     * @param string $name
     * @return void
     */
    public function setClassName($name) {
    	$this->_className = (string) $name;
    }

    public function getClassName() {
    	return (null === $this->_className)
    		? parent::getClassName()
    		: $this->_className;
    }
}