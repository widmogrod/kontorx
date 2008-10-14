<?php
require_once 'KontorX/DataGrid/Row/Interface.php';

abstract class KontorX_DataGrid_Row_Abstract implements KontorX_DataGrid_Row_Interface {

	/**
	 * Constructor
	 *
	 * @param array $options
	 */
	public function __construct(array $options = null) {
		if (null != $options) {
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
	 * @var mixed
	 */
	private $_data = null;
	
	/**
	 * Set data to rendered
	 *
	 * @param mixed $data
	 * @return void
	 */
	public function setData($data) {
		$this->_data = $data;
	}

	/**
	 * Return data
	 *
	 * @return mixed
	 */
	public function getData($key = null) {
		if (null !== $key) {
			return (array_key_exists($key, $this->_data))
				? $this->_data[$key] : null;
		}
		return $this->_data;
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
	 * To string
	 *
	 * @return string
	 */
	public function __toString() {
		return $this->render();
	}
}