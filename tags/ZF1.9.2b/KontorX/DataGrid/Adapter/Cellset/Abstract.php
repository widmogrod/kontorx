<?php
require_once 'KontorX/DataGrid/Adapter/Cellset/Interface.php';
abstract class KontorX_DataGrid_Adapter_Cellset_Abstract implements KontorX_DataGrid_Adapter_Cellset_Interface {
	/**
	 * @var array of @see KontorX_DataGrid_Cell_Interface
	 */
	protected $_cells = array();

	/**
	 * @var integer
	 */
	protected $_pointer = 0;
	
	/**
	 * @var integer
	 */
	protected $_count = 0;

	/**
	 * @var KontorX_DataGrid_Cell_Interface
	 */
	protected $_groupCell = null;
	
	/**
	 * @param KontorX_DataGrid_Cell_Interface $cell
	 * @return void
	 */
	public function addCell($cell) {
		if (is_object($cell) && !$cell instanceof KontorX_DataGrid_Cell_Interface) {
			require_once 'KontorX/DataGrid/Exception.php';
			$message = sprintf('Row object "%s" is not instance of KontorX_DataGrid_Cell_Interface', get_class($cell));
			throw new KontorX_DataGrid_Exception($message);
		}

		$this->_cells[$this->_count++] = $cell;
	}

	/**
	 * @param KontorX_DataGrid_Cell_Interface|mixed $cell
	 * @return void
	 */
	public function setGroupCell($cell) {
		if (is_object($cell) && !$cell instanceof KontorX_DataGrid_Cell_Interface) {
			require_once 'KontorX/DataGrid/Exception.php';
			$message = sprintf('Row object "%s" is not instance of KontorX_DataGrid_Cell_Interface', get_class($cell));
			throw new KontorX_DataGrid_Exception($message);
		}
		$this->_groupCell = $cell;
	}

	/**
	 * @return KontorX_DataGrid_Cell_Interface
	 */
	public function getGroupCell() {
		return $this->_groupCell;
	}

	/**
	 * @return bool
	 */
	public function hasGroupedCell() {
		return is_null($this->_groupCell) === false;
	}
	
	/**
	 * @return KontorX_DataGrid_Cell_Interface
	 */
	public function current() {
		return $this->_cells[$this->_pointer];
	}

	/**
	 * @return void
	 */
	public function next() {
		++$this->_pointer;
	}

	/**
	 * @return void
	 */
	public function rewind() {
		$this->_pointer = 0;
	}

	/**
	 * @return integer
	 */
	public function key() {
		return $this->_pointer;
	}
	
	/**
	 * @return bool
	 */
	public function valid() {
		return $this->_pointer < $this->_count;
	}

	/**
	 * @return integer
	 */
	public function count() {
		return $this->_count;
	}
	
	public function toArray() {
		$result = array();
		foreach ($this as $i => $cell) {
			/* @var $cell KontorX_DataGrid_Cell_Interface */
			$name = $cell->getColumn()->getColumnName();
			$result[$name] = $cell->render();
		}
		return $result;
	}
}