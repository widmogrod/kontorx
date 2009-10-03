<?php
require_once 'Zend/Config.php';

/**
 * KontorX_DataGrid
 *
 * @category 	KontorX
 * @package 	KontorX_DataGrid
 * @version 	0.5.1
 * @license	GNU GPL
 * @author 	Marcin `widmogror` Habryn, widmogrod@gmail.com
 */
class KontorX_DataGrid {
    const DEFAULT_CELL_TYPE   = 'Text';
    const DEFAULT_FILTER_TYPE = 'Text';
    const DEFAULT_COLUMN_TYPE = 'Text';
    
    /**
     * @param KontorX_DataGrid_Adapter_Interface $adapter
     */
    private function __construct(KontorX_DataGrid_Adapter_Interface $adapter) {
        $this->_adapter = $adapter;
        $this->_adapter->setDataGrid($this);
    }

    /**
     * Setup @see KontorX_DataGrid with property data adapter
     * @param mixed $data
     * @param Zend_Config|array|null $options
     * @return KontorX_DataGrid
     */
    public static function factory($data, $options = null) {
        $instance = null;
        if ($data instanceof Zend_Db_Table_Abstract) {
        	if ($data instanceof KontorX_Db_Table_Tree_Abstract) {
        		require_once 'KontorX/DataGrid/Adapter/DbTable.php';
            	$adapter = new KontorX_DataGrid_Adapter_DbTableTree($data);
        	} else {
        		require_once 'KontorX/DataGrid/Adapter/DbTable.php';
            	$adapter = new KontorX_DataGrid_Adapter_DbTable($data);
        	}
        } else
        if ($data instanceof Zend_Db_Select) {
            require_once 'KontorX/DataGrid/Adapter/DbSelect.php';
            $adapter = new KontorX_DataGrid_Adapter_DbSelect($data);
        } else
        if (is_array($data)) {
            require_once 'KontorX/DataGrid/Adapter/Array.php';
            $adapter = new KontorX_DataGrid_Adapter_Array($data);
        } else {
            require_once 'KontorX/DataGrid/Exception.php';
            throw new KontorX_DataGrid_Exception("Data type is not suported");
        }

        /* @var $instance KontorX_DataGrid */
        $instance = new self($adapter);

        if (is_array($options)) {
            $instance->setOptions($options);
        } elseif ($options instanceof Zend_Config) {
            $instance->setConfig($options);
        }

        return $instance;
    }
    
	/**
     * @var KontorX_DataGrid_Adapter_Interface
     */
    private $_adapter = null;

    /**
     * Return @see KontorX_DataGrid_Adapter_Interface
     * @return KontorX_DataGrid_Adapter_Interface
     */
    public function getAdapter() {
        return $this->_adapter;
    }

    /**
     * Types of @see Zend_Loader_PluginLoader
     */
    const COLUMN = 'column';
    const CELL 	 = 'cell';
    const FILTER = 'filter';

    /**
     * @var array of @see Zend_Loader_PluginLoader
     */
    private $_pluginLoader = array();

    /**
     * Set @see Zend_Loader_PluginLoader
     * @param Zend_Loader_PluginLoader $loader
     * @param string $type
     */
    public function setPluginLoader(Zend_Loader_PluginLoader $loader, $type) {
        switch ($type) {
            case self::COLUMN:
            case self::CELL:
            case self::FILTER:
                $this->_pluginLoader[$type] = $loader;
                break;
        default:
            require_once 'KontorX/DataGrid/Exception.php';
            throw new KontorX_DataGrid_Exception(sprintf('Invalid type "%s" provided to getPluginLoader()', $type));
        }
    }

