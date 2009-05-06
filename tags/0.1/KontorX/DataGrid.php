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
    const DEFAULT_ROW_TYPE = 'Text';
    const DEFAULT_FILTER_TYPE = 'Text';
    const DEFAULT_COLUMN_TYPE = 'Text';

    /**
     * Konstruktor
     * @param KontorX_DataGrid_Adapter_Interface $adapter
     */
    private function __construct(KontorX_DataGrid_Adapter_Interface $adapter) {
        $this->_adapter = $adapter;
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
            require_once 'KontorX/DataGrid/Adapter/DbTable.php';
            $adapter = new KontorX_DataGrid_Adapter_DbTable($data);
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

        // $adapter->setData($data);
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

    const COLUMN = 'column';
    const ROW = 'row';
    const FILTER = 'filter';

    /**
     * @var array
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
            case self::ROW:
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
                case self::ROW:
                    $prefixSegment = 'DataGrid_Row';
                    $pathSegment   = 'DataGrid/Row';
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
            $type = self::DEFAULT_FILTER_TYPE;
        }

        // create column instance
        $filterClass = $this->getPluginLoader(self::FILTER)->load($type);
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
            $type = self::DEFAULT_ROW_TYPE;
        }

        // create column instance
        $rowClass = $this->getPluginLoader(self::ROW)->load($type);
        $rowInstance = new $rowClass($options);
        return $rowInstance;
    }

    /**
     * Return array of @see KontorX_DataGrid_Filter_Interface
     * @return array
     */
    private function _getFilters() {
        $result = array();
        foreach ($this->getColumns() as $column) {
            array_push($result, $column->getFilters());
        }
        return $result;
    }

    /**
     * Notice filters to prepare adapter
     * @return void
     */
    private function _noticeFilters(KontorX_DataGrid_Adapter_Interface $adapter) {
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
            $adapter = $this->getAdapter();

            $this->_noticeValues();
            $this->_noticeFilters($adapter);

            $this->_orderColumns();

            $columns = $this->getColumns();
            $adapter->setColumns($columns);

            if ($this->_isPagination()) {
                list($pageNumber, $itemCountPerPage) = $this->getPagination();
                $adapter->setPagination($pageNumber, $itemCountPerPage);
            }

            $this->_vars = array(
                'columns' => $columns,
                'filters' => $this->_getFilters(),
                'rowset'  => $adapter->fetchData(),
                'paginator' => ($this->_isPagination() ? $this->_createPaginator() : null),
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
     * Default name of partial file @see Zend_View_Helper_Partial
     * @var string
     */
    private $_defaultPartial = 'dataGrid.phtml';

    /**
     * Set name of partial file @see Zend_View_Helper_Partial
     * @var string
     */
    public function setDefaultPartial($partial) {
        $this->_defaultPartial = (string) $partial;
    }

    /**
     * Return name of partial file @see Zend_View_Helper_Partial
     */
    public function getDefaultPartial() {
        return $this->_defaultPartial;
    }

    /**
     * @var Zend_View_Interface
     */
    private $_view = null;

    /**
     * Ustawienie widoku
     * @param Zend_View_Interface $view
     */
    public function setView(Zend_View_Interface $view) {
        $this->_view = $view;
    }

    /**
     * Zwraca instancję widoku
     * @return Zend_View_Interface
     */
    public function getView() {
        if (null === $this->_view) {
            require_once 'Zend/View.php';
            $this->_view = new Zend_View();
        }
        return $this->_view;
    }

    /**
     * Renderowanie
     * @param Zend_View_Interface $view
     * @param string $partial
     * @return string
     */
    public function render(Zend_View_Interface $view = null, $partial = null) {
        if (null != $view) {
            $this->setView($view);
        }

        $view = $this->getView();

        if(!$view->getHelperPath('KontorX_View_Helper_')) {
        	$view->addHelperPath('KontorX/View/Helper', 'KontorX_View_Helper_');
        }

        // wywolanie helpera widoku @see KontorX_View_Helper_DataGrid
        return $view->dataGrid($this, $partial);
    }

    /**
     * To string
     * @return string
     */
    public function __toString() {
        return $this->render();
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
    private function _isPagination() {
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

            $data = $this->getAdapter();
            $this->_paginator = Zend_Paginator::factory($data);
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
}