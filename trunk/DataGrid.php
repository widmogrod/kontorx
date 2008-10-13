<?php
class KontorX_DataGrid {
	
	/**
	 * @var KontorX_DataGrid_Adapter_Interface
	 */
	private $_adapter = null;

	private function __construct(KontorX_DataGrid_Adapter_Interface $adapter, $rawData = null) {
		$this->_adapter = $adapter;
		$this->_rawData = $rawData;
	}

	/**
	 * Enter description here...
	 *
	 * @return KontorX_DataGrid_Adapter_Interface
	 */
	public function getAdapter() {
		return $this->_adapter;
	}

	private $_rawData = null;
	
	public function getRawData() {
		return $this->_rawData;
	}
	
	/**
	 * Setup @see KontorX_DataGrid with property data adapter
	 *
	 * @param mixed $data
	 * @param Zend_Config|array|null $options
	 * @return KontorX_DataGrid
	 */
	public static function factory($data, $options = null) {
		$instance = null;
		if ($data instanceof Zend_Db_Table_Abstract) {
			require_once 'KontorX/DataGrid/Adapter/DbTable.php';
			$instance = new self(new KontorX_DataGrid_Adapter_DbTable($data), $data);
		} else
		if (is_array($data)) {
			
		} else {
			require_once 'KontorX/DataGrid/Exception.php';
			throw new KontorX_DataGrid_Exception("Data type is not suported");
		}

		return $instance;
	}
	
	private $_enabledPagination = true;
	
	public function disablePagination() {
		$this->_enabledPagination = false;
	}
	
	public function enablePagination() {
		$this->_enabledPagination = true;
	}

	public function isPagination() {
		return $this->_enabledPagination && (count($this->_pagination) == 2);
	}

	/**
	 * @var array
	 */
	private $_pagination = array();
	
	/**
	 * Set pagination parameters
	 *
	 * @param integer $limit
	 * @param integer $rowCount
	 */
	public function setPagination($pageNumber, $itemCountPerPage) {
		$this->_pagination = array($pageNumber, $itemCountPerPage);
	}

	/**
	 * Return pagination controls
	 *
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
	 *
	 * @param Zend_Paginator $paginator
	 */
	public function setPaginator(Zend_Paginator $paginator) {
		$this->_paginator = $paginator;
	}

	/**
	 * Return @see Zend_Paginator
	 *
	 * @return Zend_Paginator
	 */
	public function getPaginator() {
		if (null === $this->_paginator) {
			require_once 'Zend/Paginator.php';
			
			$data = $this->getRawData();
			$this->_paginator = Zend_Paginator::factory($data);
		}
		return $this->_paginator;
	}
	
	/**
	 * Create @see Zend_Paginator object instance
	 *
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
	 * @var array
	 */
	private $_vars = null;
	
	/**
	 * Zwraca zmienne dla widoku
	 * 
	 * Tak naprawdę tutaj zaczyna się całaprzygoda kompilacji ..
	 *
	 * @return array
	 */
	public function getVars() {
		if (null === $this->_vars) {
			$adapter = $this->getAdapter();

			$this->_startModelOrder();

			$columns = $this->getColumns();
			$filters = $this->getFilters();
			$rows 	 = $this->getRows();

			$adapter->setColumns($columns);
			$adapter->setFilters($filters);
			$adapter->setRows($rows);

			if ($this->isPagination()) {
				list($pageNumber, $itemCountPerPage) = $this->getPagination();
				$adapter->setPagination($pageNumber, $itemCountPerPage);
			}

			$this->_vars = array(
				'columns' => $columns,
				'filters' => $filters,
				'rowset'  => $adapter->fetchData(),
				'paginator' => ($this->isPagination() ? $this->_createPaginator() : null)
			);
		}
		return $this->_vars;
	}

	/**
	 * Czyści zmienne
	 * @return void
	 */
	public function resetVars() {
		$this->_vars = null;
	}
	
	/**
	 * Domyślna nazwa pliku dla @see Zend_View_Helper_Partial
	 *
	 * @var string
	 */
	private $_defaultPartial = 'dataGrid.phtml';
	
	/**
	 * Ustawia nazwę pliku dla @see Zend_View_Helper_Partial
	 *
	 * @var string
	 */
	public function setDefaultPartial($partial) {
		$this->_defaultPartial = (string) $partial;
	}
	
