<?php
require_once 'KontorX/DataGrid/Cell/Editable/Abstract.php';
/**
 * @author gabriel
 */
abstract class KontorX_DataGrid_Cell_Editable_Db_DbTable extends KontorX_DataGrid_Cell_Editable_Abstract {

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
}