<?php
class KontorX_Controller_Action_Helper_Scaffold extends Zend_Controller_Action_Helper_Abstract {

	/* Action */
	/*const READ = 'read';*/
	const CREATE = 'create';
	const UPDATE = 'update';
	const DELETE = 'delete';
	
	/* Status */
	const NO_POST_DATA = 'NO_POST_DATA';
	const NO_EXSISTS = 'NO_EXSISTS';
	const NO_VALID = 'NO_VALID';
	const SUCCESS = 'SUCCESS';
	const FAILURE = 'FAILURE';

	/**
	 * @var array
	 */
	protected $_hooks = array(
		self::CREATE => array()
	);
	
	/**
	 * @param string $type
	 * @return void
	 * @throws Zend_Controller_Action_Exception
	 */
	public function run($type) {
		$type = strtolower($type);
		switch ($type) {
			case self::CREATE: $this->_create(); break;
			case self::UPDATE: $this->_update(); break;
			case self::DELETE: $this->_delete(); break;

			default:
				require_once 'Zend/Controller/Action/Exception.php';
				throw new Zend_Controller_Action_Exception(sprintf('Scaffold type "%s" do not exsists', $type));
		}
	}

	/**
	 * @var Zend_Form
	 */
	protected $_form = null;
	
	/**
	 * @param Zend_Form $form
	 * @return KontorX_Controller_Action_Helper_Scaffold
	 */
	public function setForm(Zend_Form $form) {
		$this->_form = $form;
		return $this;
	}

	/**
	 * @return Zend_Form
	 * @throws Zend_Controller_Action_Exception
	 */
	public function getForm() {
		if (null === $this->_form) {
			require_once 'Zend/Controller/Action/Exception.php';
			throw new Zend_Controller_Action_Exception('Zend_Form is not set');
		}
		return $this->_form;
	}

	/**
	 * @var Zend_Db_Table_Abstract
	 */
	protected $_dbTable = null;
	
	/**
	 * @param Zend_Db_Table_Abstract $table
	 * @return KontorX_Controller_Action_Helper_Scaffold
	 */
	public function setDbTable(Zend_Db_Table_Abstract $table) {
		$this->_dbTable = $table;
		return $this;
	}

	/**
	 * @return Zend_Db_Table_Abstract
	 * @throws Zend_Controller_Action_Exception
	 */
	public function getDbTable() {
		if (null === $this->_dbTable) {
			require_once 'Zend/Controller/Action/Exception.php';
			throw new Zend_Controller_Action_Exception('Zend_Db_Table_Abstract is not set');
		}
		return $this->_dbTable;
	}

	/**
	 * @var array
	 */
	protected $_rowPK = null;
	
