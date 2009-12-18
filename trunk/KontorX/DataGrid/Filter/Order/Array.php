<?php
require_once 'KontorX/DataGrid/Filter/Abstract.php';
class KontorX_DataGrid_Filter_Order_Array extends KontorX_DataGrid_Filter_Abstract {

    public function filter(KontorX_DataGrid_Adapter_Interface $adapter) {
        if (!$adapter instanceof KontorX_DataGrid_Adapter_Array) {
            require_once 'KontorX/DataGrid/Exception.php';
            throw new KontorX_DataGrid_Exception("Wrong filter adapter");
        }

        $data = $adapter->getAdaptable();
        $column = $this->getAttrib('column', $this->getColumnName());
        // set default order type
        $order = $this->getValue($this->getAttrib('order'));

        // wyciągnięcie klumny, po której odbywa sie sortowanie
        $orderColumn = array();
        foreach($data as $key => $value) {
        	$orderColumn[$key] = $value[$column];
        }

        if ('null' != $order) {
        	if (null !== $order) {
	            $order = ($order != 'asc') ? SORT_DESC : SORT_ASC;
	            array_multisort($orderColumn, $order, $data);
	        }
        } else	         
        // grupowanie po tej koumnie - ustawia domyślne sortowanie
    	if ($this->getColumn()->isGroup()) {
    		array_multisort($orderColumn, SORT_DESC, $data);
        }

        // ustaw ponownie posortowane wartości
        $adapter->setAdaptable($data);
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