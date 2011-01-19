<?php
require_once 'KontorX/DataGrid/Adapter/Abstract.php';

/**
 * @author msanokowski (michal.sanokowski@gmail.com)
 */
class KontorX_DataGrid_Adapter_Doctrine extends KontorX_DataGrid_Adapter_Abstract 
{
    /**
     * @param Doctrine_Query $query
     */
	public function __construct(Doctrine_Query $query) 
	{
        $this->setAdaptable($query);
    }

    /**
     * @return Doctrine_Query
     */
    public function getQuery() {
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
    protected function _fetchData() 
    {
    	/* @var $query Doctrine_Query */
        $query = $this->getQuery();
       
        // czy jest paginacja
        if ($this->_dataGrid->enabledPagination()) 
        {
            list($pageNumber, $itemCountPerPage) = $this->_dataGrid->getPagination();
            
            $page = $this->_dataGrid->getPageNumber();
            
            $count  = (int) $rowCount;
            $offset = (int) $rowCount * ($page - 1);

            $query->limit($count);
            $query->offset($offset);
        }

        $data = $query->execute(array(), Doctrine::HYDRATE_SCALAR);
        
        return $data;
    }
}