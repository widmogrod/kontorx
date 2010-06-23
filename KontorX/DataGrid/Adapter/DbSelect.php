<?php
require_once 'KontorX/DataGrid/Adapter/Abstract.php';
class KontorX_DataGrid_Adapter_DbSelect extends KontorX_DataGrid_Adapter_Abstract {

    /**
     * @param Zend_Db_Table_Abstract $table
     */
    public function __construct(Zend_Db_Select $select) {
        $this->setAdaptable($select);
    }

    /**
     * @return Zend_Db_Select
     */
    public function getSelect() {
        return $this->getAdaptable();
    }
    
	/**
     * Get raw column names & optional options
     * @return array
     */
    public function getRawColumnsInfo()
    {
    	$data = $this->_fetchData();
    	$data = current($data); 	// fetch first row and
    	$data = array_keys($data); 	// get array keys as column names
    	return $data;
    }

    /**
     * Wyławia szukane kolumny spełniające warunek ..
     * @return array
     */
    protected function _fetchData() {
    	/* @var $select Zend_Db_Select */
        $select = $this->getSelect();

        // czy jest paginacja
        if ($this->_dataGrid->enabledPagination()) {
            list($pageNumber, $itemCountPerPage) = $this->_dataGrid->getPagination();
            $select->limitPage($pageNumber, $itemCountPerPage);
        }

        $stmt = $select->query();
        $data = $stmt->fetchAll(Zend_Db::FETCH_ASSOC);
        return $data;
    }
}