    /**
     * Get @see Zend_Loader_PluginLoader
     *
     * @param string $type
     * @return Zend_Loader_PluginLoader
     */
    public function getPluginLoader($type = null) {
        if (!isset($this->_pluginLoader[$type])) {
            switch ($type) {
                case self::COLUMN:
                    $prefixSegment = 'DataGrid_Column';
                    $pathSegment   = 'DataGrid/Column';
                    break;
                case self::CELL:
                    $prefixSegment = 'DataGrid_Cell';
                    $pathSegment   = 'DataGrid/Cell';
                    break;
                case self::FILTER:
                    $prefixSegment = 'DataGrid_Filter';
                    $pathSegment   = 'DataGrid/Filter';
                    break;
                default:
                    require_once 'KontorX/DataGrid/Exception.php';
                    throw new KontorX_DataGrid_Exception(sprintf('Invalid type "%s" provided to getPluginLoader()', $type));
            }

            $this->_pluginLoader[$type] = new Zend_Loader_PluginLoader(array(
                "KontorX_$prefixSegment" => "KontorX/$pathSegment"
            ));
        }

        return $this->_pluginLoader[$type];
    }

    /**
     * @param array $paths
     * @return void
     */
    public function addPrefixPaths(array $paths) {
    	foreach ($paths as $data) {
    		if (isset($data['prefix']) && isset($data['path']) && isset($data['type'])) {
    			$this->addPrefixPath($data['prefix'], $data['path'], $data['type']);
    		}
    	}
    }
    
    /**
     * @param string $prefix
     * @param string $path
     * @param string $type
     * @return void
     */
    public function addPrefixPath($prefix, $path, $type) {
    	$loader = $this->getPluginLoader($type);
    	$loader->addPrefixPath($prefix, $path);
    }

    /**
     * @param Zend_Config $config
     * @return void
     */
    public function setConfig(Zend_Config $config) {
        $this->setOptions($config->toArray());
    }

    /**
     * @param array $options
     * @return void
     */
    public function setOptions(array $options) {
    	if (isset($options['prefixPaths'])) {
    		$this->addPrefixPaths((array) $options['prefixPaths']);
    		unset($options['prefixPaths']);
    	}
    	if (isset($options['pagination'])) {
    		if (is_array($options['pagination'])) {
    			if (isset($options['pagination']['pageNumber'])
    					&& isset($options['pagination']['itemCountPerPage'])) {
    				$pageNumber		  = $options['pagination']['pageNumber'];
    				$itemCountPerPage = $options['pagination']['itemCountPerPage'];
    				$this->setPagination($pageNumber, $itemCountPerPage);
    			} else
    			if (isset($options['pagination'][0])
    					&& isset($options['pagination'][1])) {
    				list($pageNumber, $itemCountPerPage) = $options['pagination'];
    				$this->setPagination($pageNumber, $itemCountPerPage);
    			}
    		}
    		unset($options['pagination']);
    	}

        foreach ($options as $name => $value) {
            $method = 'set'.ucfirst($name);
            if (method_exists($this, $method)) {
                $this->$method($value);
            }
        }
    }

    /**
     * @var array
     */
    private $_order = array();

    /**
     * Set order of column to display
     * @param array $order
     */
    public function setOrder(array $order) {
        $this->_order = $order;
    }

    /**
     * Get order of column
     * @return array
     */
    public function getOrder() {
        return $this->_order;
    }

    /**
     * Initialize a order of columns/filters ..
     */
    private function _orderColumns() {
        $orderCount   = count($this->_order);
        $columnsCount = count($this->_columns);

        if ($orderCount > $columnsCount) {
            require_once 'KontorX/DataGrid/Exception.php';
            throw new KontorX_DataGrid_Exception("Mising columns? to meny columns order");
        }

        $array = $this->_columns;
        $result = array();
        foreach ($this->_order as $columnName) {
            if (array_key_exists($columnName, $array)) {
                $result[$columnName] = $array[$columnName];
                unset($array[$columnName]);
            }
        }

        // add columns not ordered
        if (count($array)) {
            $result += $array;
        }

        $this->_columns = $result;
    }

    /**
     * @var Zend_Config
     */
    private $_values = null;

    /**
     * Set array of values
     * array(
     *  //columns => ..
     *  filters => ..
     * )
     *
     * @param Zend_Config $values
     */
    public function setValues($values) {
        if (is_array($values)) {
            $values = new Zend_Config((array) $values, true);
        } else
        if (!$values instanceof Zend_Config) {
            require_once 'KontorX/DataGrid/Exception.php';
            throw new KontorX_DataGrid_Exception("Values are not array or Zend_Config instance");
        }

        // sprawdzanie czy jest 'filter' namespace!
        if (!isset($values->filter)) {
            // stworzenie namespace 'filter'
            $backup = $values;
            $values = new Zend_Config(array('filter' => $backup), true);
        }

        $this->_values = array();
        $this->_values = $values;
    }

