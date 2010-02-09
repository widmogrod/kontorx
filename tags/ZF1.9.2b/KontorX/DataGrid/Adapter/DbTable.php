<?php
require_once 'KontorX/DataGrid/Adapter/Abstract.php';

class KontorX_DataGrid_Adapter_DbTable extends KontorX_DataGrid_Adapter_Abstract {

    /**
     * @param Zend_Db_Table_Abstract $table
     */
    public function __construct(Zend_Db_Table_Abstract $table) {
        $this->setAdaptable($table);
    }

    /**
     * @var Zend_Db_Table_Abstract
     */
    protected $_table = null;

    /**
     * Zwraca @see Zend_Db_Table_Abstract
     * @return Zend_Db_Table_Abstract
     */
    public function getTable() {
        return $this->_adaptable;
    }

    /**
     * @var Zend_Db_Select
     */
    private $_select = null;

    /**
     * Return select statment mini. singletone
     * @return Zend_Db_Select
     */
    public function getSelect() {
        if (null === $this->_select) {
            $this->_select = $this->_adaptable->select();
        }
        return $this->_select;
    }

    /**
     * Wyławia szukane kolumny spełniające warunek ..
     * @return array
     */
    protected function _fetchData() {
    	/* @var $select Zend_Db_Table_Abstract */
        $table = $this->getTable();
        /* @var $select Zend_Db_Select */
        $select = $this->getSelect();

    	// czy jest paginacja
        if ($this->_dataGrid->enabledPagination()) {
            list($pageNumber, $itemCountPerPage) = $this->_dataGrid->getPagination();
            $select->limitPage($pageNumber, $itemCountPerPage);
        }

        $dataset = $table->fetchAll($select);
        return $this->_data = $dataset->toArray();
    }
}