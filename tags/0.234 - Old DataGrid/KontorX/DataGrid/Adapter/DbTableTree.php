<?php
require_once 'KontorX/DataGrid/Adapter/Abstract.php';

class KontorX_DataGrid_Adapter_DbTableTree extends KontorX_DataGrid_Adapter_Abstract {

    /**
     * @param KontorX_Db_Table_Tree_Abstract $table
     */
    public function __construct(KontorX_Db_Table_Tree_Abstract  $table) {
        $this->setData($table);
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
        return $this->getData();
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
            $this->_select = $this->getTable()->select();
        }
        return $this->_select;
    }

    /**
     * Wyławia szukane kolumny spełniające warunek ..
     * @param bool $raw
     * @return array
     */
    public function fetchData($raw = false) {
        $table = $this->getTable();
        $select = $this->getSelect();

        // czy jest paginacja
        if ($this->isPagination()) {
        	require_once 'KontorX/DataGrid/Exception.php';
        	throw new KontorX_DataGrid_Exception('pagination is not supported in this adapter');
        }

        // cache on
        if ($this->_cacheEnabled()) {
            if (false !== ($result = self::$_cache->load($this->_getCacheId()))) {
                return $result;
            }
        }

        $columns = $this->getColumns();
        $rows   = $this->getRows();

        $i = 0;
        $result = array();
        $dataset = $table->fetchAll($select);

        // hack.. dla potrzebnej funkcjonalności..
        if (true === $raw) {
        	return $dataset;
        }
        
        $recursive = new RecursiveIteratorIterator($dataset, RecursiveIteratorIterator::SELF_FIRST);
        $recursive->rewind();

        while ($recursive->valid()) {
        	$data = $recursive->current();
            $rawData = $data->toArray();

            // tworzymy tablice wielowymiarowa rekordow
            foreach ($columns as $columnName => $columnInstance) {
                // jest dekorator rekordu @see KontorX_DataGrid_Row_Interface
                if (count($rows)
                    && isset($rows[$columnName])
                    && $rows[$columnName] instanceof KontorX_DataGrid_Row_Interface) {
                    $cloneRowInstance = clone $rows[$columnName];
                    $cloneRowInstance->setData($rawData, $columnName);
                    $result[$i][$columnName] = $cloneRowInstance;
                }
                // surowy rekord!
                else {
                    $result[$i][$columnName] = isset($rawData[$columnName])
                    ? $rawData[$columnName] : null;
                }
            }
            ++$i;
            $recursive->next();
        }

        // cache save
        if ($this->_cacheEnabled()) {
            self::$_cache->save($result, $this->_getCacheId());
        }

        return $result;
    }
    
	protected function _getCacheId() {
        return self::CACHE_PREFIX . md5((string)$this->getTable()->getSelect());
    }
}