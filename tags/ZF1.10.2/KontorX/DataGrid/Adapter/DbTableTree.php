<?php
require_once 'KontorX/DataGrid/Adapter/Abstract.php';

class KontorX_DataGrid_Adapter_DbTableTree extends KontorX_DataGrid_Adapter_Abstract {

    /**
     * @param KontorX_Db_Table_Tree_Abstract $table
     */
    public function __construct(KontorX_Db_Table_Tree_Abstract  $table) {
        $this->setAdaptable($table);
    }

    /**
     * @var KontorX_Db_Table_Tree_Abstract
     */
    protected $_table = null;

    /**
     * Zwraca @see KontorX_Db_Table_Tree_Abstract
     *
     * @return KontorX_Db_Table_Tree_Abstract
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
     *
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
    	if (null === $this->_data) {
    		/* @var $select Zend_Db_Table_Abstract */
	        $table = $this->getTable();
	        /* @var $select Zend_Db_Select */
	        $select = $this->getSelect();
	    	
	
	        // czy jest paginacja
	        if ($this->_dataGrid->enabledPagination()) {
	        	require_once 'KontorX/DataGrid/Exception.php';
	        	throw new KontorX_DataGrid_Exception('pagination is not supported in this adapter');
	        }
	        
	        /* @var $dataset KontorX_Db_Table_Tree_Rowset_Abstract */
	        $dataset = $table->fetchAll($select);
	        $this->_iterator = $dataset;
	
	        $recursive = new RecursiveIteratorIterator($dataset, RecursiveIteratorIterator::SELF_FIRST);
	        $recursive->rewind();
	
	        while ($recursive->valid()) {
	        	/* @var $current KontorX_Db_Table_Tree_Row_Abstract */
	        	$current = $recursive->current();
				$this->_data[] = $current->toArray();
	            $recursive->next();
	        }
    	}
        return $this->_data;
    }

    /**
     * @var KontorX_Db_Table_Tree_Rowset_Abstract
     */
    protected $_iterator;
    
    /**
     * @return KontorX_Db_Table_Tree_Rowset_Abstract
     */
    public function getIterator() {
    	if (null === $this->_iterator) {
    		// implicite set $this->_iterator
    		$this->_fetchData();
    	}
    	return $this->_iterator;
    }
    
	protected function _getCacheId() {
        return self::CACHE_PREFIX . md5((string)$this->getTable()->getSelect());
    }
}