    /**
     * @param string $type
     */
    public function setRequestValues($type = null) {
        $type = strtoupper($type);
        switch ($type) {
            default:
            case 'GET':
                $values = $_GET;
            case 'POST':
                $values = $_POST;
                break;
        }

        if (isset($values['filter'])) {
            $this->setValues((array) $values['filter']);
        }
    }

    /**
     * Return @see Zend_Config
     * @return Zend_Config
     */
    public function getValues() {
        if (null === $this->_values) {
            $this->_values = new Zend_Config(array(), true);
        }
        return $this->_values;
    }

    /**
     * Set each column property value
     * @return void
     */
    private function _noticeValues() {
        $values = $this->getValues();
        if ($values instanceof Zend_Config) {
            foreach ($this->getColumns() as $name => $column) {
                $column->setValues($values);
                // if column has filter - set values to filter!
                foreach ($column->getFilters() as $filter) {
                    $filter->setValues($values);
                }
            }
        }
    }

    /**
     * @var string
     */
    protected $_group = null;
    
    /**
     * @param string $columnName
     */
    public function setGroup($columnName) {
    	$this->_group = (string) $columnName;
    }
    
    /**
     * @return string
     */
	public function getGroup() {
    	return $this->_group;
    }

    /**
     * @return bool
     */
	public function isGrouping() {
    	return null !== $this->_group;
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
            $type = self::DEFAULT_COLUMN_TYPE;
        }

        if (!$columnName instanceof KontorX_DataGrid_Column_Interface) {
        	// create column instance
	        $columnClass = $this->getPluginLoader(self::COLUMN)->load($type);
	        /* @var $columnInstance KontorX_DataGrid_Column_Interface */
	        $columnInstance = new $columnClass($columnName, $options);
        } else {
        	/* @var $columnInstance KontorX_DataGrid_Column_Interface */
        	$columnInstance = $columnName;
        }
        
        // set group column
        if (array_key_exists('group', $options)) {
			$columnInstance->setGroup((bool) $options['group']);
        }

        // create filter
        if (isset($options['filter'])) {
            $filter = $this->_createFilter((array) $options['filter']);
            $columnInstance->addFilter($filter);
        }

