<?php

// TODO TO DO!

require_once 'KontorX/DataGrid/Column/Abstract.php';
class KontorX_DataGrid_ColumnGroup extends KontorX_DataGrid_Column_Abstract {

    protected function _init() {
        require_once 'KontorX/DataGrid/Filter/Order.php';
        $filter = new KontorX_DataGrid_Filter_Order($this->getAttribs());

        $this->addFilter($filter);
    }

    public function render() {
        foreach ($this->getColumns() as $column) {
        	
        }
    }
    
    /**
     * @var KontorX_DataGrid
     */
    protected $_dataGrid = null;
    
    /**
     * @param KontorX_DataGrid $dataGrid
     * @return void
     */
    public function setDataGrid(KontorX_DataGrid $dataGrid) {
    	$this->_dataGrid = $dataGrid;
    }

    /**
     * @return KontorX_DataGrid
     */
    public function getDataGrid() {
    	if (null === $this->_dataGrid) {
    		require_once 'KontorX/DataGrid/Exception.php';
    		throw new KontorX_DataGrid_Exception('Data grid instance is not set');
    	}
    	return $this->_dataGrid;
    }
    
	/**
     * @var array
     */
    private $_columns = array();

    /**
     * Dodaje kolumnę
     * @param string $columnName
     * @param array $options
     */
    public function addColumn($columnName, $options = null) {
        if (null === $options) {
            $options = array();
        } else
        if (is_string($options)) {
            $type = $options;
            $options = array();
        } else
        if ($options instanceof Zend_Config) {
            $options = $options->toArray();
        } else
        if (is_array($options)) {
            $options = $options;
        }

        if (!isset($options['name'])) {
            $options['name'] = $columnName;
        }

        if (!isset($options['columnName'])) {
            $options['columnName'] = $columnName;
        }

        if (isset($options['type'])) {
            $type = $options['type'];
            unset ($options['type']);
        }

        if (!isset($type)) {
            $type = KontorX_DataGrid::DEFAULT_COLUMN_TYPE;
        }

        if (!$columnName instanceof KontorX_DataGrid_Column_Interface) {
        	// create column instance
	        $columnClass = $this->getPluginLoader(KontorX_DataGrid::COLUMN)->load($type);
	        $columnInstance = new $columnClass($columnName, $options);
        } else {
        	$columnInstance = $columnName;
        }
        
        // create filter
        if (isset($options['filter'])) {
        	// TODO Teraz nie muszę przekazywać 'columnName'
            $filter = $this->_createFilter((array) $options['filter']);
            $columnInstance->addFilter($filter);
        }
        // create row
        if (isset($options['row'])) {
        	// TODO Teraz nie muszę przekazywać 'columnName'
            $row = $this->_createRow((array) $options['row']);
            $columnInstance->setRow($row);
        }

        $this->_columns[$columnInstance->getColumnName()] = $columnInstance;
    }

    /**
     * Add multi columns
     * @param array $columns
     */
    public function addColumns(array $columns) {
        foreach ($columns as $key => $value) {
            if (is_array($value)) {
                $this->addColumn($key, $value);
            } else {
                $this->addColumn($value);
            }
        }
    }

    /**
     * Return true if column was added otherwey false
     * @param string $name
     * @return bool
     */
    public function hasColumn($name) {
        return array_key_exists($name, $this->_columns);
    }

    /**
     * Return @see KontorX_DataGrid_Column_Interface or null if no exsists
     * @param string $name
     * @return KontorX_DataGrid_Column_Interface
     */
    public function getColumn($name) {
        return $this->_columns[$name];
    }

    /**
     * Reset column if any was exsists before and add multi columns
     * @param array $columns
     */
    public function setColumns(array $columns) {
        $this->resetColumns();
        $this->addColumns($columns);
    }

    /**
     * Return columns
     * @return array
     */
    public function getColumns() {
        return $this->_columns;
    }

    /**
     * Reset columns
     */
    public function resetColumns() {
        $this->_columns = null;
        $this->_columns = array();
    }

    /**
     * Create filter object @see KontorX_DataGrid_Filter_Interface
     * @param array $options
     * @return KontorX_DataGrid_Filter_Interface
     */
    private function _createFilter($options = null) {
        if (null === $options) {
            $options = array();
        } else
        if (is_string($options)) {
            $type = $options;
            $options = array();
        } else
        if ($options instanceof Zend_Config) {
            $options = $options->toArray();
        } else
        if (is_array($options)) {
            $options = $options;
        }

        if (isset($options['type'])) {
            $type = $options['type'];
        }

        if (!isset($type)) {
            $type = KontorX_DataGrid::DEFAULT_FILTER_TYPE;
        }

        // create column instance
        $filterClass = $this->getPluginLoader(KontorX_DataGrid::FILTER)->load($type);
        $filterInstance = new $filterClass($options);

        return $filterInstance;
    }

    /**
     * Create row object @see KontorX_DataGrid_Row_Interface
     * @param array $options
     * @return KontorX_DataGrid_Row_Interface
     */
    private function _createRow($options = null) {
        if (null === $options) {
            $options = array();
        } else
        if (is_string($options)) {
            $type = $options;
            $options = array();
        } else
        if ($options instanceof Zend_Config) {
            $options = $options->toArray();
        } else
        if (is_array($options)) {
            $options = $options;
        }

        if (isset($options['type'])) {
            $type = $options['type'];
            unset ($options['type']);
        }

        if (!isset($type)) {
            $type = KontorX_DataGrid::DEFAULT_ROW_TYPE;
        }

        // create column instance
        $rowClass = $this->getPluginLoader(KontorX_DataGrid::ROW)->load($type);
        $rowInstance = new $rowClass($options);
        return $rowInstance;
    }
}