<?php
/**
 * @author msanokowski (michal.sanokowski@gmail.com)
 */
class KontorX_DataGrid_Adapter_Doctrine extends KontorX_DataGrid_Adapter_Abstract {

    /**
     * @param array $table
     */
public function __construct(Doctrine_Query $select) {
      
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
        $oQuery = $this->getSelect();
       
        // czy jest paginacja
        
        if ($this->_dataGrid->enabledPagination()) {
            list($pageNumber, $itemCountPerPage) = $this->_dataGrid->getPagination();
            
            $page = $this->_dataGrid->getPageNumber();
            
             $count  = (int) $rowCount;
             $offset = (int) $rowCount * ($page - 1);

            $oQuery->limit($count);
            $oQuery->offset($offset);
        }

        
        $data = $oQuery->execute(array(), Doctrine::HYDRATE_ARRAY);
        return $data;
    }
}