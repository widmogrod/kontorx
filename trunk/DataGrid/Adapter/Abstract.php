<?php
require_once 'KontorX/DataGrid/Adapter/Interface.php';
abstract class KontorX_DataGrid_Adapter_Abstract implements KontorX_DataGrid_Adapter_Interface {

    /**
     * @var mixed
     */
    private $_data = null;

    /**
     * Set raw data
     * @param mixed $data
     */
    public function setData($data) {
        $this->_data = $data;
    }

    /**
     * Return a raw data
     * @return mixed
     */
    public function getData() {
        return $this->_data;
    }

    /**
     * @var array
     */
    private $_columns = array();

    /**
     * Set array of @see KontorX_DataGrid_Column_Interface objects
     * @param array $columns
     */
    public function setColumns(array $columns) {
        $this->_columns = $columns;
    }

    /**
     * Return columns
     * @return array
     */
    public function getColumns(array $columns = null) {
        return empty($this->_columns) ? $columns : $this->_columns;
    }

    /**
     * @var array
     */
    private $_pagination = array();

    /**
     * Set pagination parameters
     * @param integer $limit
     * @param integer $rowCount
     */
    public function setPagination($pageNumber, $itemCountPerPage) {
        $this->_pagination = array($pageNumber, $itemCountPerPage);
    }

    /**
     * Return pagination controls
     * @return array
     */
    public function getPagination() {
        return $this->_pagination;
    }

    /**
     * Return true if pagination is set (has parameters) otherway false
     * @return bool
     */
    public function isPagination() {
        return (count($this->_pagination) == 2);
    }

    private $_rows = null;

    /**
     * Get array set of @see KontorX_DataGrid_Row_Interface
     * @return array
     */
    public function getRows() {
        if (null === $this->_rows) {
            $result = array();
            foreach ($this->getColumns() as $columnName => $columnInstance) {
                $row = $columnInstance->getRow();
                if (null != $row) {
                    $result[$columnName] = $row;
                }
            }
            $this->_rows = $result;
        }

        return $this->_rows;
    }
}