	/**
	 * @param array|string|int $rowPK
	 * @return KontorX_Controller_Action_Helper_Scaffold
	 */
	public function setRowPK($rowPK) {
		$this->_rowPK = (array) $rowPK;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function hasRowPK() {
		return is_array($this->_rowPK);
	}
	
	/**
	 * @return Zend_Form
	 * @throws Zend_Controller_Action_Exception
	 */
	public function getRowPK() {
		if (null === $this->_rowPK) {
			require_once 'Zend/Controller/Action/Exception.php';
			throw new Zend_Controller_Action_Exception('primary key data for find row is not set');
		}
		
		$dbTable = $this->getDbTable();
		$primaryKey = $dbTable->info(Zend_Db_Table::PRIMARY);

		return array_intersect_key($this->_rowPK, array_flip($primaryKey));
	}
	
	/**
	 * @var string
	 */
	private $_status = null;
	
	/**
	 * @return string
	 */
	public function getStatus() {
		return $this->_status;
	}

	/**
	 * @param string $status
	 * @return void
	 */
	protected function _setStatus($status) {
		$this->_status = $status;
	}

	/**
	 * @var mixed
	 */
	private $_result = null;
	
	/**
	 * @return string
	 */
	public function getResult() {
		return $this->_result;
	}

	/**
	 * @param string $result
	 * @return KontorX_Controller_Action_Helper_Scaffold
	 */
	protected function _setResult($result) {
		$this->_result = $result;
	}
	
	/**
	 * @var bool
	 */
	private $_suppressArrayNotation = false;
	
	/**
	 * @param bool $flag
	 * @return KontorX_Controller_Action_Helper_Scaffold
	 */
	public function setSuppressArrayNotation($flag = true) {
		$this->_suppressArrayNotation = (bool) $flag;
		return $this;
	}

	/**
	 * default C(R)UD .. 
	 */
	
	protected function _create() {
		$rq = $this->getRequest();

		$table = $this->getDbTable();
		$row = $table->createRow();
		
		// Szukaj rodzica dla zagnieżdzonych rekordów
		if ($this->hasRowPK()) {
			$rowset = call_user_func_array(array($table,'find'), $this->getRowPK());
			if (!$rowset->count()) {
				$this->_setStatus(self::NO_EXSISTS);
				return;
			}

			if ($row instanceof KontorX_Db_Table_Tree_Row_Interface) {
				$row->setParentRow($rowset->current());
			}
		}

		$form = $this->getForm();
		if (!$rq->isPost()) {
			$form->setDefaults($row->toArray());
			$this->_setStatus(self::NO_POST_DATA);
			return;
		}

		if (!$form->isValid($rq->getPost())) {
			$this->_setStatus(self::NO_VALID);
			return;
		}
		
		$row->setFromArray($form->getValues($this->_suppressArrayNotation));

		try {
			$result = $row->save();
			$this->_setResult($result);
			$this->_setStatus(self::SUCCESS);
		} catch(Zend_Db_Table_Exception $e) {
			$this->_setResult($e);
			$this->_setStatus(self::FAILURE);
		} catch (Zend_Db_Statement_Exception $e) {
			$this->_setResult($e);
			$this->_setStatus(self::FAILURE);
		}
	}
	
	protected function _update() {
		$rq = $this->getRequest();

		$pk = $this->getRowPK();
		$table = $this->getDbTable();

		$rowset = call_user_func_array(array($table,'find'), $pk);
		if (!$rowset->count()) {
			$this->_setStatus(self::NO_EXSISTS);
			return;
		}

		$row = $rowset->current();

		$form = $this->getForm();
		if (!$rq->isPost()) {
			$form->setDefaults($row->toArray());
			$this->_setStatus(self::NO_POST_DATA);
			return;
		}

		if (!$form->isValid($rq->getPost())) {
			$this->_setStatus(self::NO_VALID);
			return;
		}

		$row->setFromArray($form->getValues($this->_suppressArrayNotation));
		
		try {
			$result = $row->save();
			$this->_setResult($result);
			$this->_setStatus(self::SUCCESS);
		} catch(Zend_Db_Table_Exception $e) {
			$this->_setResult($e);
			$this->_setStatus(self::FAILURE);
		} catch (Zend_Db_Statement_Exception $e) {
			$this->_setResult($e);
			$this->_setStatus(self::FAILURE);
		}
	}
	
	protected function _delete() {
		$rq = $this->getRequest();
		$table = $this->getDbTable();
		try {
			$rowset = call_user_func_array(array($table,'find'), $this->getRowPK());
			if (!$rowset->count()) {
				$this->_setStatus(self::NO_EXSISTS);
				return;
			}
		} catch(Zend_Db_Table_Exception $e) {
			$this->_setResult($e);
			$this->_setStatus(self::NO_EXSISTS);
		}	
			

		$row = $rowset->current();

		$form = $this->getForm();
		if (!$rq->isPost()) {
			$form->setDefaults($row->toArray());
			$this->_setStatus(self::NO_POST_DATA);
			return;
		}

		if (!$form->isValid($rq->getPost())) {
			$this->_setStatus(self::NO_VALID);
			return;
		}

		$row->setFromArray($form->getValues());

		try {
			$result = $row->delete();
			$this->_setResult($result);
			$this->_setStatus(self::SUCCESS);
		} catch(Zend_Db_Table_Exception $e) {
			$this->_setResult($e);
			$this->_setStatus(self::FAILURE);
		} catch (Zend_Db_Statement_Exception $e) {
			$this->_setResult($e);
			$this->_setStatus(self::FAILURE);
		}
	}
}