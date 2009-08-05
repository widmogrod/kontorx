<?php
require_once 'KontorX/DataGrid/Adapter/Abstract.php';
class KontorX_DataGrid_Adapter_Array extends KontorX_DataGrid_Adapter_Abstract {

    /**
     * Konstruktor
     *
     * @param array $array
     */
    public function __construct(array $array) {
        $this->_array = $array;
    }

    /**
     * @var array
     */
    protected $_array = null;

    /**
     * Zwraca array
     *
     * @return array
     */
    public function getArray() {
        return $this->_array;
    }

    /**
     * Wyławia szukane kolumny spełniające warunek ..
     * @return array
     */
    public function fetchData() {
        $array = $this->getArray();

        // czy jest paginacja
        if ($this->isPagination()) {
            list($pageNumber, $itemCountPerPage) = $this->getPagination();
            // @TODO Test
            $array = array_slice($array, $itemCountPerPage*($pageNumber-1), $itemCountPerPage);
        }

        // cache on
        if ($this->_cacheEnabled()) {
            if (false !== ($result = self::$_cache->load($this->_getCacheId()))) {
                return $result;
            }
        }

        $columns = $this->getColumns();
        $rows   = $this->getRows();

        $result = array();
        $i = 0;
        while (($rawData = array_shift($array))) {
            // tworzymy tablice wielowymiarowa rekordow
            foreach ($columns as $columnName => $columnInstance) {
                // jest dekorator rekordu @see KontorX_DataGrid_Row_Interface
                if (isset($rows[$columnName])
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
            $i++;
        }

        // cache save
        if ($this->_cacheEnabled()) {
            self::$_cache->save($result, $this->_getCacheId());
        }

        return $result;
    }
}