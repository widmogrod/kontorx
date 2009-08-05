<?php
require_once 'KontorX/DataGrid/Adapter/Abstract.php';
class KontorX_DataGrid_Adapter_DbSelect extends KontorX_DataGrid_Adapter_Abstract {

    /**
     * Konstruktor
     *
     * @param Zend_Db_Table_Abstract $table
     */
    public function __construct(Zend_Db_Select $select) {
        $this->_select = $select;
    }

    /**
     * @var Zend_Db_Select
     */
    protected $_select = null;

    /**
     * Zwraca @see Zend_Db_Select
     *
     * @return Zend_Db_Select
     */
    public function getSelect() {
        return $this->_select;
    }

    /**
     * Wyławia szukane kolumny spełniające warunek ..
     *
     * @param array $columns
     * @param array $filters
     * @return array
     */
    public function fetchData() {
        $select = $this->getSelect();

        // czy jest paginacja
        if ($this->isPagination()) {
            list($pageNumber, $itemCountPerPage) = $this->getPagination();
            $select->limitPage($pageNumber, $itemCountPerPage);
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

        $stmt = $select->query();
        while (($rawData = $stmt->fetch(Zend_Db::FETCH_ASSOC))) {
            // tworzymy tablice wielowymiarowa rekordow
            foreach ($columns as $columnName => $columnInstance) {
                // jest dekorator rekordu @see KontorX_DataGrid_Row_Interface
                if (isset($rows[$columnName])
                    && $rows[$columnName] instanceof KontorX_DataGrid_Row_Interface)
                {
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
            $i++;
        }

        // cache save
        if ($this->_cacheEnabled()) {
            self::$_cache->save($result, $this->_getCacheId());
        }

        return $result;
    }
    
	protected function _getCacheId() {
        return self::CACHE_PREFIX . md5((string)$this->getSelect());
    }
}