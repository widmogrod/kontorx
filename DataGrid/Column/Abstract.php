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
	}

	/**
	 * Return class name without prefix
	 *
	 * @return string
	 */
	public function getName() {
		return end(explode('_',get_class($this)));
	}

	/**
	 * @var KontorX_DataGrid_Filter_Interface
	 */
	private $_filter = null;
	
	/**
	 * Set filter instance @see KontorX_DataGrid_Filter_Interface
	 *
	 * @param KontorX_DataGrid_Filter_Interface $filter
	 */
	public function setFilter(KontorX_DataGrid_Filter_Interface $filter) {
		$this->_filter = $filter;
	}
	
	/**
	 * Return filter instance @see KontorX_DataGrid_Filter_Interface
	 * 
	 * @return KontorX_DataGrid_Filter_Interface
	 */
	public function getFilter() {
		return $this->_filter;
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
	 * @var array
	 */
	private $_values = array();
	
	/**
	 * Values
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
	 * Return value
	 *
	 * @param string $name
	 * @param mixed $default
	 * @return mixed
	 */
	public function getValue($name, $default = null) {
		return array_key_exists($name, $this->_values)
			? $this->_values[$name] : $default;
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