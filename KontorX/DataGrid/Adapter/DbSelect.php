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
     * Wyławia szukane kolumny spełniające warunek ..
     *
     * @param bool $raw
     * @return array
     */
    public function fetchData($raw = false) {
        /* @var $select Zend_Db_Select */
        $select = $this->getSelect();        

        // czy jest paginacja
        if ($this->_dataGrid->enabledPagination()) {
            list($pageNumber, $itemCountPerPage) = $this->_dataGrid->getPagination();
            $select->limitPage($pageNumber, $itemCountPerPage);
        }

        // cache on
        if ($this->_cacheEnabled()) {
            if (false !== ($result = self::$_cache->load($this->_getCacheId()))) {
            	$this->_count = count($this->_rows);
                return $this->_rows;
            }
        }

        $columns = $this->_dataGrid->getColumns();
        $cells   = $this->getCells();
        $cellsetClass = $this->getCellsetClass();

        $stmt = $select->query();

	    // hack.. dla potrzebnej funkcjonalności..
        if (true === $raw) {
        	return $stmt->fetchAll(Zend_Db::FETCH_OBJ);
        }

        $this->_fetched = true;
        
        $groupCellPrev = null;

        while (($rawData = $stmt->fetch(Zend_Db::FETCH_ASSOC))) {
			/* @var $cellset KontorX_DataGrid_Adapter_Cellset_Interface */
			$cellset = new $cellsetClass();

            // tworzymy tablice wielowymiarowa rekordow
            foreach ($columns as $columnName => $columnInstance) {
                if (isset($cells[$columnName])
                    	&& $cells[$columnName] instanceof KontorX_DataGrid_Cell_Interface) {
					// jest dekorator rekordu @see KontorX_DataGrid_Cell_Interface
                    $cloneCellInstance = clone $cells[$columnName];
                    $cloneCellInstance->setData($rawData, $columnName);
                    $cell = $cloneCellInstance;
                } else {
                	// surowy rekord!
                    $cell = isset($rawData[$columnName])
                    	? $rawData[$columnName] : null;
                }

	            if ($columnInstance->isGroup()) {
	            	if ($groupCellPrev !== $cell) {
	            		$groupCellPrev = $cell;
	            		$cellset->setGroupCell($cell);
	            	}
		        } else {
		        	$cellset->addCell($cell);
		        }
            }

			$this->_rows[$this->_pointer++] = $cellset;
			$this->_count++;
        }

        // cache save
        if ($this->_cacheEnabled()) {
            self::$_cache->save($this->_rows, $this->_getCacheId());
        }
    }
    
	protected function _getCacheId() {
        return self::CACHE_PREFIX . md5((string)$this->getSelect());
    }
}