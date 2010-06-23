<?php
require_once 'KontorX/DataGrid/Adapter/Abstract.php';
class KontorX_DataGrid_Adapter_Array extends KontorX_DataGrid_Adapter_Abstract {

    /**
     * @param array $table
     */
    public function __construct(array $table) {
        $this->setAdaptable($table);
    }

    /**
     * Get raw column names & optional options
     * @return array
     */
    public function getRawColumnsInfo()
    {
    	$data = $this->getAdaptable();
    	$data = current($data); 	// fetch first row and
    	$data = array_keys($data); 	// get array keys as column names
    	return $data;
    }
    
    /**
     * Wyławia szukane kolumny spełniające warunek ..
     * @return array
     */
    protected function _fetchData() {
    	$data = $this->getAdaptable();
    	$data = array_values($data);

    	// czy jest paginacja
        if ($this->_dataGrid->enabledPagination()) {
            list($pageNumber, $itemCountPerPage) = $this->_dataGrid->getPagination();
            $data = array_slice(
	            	$data,
	            	$pageNumber * $itemCountPerPage,
	            	$itemCountPerPage
            );
        }

        return $this->_data = $data;
    }
}