        // cell is allways
		$cell = $this->_createCell((array) @$options['cell']);
		$columnInstance->setCell($cell);

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
            $type = self::DEFAULT_FILTER_TYPE;
        }

        // create column instance
        $filterClass = $this->getPluginLoader(self::FILTER)->load($type);
        $filterInstance = new $filterClass($options);

        return $filterInstance;
    }

    /**
     * Create row object @see KontorX_DataGrid_Cell_Interface
     * @param array $options
     * @return KontorX_DataGrid_Cell_Interface
     */
    private function _createCell($options = null) {
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
            $type = self::DEFAULT_CELL_TYPE;
        }

        // create cell instance
        $cellClass = $this->getPluginLoader(self::CELL)->load($type);
        $cellInstance = new $cellClass($options);
        return $cellInstance;
    }

    /**
     * Return array of @see KontorX_DataGrid_Filter_Interface
     * @return array
     */
    private function getFilters() {
        $result = array();
        foreach ($this->getColumns() as $column) {
            array_push($result, $column->getFilters());
        }
        return $result;
    }

    /**
     * @return void
     */
    private function _noticeFilters() {
    	$adapter = $this->getAdapter();
        foreach ($this->getColumns() as $column) {
            foreach ($column->getFilters() as $filter) {
                $filter->filter($adapter);
            }
        }
    }

    /**
     * @var array
     */
    private $_vars = null;

    /**
     * Return array of vars neaded to render html table
     * @return array
     */
    public function getVars() {
        if (null === $this->_vars) {
            $this->_noticeValues();
            $this->_noticeFilters();

            $this->_orderColumns();

            $columns = $this->getColumns();
			$filters = $this->getFilters();
        	$adapter = $this->getAdapter();

            $this->_vars = array(
                'columns' => $columns,
                'filters' => $filters,
                'rowset'  => $adapter,
                'paginator' => ($this->enabledPagination() ? $this->_createPaginator() : null),
                'valuesQuery' => urldecode(http_build_query($this->getValues()->toArray()))
            );
        }
        return $this->_vars;
    }

    /**
     * Reset vars
     */
    public function resetVars() {
        $this->_vars = null;
    }

    /**
     * @var bool
     */
    private $_enabledPagination = true;

    /**
     * Disable pagination if is enabled
     */
    public function disablePagination() {
        $this->_enabledPagination = false;
    }

    /**
     * Disable pagination if is disabled
     */
    public function enablePagination() {
        $this->_enabledPagination = true;
    }

    /**
     * Retrun true if pagination is enabled and pagination options are set!
     * @return bool
     */
    public function enabledPagination() {
        return $this->_enabledPagination && (count($this->_pagination) == 2);
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
     * @var Zend_Paginator
     */
    private $_paginator = null;

    /**
     * Set @see Zend_Paginator
     * @param Zend_Paginator $paginator
     */
    public function setPaginator(Zend_Paginator $paginator) {
        $this->_paginator = $paginator;
    }

    /**
     * Return @see Zend_Paginator
     * @return Zend_Paginator
     */
    public function getPaginator() {
        if (null === $this->_paginator) {
			require_once 'Zend/Paginator.php';

            $adapter = Zend_Paginator::INTERNAL_ADAPTER;
            $adaptable = $this->getAdapter()->getAdaptable();
            if ($adaptable instanceof Zend_Db_Table_Abstract) {
            	// hack, for Zend_Db_Table_Abstract pagination
            	Zend_Paginator::addAdapterPrefixPath('KontorX_Paginator_Adapter','KontorX/Paginator/Adapter/');
            	$adapter = 'DbTable';
            }
            $this->_paginator = Zend_Paginator::factory($adaptable, $adapter);
        }
        return $this->_paginator;
    }

    /**
     * Create @see Zend_Paginator object instance
     * @return Zend_Paginator
     */
    private function _createPaginator() {
        $paginator = $this->getPaginator();

        list($pageNumber, $itemCountPerPage) = $this->getPagination();
        $paginator->setCurrentPageNumber($pageNumber);
        $paginator->setItemCountPerPage($itemCountPerPage);

        return $paginator;
    }
    
    /**
     * @var KontorX_DataGrid_Renderer_Interface
     */
    protected $_renderer = 'KontorX_DataGrid_Renderer_HtmlTable';

    /**
     * @param KontorX_DataGrid_Renderer_Interface|string $renderer
     * @return KontorX_DataGrid
     */
    public function setRenderer($renderer) {
    	$this->_renderer = $renderer;
    	return $this;
    }

    /**
     * @return KontorX_DataGrid_Renderer_Interface
     */
    public function getRenderer() {
    	if (!$this->_renderer instanceof KontorX_DataGrid_Renderer_Interface) {
    		if (is_string($this->_renderer)) {
	    		if (!class_exists($this->_renderer)) {
	    			require_once 'Zend/Loader.php';
	    			Zend_Loader::loadClass($this->_renderer);
	    		}
	
	    		/* @var $renderer KontorX_DataGrid_Renderer_Interface */
	    		$this->_renderer = new $this->_renderer();
	    	} else {
	    		require_once 'KontorX/DataGrid/Exception.php';
	    		throw new KontorX_DataGrid_Exception(
	    				sprintf('Renderer "%s" is not instance of "KontorX_DataGrid_Renderer_Interface"',
	    						is_object($this->_renderer)
	    							? get_class($this->_renderer)
	    							: (string) $this->_renderer));
	    	}
    	}

    	$this->_renderer->setDataGrid($this);

    	return $this->_renderer;
    }
    
    /**
     * @param KontorX_DataGrid_Renderer_Interface $renderer
     * @return string
     */
    public function render(KontorX_DataGrid_Renderer_Interface $renderer = null) {
    	if (null !== $renderer) {
    		$this->setRenderer($renderer);
    	}

    	return $this->getRenderer();
    }

    /**
     * To string
     * @return string
     */
    public function __toString() {
        return $this->getRenderer()->render();
    }
}