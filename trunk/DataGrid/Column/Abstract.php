<?php
require_once 'KontorX/DataGrid/Column/Interface.php';

abstract class KontorX_DataGrid_Column_Abstract implements KontorX_DataGrid_Column_Interface {

	/**
	 * Konstruktor
	 *
	 * @param array $options
	 */
	public function __construct(array $options = null) {
		if (null != $options) {
			if (isset($options['name'])) {
				$this->setColumnMainName($options['name']);
			}
			if (isset($options['columnName'])) {
				$this->setColumnName($options['columnName']);
			}
			$this->setOptions($options);
		}
		$this->_init();
	}

	/**
	 * Return options key => value
	 * @return string
	 */
	public function __get($name) {
		return array_key_exists($name, $this->_options)
			? $this->_options[$name] : null;
	}
	
	/**
	 * Initialize class .. specialization purpose ..
	 *
	 * @return void
	 */
	protected function _init() {}
	
	/**
	 * Setup values
	 *
	 * @return void
	 */
	protected function _setupValues() {}
	
	/**
	 * Return class name without prefix
	 *
	 * @return string
	 */
	public function getName() {
		return end(explode('_',get_class($this)));
	}

	/**
	 * @var arary
	 */
	private $_filters = array();
	
	/**
	 * Add filter instance @see KontorX_DataGrid_Filter_Interface
	 *
	 * @param KontorX_DataGrid_Filter_Interface $filter
	 */
	public function addFilter(KontorX_DataGrid_Filter_Interface $filter) {
		$this->_filters[] = $filter;
	}
	
	/**
	 * Return array of filter objects @see KontorX_DataGrid_Filter_Interface
	 * 
	 * @return array
	 */
	public function getFilters() {
		return $this->_filters;
	}

	/**
	 * @var KontorX_DataGrid_Row_Interface
	 */
	private $_row = null;

	/**
	 * Set filter instance @see KontorX_DataGrid_Row_Interface
	 *
	 * @param KontorX_DataGrid_Row_Interface $filter
	 */
	public function setRow(KontorX_DataGrid_Row_Interface $row) {
		$this->_row = $row;
	}

	/**
	 * Return filter instance @see KontorX_DataGrid_Filter_Interface
	 * 
	 * @return KontorX_DataGrid_Row_Interface
	 */
	public function getRow() {
		return $this->_row;
	}
	
	/**
	 * @var Zend_Config
	 */
	private $_values = null;
	
	/**
	 * Values
	 *
	 * @param array $values
	 */
	public function setValues(Zend_Config $values) {
		$this->_values = $values;
		// TODO To jest w tej chwili testowane
		// a idea to zeby vartości filtrów zawsze były zaktualizowane!
		$this->_setupValues();
	}
	
	/**
	 * Return values
	 *
	 * @return Zend_Config
	 */
	public function getValues() {
		return $this->_values;
	}
	
	/**
	 * @var string
	 */
	private $_name = null;

	/**
	 * Ustawia pełnowymiarową nazwę kolumny
	 *
	 * @param string $name
	 */
	public function setColumnMainName($name) {
		$this->_name = (string) $name;
	}
	
	/**
	 * Zwraca pełnowymiarową nazwę kolumny
	 *
	 * @return string
	 */
	public function getColumnMainName() {
		return $this->_name;
	}

	/**
	 * @var string
	 */
	private $_columnName = null;

	/**
	 * Set column name
	 * @return void
	 */
	public function setColumnName($name) {
		$this->_columnName = $name;
	}

	/**
	 * Get column name
	 *
	 * @return string
	 */
	public function getColumnName() {
		return $this->_columnName;
	}
	
	/**
	 * @var array
	 */
	private $_options = array();
	
	/**
	 * Set options
	 *
	 * @param array $options
	 */
	public function setOptions(array $options) {
		$this->_options = $options;
	}
	
	/**
	 * Return options
	 *
	 * @return array
	 */
	public function getOptions() {
		return $this->_options;
	}
	
	/**
	 * To string
	 *
	 * @return string
	 */
	public function __toString() {
		return $this->render();
	}
}