	/**
	 * Zwraca nazwę pliku dla @see Zend_View_Helper_Partial
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
	 *
	 * @param Zend_View_Interface $view
	 */
	public function setView(Zend_View_Interface $view) {
		$this->_view = $view;
	}
	
	/**
	 * Zwraca instancję widoku
	 *
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
	 * 
	 * @param Zend_View_Interface $view
	 */
	public function render(Zend_View_Interface $view = null) {
		if (null != $view) {
			$this->setView($view);
		}

		$view = $this->getView();

		// wywolanie helpera widoku @see KontorX_View_Helper_DataGrid
		return $view->dataGrid($this);
	}

	/**
	 * To string
	 *
	 * @return string
	 */
	public function __toString() {
		return $this->render();
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
	 *
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
	 * Add column model column/filter/row specyfication
	 *
	 * @param string $columnName
	 * @param Zend_Config|array $options
	 */
	public function addModel($columnName, $options) {
		if ($options instanceof Zend_Config) {
			$options = $options->toArray();
		}
		$options = (array) $options;

		if (isset($options['column'])) {
			$this->addColumn($columnName, $options['column']);
		}
		if (isset($options['filter'])) {
			$this->addFilter($columnName, $options['filter']);
		}
//		if (isset($options['row'])) {
//			$this->addRow($columnName, $options['row']);
//		}
	}

	public function resetModel() {
		$this->resetColumns();
		$this->resetFilters();
		$this->resetRows();
		$this->resetVars();
	}

	/**
	 * @var array
	 */
	private $_order = array();
	
	/**
	 * Set order of column to display
	 *
	 * @param array $order
	 */
	public function setModelOrder(array $order) {
		$this->_order = $order;
	}

	/**
	 * Get order of column
	 *
	 * @return array
	 */
	public function getModelOrder() {
		return $this->_order;
	}

	/**
	 * Initialize a order of columns/filters ..
	 *
	 */
	private function _startModelOrder() {
		// minimum 2 values!
		if (count($this->_order) > 1) {
			if (count($this->_columns) > 1) {
				$this->_columns = $this->_orderArray($this->_columns);
			}
			if (count($this->_filters) > 1) {
				$this->_filters = $this->_orderArray($this->_filters);
			}
		}
	}

	private function _orderArray(array $array) {
		$result = array();
		foreach ($this->_order as $columnName) {
			if (array_key_exists($columnName, $array)) {
				$result[$columnName] = $array[$columnName];
				unset($array[$columnName]);
			}
		}

		// dodajemy kolmny, ktore nie zostaly uwzglednione w `$this->_order`
		if (count($array)) {
			$result += $array;
		}

		return $result;
	}
	
	
	/**
	 * @var array
	 */
	private $_columns = array();
	
	/**
	 * Dodaje kolumnę
	 *
	 * @param string $columnName
	 * @param array $options
	 */
	public function addColumn($columnName, $options = null) {
		// default ..
		$columnType 	= 'Text';
		$columnOptions  = array(
			'name' => $columnName
		);

		if (is_string($options)) {
			$columnType = $options;
		} else
		if ($options instanceof Zend_Config) {
			$columnOptions = $options->toArray();
		} else
		if (is_array($options)){
			$columnOptions = $options;
		}
		unset($options);

		if (isset($columnOptions['type'])) {
			$columnType = $columnOptions['type'];
		}
		if (!isset($columnOptions['columnName'])) {
			$columnOptions['columnName'] = $columnName;
		}

		// create column instance
		$columnClass = $this->getPluginLoader(self::COLUMN)->load($columnType);
		$columnInstance = new $columnClass($columnOptions);

		$this->_columns[$columnName] = $columnInstance;
	}

	/**
	 * Add multi columns
	 *
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
	 *
	 * @param string $name
	 * @return bool
	 */
	public function hasColumn($name) {
		return array_key_exists($name, $this->_columns);
	}
	
	/**
	 * Reset column if any was exsists before and add multi columns
	 *
	 * @param array $columns
	 */
	public function setColumns(array $columns) {
		$this->resetColumns();
		$this->addColumns($columns);
	}
	
	/**
	 * Return columns
	 *
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
	 * Return array of columns Keys/Names
	 *
	 * @return array
	 */
	public function getColumnsKeys() {
		return array_keys($this->_columns);
	}

	/**
	 * @var array
	 */
	private $_filters = array();
	
	/**
	 * Add filter
	 *
	 * @param string $columnName
	 * @param array $options
	 */
	public function addFilter($columnName, $options = null) {
		$filterType 	= 'Text';
		$filterOptions  = array();

		if (is_string($options)) {
			$filterType = $options;
		} else
		if ($options instanceof Zend_Config) {
			$filterOptions = $options->toArray();
		} else
		if (is_array($options)){
			$filterOptions = $options;
		}
		unset($options);

		if (isset($filterOptions['type'])) {
			$filterType = $filterOptions['type'];
		}
		if (!isset($filterOptions['columnName'])) {
			$filterOptions['columnName'] = $columnName;
		}
		
		// create column instance
		$filterClass = $this->getPluginLoader(self::FILTER)->load($filterType);
		$filterInstance = new $filterClass($filterOptions);

		$this->_filters[$filterName] = $filterInstance;
	}

	/**
	 * Add multi filters
	 *
	 * @param array $columns
	 */
	public function addFilters(array $filters) {
		foreach ($filters as $key => $value) {
			if (is_array($value)) {
				$this->addFilter($key, $value);
			} else {
				$this->addFilter($value);
			}
		}
	}

	/**
	 * Return true if filter was added otherwey false
	 *
	 * @param string $name
	 * @return bool
	 */
	public function hasFilter($name) {
		return array_key_exists($name, $this->_filters);
	}

	/**
	 * Reset filter if any was exsists before and add multi filters
	 *
	 * @param array $filters
	 */
	public function setFilters(array $filters) {
		$this->resetFilters();
		$this->addFilters($filters);
	}
	
	/**
	 * Return filters
	 *
	 * @return array
	 */
	public function getFilters() {
		return $this->_filters;
	}

	/**
	 * Reset filters
	 */
	public function resetFilters() {
		$this->_filters = null;
		$this->_filters = array();
	}

	/**
	 * Return array of filters Keys/Names
	 *
	 * @return array
	 */
	public function getFiltersKeys() {
		return array_keys($this->_filters);
	}

	/**
	 * @var array
	 */
	private $_rows = array();
	
	/**
	 * Add row
	 *
	 * @param string $columnName
	 * @param array $options
	 */
	public function addRow($columnName, $options = null) {
		$rowType 	= 'Text';
		$rowOptions  = array();

		if (is_string($options)) {
			$rowType = $options;
		} else
		if ($options instanceof Zend_Config) {
			$rowOptions = $options->toArray();
		} else
		if (is_array($options)){
			$rowOptions = $options;
		}
		unset($options);

		if (isset($rowOptions['type'])) {
			$rowType = $rowOptions['type'];
		}
		if (!isset($rowOptions['columnName'])) {
			$rowOptions['columnName'] = $columnName;
		}
		
		// create column instance
		$rowClass = $this->getPluginLoader(self::ROW)->load($rowType);
		$rowInstance = new $rowClass($rowOptions);

		$this->_rows[$columnName] = $rowInstance;
	}

	/**
	 * Add multi rows
	 *
	 * @param array $rows
	 */
	public function addRows(array $rows) {
		foreach ($rows as $key => $value) {
			if (is_array($value)) {
				$this->addRow($key, $value);
			} else {
				$this->addRow($value);
			}
		}
	}

	/**
	 * Return true if row was added otherwey false
	 *
	 * @param string $name
	 * @return bool
	 */
	public function hasRow($name) {
		return array_key_exists($name, $this->_rows);
	}

	/**
	 * Reset rows if any was exsists before and add multi rows
	 *
	 * @param array $rows
	 */
	public function setRows(array $rows) {
		$this->resetRows();
		$this->addRows($rows);
	}
	
	/**
	 * Return columns
	 *
	 * @return array
	 */
	public function getRows() {
		return $this->_rows;
	}

	/**
	 * Reset rows
	 */
	public function resetRows() {
		$this->_rows = null;
		$this->_rows = array();
	}

	/**
	 * Return array of columns Keys/Names
	 *
	 * @return array
	 */
	public function getRowsKeys() {
		return array_keys($this->_rows);
	}
}