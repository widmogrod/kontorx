<?php
require_once 'KontorX/DataGrid/Row/Editable/Abstract.php';
/**
 * @author gabriel
 */
abstract class KontorX_DataGrid_Row_Editable_Db_Adapter extends KontorX_DataGrid_Row_Editable_Abstract {

	/**
	 * @var string
	 */
	protected $_tabelCols;

	/**
	 * @param string $cols
	 * @return KontorX_Form_Element_Db_Abstract
	 */
	public function setTableCols($cols) {
		$this->_tabelCols = (array) $cols;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getTableCols() {
		if (null === $this->_tabelCols) {
			$this->setTableCols($this->getColumnName());
		}
		return $this->_tabelCols;
	}

	/**
	 * @var string
	 */
	protected $_tabelName;

	/**
	 * @param string $name
	 * @return KontorX_Form_Element_Db_Abstract
	 */
	public function setTableName($name) {
		$this->_tabelName = (string) $name;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getTableName() {
		if (null === $this->_tabelName) {
			throw new Zend_Form_Element_Exception('table name is not set');
		}
		return $this->_tabelName;
	}

	/**
	 * @var Zend_Db_Adapter_Abstract
	 */
	protected $_dbAdapter;
	
	/**
	 * @param Zend_Db_Adapter_Abstract $adapter
	 * @return KontorX_Form_Element_Db_Abstract
	 */
	public function setDbAdapter(Zend_Db_Adapter_Abstract $adapter) {
		$this->_dbAdapter = Zend_Db_Adapter_Abstract;
		return $this;
	}

	/**
	 * @return Zend_Db_Adapter_Abstract
	 */
	public function getDbAdapter() {
		if (null === $this->_dbAdapter) {
			$this->_dbAdapter = Zend_Db_Table_Abstract::getDefaultAdapter();
		}
		return $this->_dbAdapter;
	}

	/**
	 * @var array
	 */
	protected static $_fetched = array();
	
	/**
	 * @return Zend_Db_Statement_Interface
	 */
	protected function _fetchAll() {
		$columnName = $this->getColumnName();
		if (array_key_exists($columnName, self::$_fetched)) {
			return self::$_fetched[$columnName];
		}

		$db = $this->getDbAdapter();
		$name = $this->getTableName();
		$cols = $this->getTableCols();

		$select = new Zend_Db_Select($db);
		$stmt = $select->from($name, $cols)
					   ->query(Zend_Db::FETCH_ASSOC);

		$result = array();
		while ($row = $stmt->fetch()) {
			$result = $this->_onFetch($row, $result);
		}

		return self::$_fetched[$columnName] = $result;
	}

	/**
	 * @param array $row
	 * @param array $rowset
	 * @return array 
	 */
	protected function _onFetch(array $row, array $rowset) {
		$rowset[] = $row;
		return $rowset;
	}
}