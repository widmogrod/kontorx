<?php
require_once 'KontorX/DataGrid/Column/Interface.php';

abstract class KontorX_DataGrid_Column_Abstract implements KontorX_DataGrid_Column_Interface {

	/**
	 * @var array
	 */
	private $_options = array();
	
	/**
	 * Konstruktor
	 *
	 * @param array $options
	 */
	public function __construct(array $options = null) {
		if (null != $options) {
			if (isset($options['name'])) {
				$this->setName($options['name']);
			}
			if (isset($options['columnName'])) {
				$this->setColumnName($options['columnName']);
			}
			$this->setOptions($options);
		}
	}

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
	
	/**
	 * @var string
	 */
	private $_name = null;

	/**
	 * Ustawia pełnowymiarową nazwę kolumny
	 *
	 * @param string $name
	 */
	public function setName($name) {
		$this->_name = (string) $name;
	}
	
	/**
	 * Zwraca pełnowymiarową nazwę kolumny
	 *
	 * @return string
	 */
	public function getName() {
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
		$this->_columnName = null;
	}

	/**
	 * Get column name
	 *
	 * @return string
	 */
	public function getColumnName() {
		return $this->_columnName;
	}
}