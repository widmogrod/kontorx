<?php
/**
 * @author gabriel
 *
 */
class Promotor_Model_Abstract {
	/* Status */
	const SUCCESS = 'SUCCESS';
	const FAILURE = 'FAILURE';

	/**
	 * @var string
	 */
	protected $_dbTableClass = null;
	
	/**
	 * @var Zend_Db_Table_Abstract
	 */
	protected $_dbTable = null;
	
	/**
	 * @return Zend_Db_Table_Abstract
	 */
	public function getDbTable() {
		if (null === $this->_dbTable) {
			$this->_dbTable = new $this->_dbTableClass();
			if (!$this->_dbTable instanceof Zend_Db_Table_Abstract) {
				throw new Promotor_Model_Exception(sprintf('table class "%s" is not istantce of Zend_Db_Table_Abstract', $this->_dbTableClass));
			}
		}
		return $this->_dbTable;
	}

	/**
	 * @var string
	 */
	private $_status = null;
	
	/**
	 * @return string
	 */
	public function getStatus() {
		$status = $this->_status;
		$this->_status = null;
		return $status;
	}

	/**
	 * @param string $status
	 * @return KontorX_Controller_Action_Helper_Scaffold
	 */
	protected function _setStatus($status) {
		$this->_status = $status;
	}
}