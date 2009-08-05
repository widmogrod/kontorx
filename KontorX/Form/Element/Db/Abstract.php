<?php
/**
 * @author gabriel
 *
 */
class KontorX_Form_Element_Db_Abstract extends Zend_Form_Element_Multi {

	/**
	 * @var string
	 */
	protected $_optionKey = 'key';

	/**
	 * @param string $optionKey
	 * @return KontorX_Form_Element_Db_Abstract
	 */
	public function setOptionKey($optionKey) {
		$this->_optionKey = (string) $optionKey;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getOptionKey() {
		return $this->_optionKey;
	}
	
	/**
	 * @var string
	 */
	protected $_optionValue = 'value';

	/**
	 * @param string $optionValue
	 * @return KontorX_Form_Element_Db_Abstract
	 */
	public function setOptionValue($optionValue) {
		$this->_optionValue = (string) $optionValue;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getOptionValue() {
		return $this->_optionValue;
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
			$this->setTableCols($this->getName());
		}
		return $this->_tabelCols;
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
	 * @return Zend_Db_Statement_Interface
	 */
	protected function _query() {
		$db = $this->getDbAdapter();
		$name = $this->getTableName();
		$cols = $this->getTableCols();

		$select = new Zend_Db_Select($db);
		$stmt = $select->from($name, $cols)
					   ->query(Zend_Db::FETCH_ASSOC);
		return $stmt;
	}
	
	public function init() {
		$key = $this->getOptionKey();
		$val = $this->getOptionValue();

		$stmt = $this->_query();
		while ($row = $stmt->fetch()) {
			$this->addMultiOption($row[$key], $row[$val]);
		}
	}
}