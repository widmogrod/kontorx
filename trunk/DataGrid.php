<?php
/**
 * KontorX_DataGrid
 * 
 * @category 	KontorX
 * @package 	KontorX_DataGrid
 * @version 	0.5.1
 * @license		GNU GPL
 * @author 		Marcin `widmogror` Habryn, widmogrod@gmail.com
 */
class KontorX_DataGrid {
	
	/**
	 * Konstruktor
	 *
	 * @param KontorX_DataGrid_Adapter_Interface $adapter
	 */
	private function __construct(KontorX_DataGrid_Adapter_Interface $adapter) {
		$this->_adapter = $adapter;
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
			$adapter = new KontorX_DataGrid_Adapter_DbTable($data);
		} else
		if (is_array($data)) {
			// TODO ..
		} else {
			require_once 'KontorX/DataGrid/Exception.php';
			throw new KontorX_DataGrid_Exception("Data type is not suported");
		}

		$adapter->setData($data);
		$instance = new self($adapter);

		return $instance;
	}
	
	/**
	 * @var KontorX_DataGrid_Adapter_Interface
	 */
	private $_adapter = null;

	/**
	 * Return @see KontorX_DataGrid_Adapter_Interface
	 *
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
	 * @var array
	 */
	private $_order = array();
	
	/**
	 * Set order of column to display
	 *
	 * @param array $order
	 */
	public function setOrder(array $order) {
		$this->_order = $order;
	}

	/**
	 * Get order of column
	 *
	 * @return array
	 */
	public function getOrder() {
		return $this->_order;
	}

	/**
	 * Initialize a order of columns/filters ..
	 *
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

		if (count($array)) {
			$result += $array;
		}

		$this->_columns = $result;
	}

	/**
	 * @var array
	 */
	private $_values = array();
	
	/**
	 * Set array of values
	 * array(
	 * 	columns => ..
	 *  filters => ..
	 * )
	 *
	 * @param array $values
	 */
	public function setValues(array $values) {
		$this->_values = $values;
	}

	/**
	 * Return values
	 *
	 * @return array
	 */
	public function getValues() {
		return $this->_values;
	}

	/**
	 * Find filter/column and pas to it values
	 *
	 * @return void
	 */
	private function _noticeColumnAndFilterValues() {
//		$values = $this->getValues();
//		if (isset($values['columns'])) {
//			foreach ((array) $values['columns'] as $columnName => $values) {
//				if ($this->hasFilter($columnName)) {
//					$filter = $this->getFilter($columnName);
//					$filterName = $filter->getName();
//					if (isset($values[$filterName])) {
//						$filter->setValues((array) $values[$filterName]);
//					}
//				}
//			}
//		}
//		if (isset($values['filters'])) {
//			foreach ((array) $values['filters'] as $columnName => $values) {
//				if ($this->hasColumn($columnName)) {
//					$column = $this->getColumn($columnName);
//					$columnName = $filter->getName();
//					if (isset($values[$columnName])) {
//						$column->setValues((array) $values[$columnName]);
//					}
//				}
//			}
//		}
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

		// create filter
		if (isset($columnOptions['filter'])) {
			$filter = $this->_createFilter($columnName, (array) $columnOptions['filter']);
			$columnInstance->setFilter($filter);
		}
		// create row
		if (isset($columnOptions['row'])) {
			$row = $this->_createRow($columnName, (array) $columnOptions['row']);
			$columnInstance->setRow($row);
		}
		
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
	 * Return @see KontorX_DataGrid_Column_Interface or null if no exsists
	 *
	 * @param string $name
	 * @return KontorX_DataGrid_Column_Interface
	 */
	public function getColumn($name) {
		return $this->_columns[$name];
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
	 * Create filter object @see KontorX_DataGrid_Filter_Interface
	 *
	 * @param string $columnName
	 * @param array $options
	 * @return KontorX_DataGrid_Filter_Interface
	 */
	private function _createFilter($columnName, $options = null) {
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

		return $filterInstance;
	}

	/**
	 * Create row object @see KontorX_DataGrid_Row_Interface
	 *
	 * @param string $columnName
	 * @param array $options
	 * @return KontorX_DataGrid_Row_Interface
	 */
	private function _createRow($columnName, $options = null) {
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

		return $rowInstance;
	}
	
	/**
	 * Return array of @see KontorX_DataGrid_Filter_Interface
	 *
	 * @return array
	 */
	private function _getFilters() {
		$result = array();
		foreach ($this->getColumns() as $column) {
			$result[] = $column->getFilter();
		}
		return $result;
	}

	/**
	 * Notice filters to prepare adapter
	 * 
	 * @return void
	 */
	private function _noticeFilters(KontorX_DataGrid_Adapter_Interface $adapter) {
		foreach ($this->getColumns() as $column) {
			$filter = $column->getFilter();
			if ($filter instanceof KontorX_DataGrid_Filter_Interface) {
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
	 * 
	 * @return array
	 */
	public function getVars() {
		if (null === $this->_vars) {
			$adapter = $this->getAdapter();

			$this->_orderColumns();
			$this->_noticeFilters($adapter);
//			$this->_noticeColumnAndFilterValues();
			
			$columns = $this->getColumns();
			$adapter->setColumns($columns);

			if ($this->isPagination()) {
				list($pageNumber, $itemCountPerPage) = $this->getPagination();
				$adapter->setPagination($pageNumber, $itemCountPerPage);
			}

			$this->_vars = array(
				'columns' => $columns,
				'filters' => $this->_getFilters(),
				'rowset'  => $adapter->fetchData(),
				'paginator' => ($this->isPagination() ? $this->_createPaginator() : null)
			);
		}
		return $this->_vars;
	}

	/**
	 * Reset vars
	 * 
	 */
	public function resetVars() {
		$this->_vars = null;
	}
	
	/**
	 * Default name of partial file @see Zend_View_Helper_Partial
	 *
	 * @var string
	 */
	private $_defaultPartial = 'dataGrid.phtml';
	
	/**
	 * Set name of partial file @see Zend_View_Helper_Partial
	 *
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
	 *
	 * @return bool
	 */
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
}