<?php
/**
 * @author gabriel
 *
 */
class KontorX_Form_Element_Db_Table_Abstract extends Zend_Form_Element_Multi {

	/**
	 * @var string
	 */
	protected $_optionKey = 'key';

	/**
	 * @param string $optionKey
	 * @return KontorX_Form_Element_Db_Table_Abstract
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
	 * @return KontorX_Form_Element_Db_Table_Abstract
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
	 * @var Zend_Db_Adapter_Abstract
	 */
	protected $_dbTable;
	
	/**
	 * @param Zend_Db_Table_Abstract|string $table
	 * @return KontorX_Form_Element_Db_Table_Abstract
	 */
	public function setDbTable($table) {
		$this->_dbTable = $table;
		return $this;
	}

	/**
	 * @return Zend_Db_Table_Abstract
	 * @throws KontorX_Exception
	 */
	public function getDbTable() {
		if (null === $this->_dbTable) {
			require_once 'KontorX/Exception.php';
			throw new KontorX_Exception('Zend_Db_Table is not set');
		}
		if (is_string($this->_dbTable)) {
			if (!class_exists($this->_dbTable)) {
				require_once 'Zend/Loader.php';
				Zend_Loader::loadClass($this->_dbTable);
			}

			$this->_dbTable = new $this->_dbTable();
		}
		if (!$this->_dbTable instanceof Zend_Db_Table_Abstract) {
			require_once 'KontorX/Exception.php';
			throw new KontorX_Exception('dbTable is not instance of Zend_Db_Table_Abstract');
		}
		return $this->_dbTable;
	}

	/**
	 * @var bool
	 */
	protected $_firstNull = true;
	
	/**
	 * @return KontorX_Form_Element_Db_Table_Abstract
	 */
	public function setFirstNull($flag = true) {
		$this->_firstNull = (bool) $flag;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function isFirstNull() {
		return $this->_firstNull;
	}

	public function init() {
		$table = $this->getDbTable();
		$rowset = $table->fetchAll();

		$this->setDisableTranslator(true);
		$this->setRegisterInArrayValidator(false);

		$this->options['rowset'] = $rowset;
		$this->options['valueCol'] = $this->getOptionKey();
		$this->options['labelCol'] = $this->getOptionValue();

		$this->setAttrib('firstNull', $this->_firstNull);
	}
}