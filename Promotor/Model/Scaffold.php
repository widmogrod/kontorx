<?php
/**
 * @author gabriel
 *
 */
class Promotor_Model_Scaffold extends Promotor_Model_Abstract {

	/**
	 * @var array
	 */
	protected $_observers = array();

	/**
	 * Powiadamianie obserwatorów zdefiniowanych dla określonej akcji..
	 * 
	 * @param string|bool $name
	 * @return void
	 */
	protected function _noticeObserver($name) {
		if (!isset($this->_observers[$name])) {
			return false;
		}

		if (true === $this->_observers[$name]) {
			$prefix = strtolower(get_class($this));
			$name = rtrim($prefix, '_') . '_' . ltrim($name, '_'); 
		} else
		if (!is_string($this->_observers[$name])) {
			return;
		}

		// notify observers
		$manager = Promotor_Observable_Manager::getInstance();
		$list = $manager->notify($name);

		$this->_addMessages($list->getMessages());
	}
	
	/**
	 * @return Zend_Db_Table_Select
	 */
	public function selectList() {
		return $this->getDbTable()->select();
	}

	/**
	 * @param mixed $primaryKey
	 * @param bool $createIfNotExsists
	 * @return Zend_Db_Table_Row_Abstract|null
	 */
	public function findByPK ($primaryKey, $createIfNotExsists = false) {
		$table = $this->getDbTable();
		
		$rowset = $table->find($primaryKey);
		if (count($rowset)) {
			return $rowset->current();
		}

		if ($createIfNotExsists) {
			return $table->createRow();
		}
		
		return null;
	}
	
	/**
	 * Create/Update row
	 * 
	 * @param array $data
	 * @param Zend_Db_Table_Row_Abstract|array|int $row
	 * @return Zend_Db_Table_Row_Abstract
	 */
	public function save(array $data, $row = null) {
		if (null === $row) {
			$row = $this->getDbTable()->createRow();
		}
		if (is_numeric($row) || is_array($row)) {
			$row = $this->getDbTable()->find($row)->current();
			if (null === $row) {
				$message = 'do not find row by primaryKey';
				$message = sprintf('%s (%s::%s)"', $message, get_class($this), __METHOD__);
				$this->_addMessage($message);
				$this->_setStatus(self::FAILURE);
			}
		} else
		if (!($row instanceof Zend_Db_Table_Row_Abstract)) {
			$message = '$row is not instanceof "Zend_Db_Table_Row_Abstract';
			$message = sprintf('%s (%s::%s)"', $message, get_class($this), __METHOD__);
			$this->_addMessage($message);
			$this->_setStatus(self::FAILURE);
			return;
		}
		
		$this->_noticeObserver('pre_save');
		
		try {
			$row->setFromArray($data);
			$row->save();
			
			$this->_noticeObserver('post_save');

			$this->_setStatus(self::SUCCESS);
		} catch (Zend_Db_Exception $e) {
			$this->_addException($e);
			$this->_setStatus(self::FAILURE);
		}

		return $row;
	}
	
	/**
	 * @param Zend_Db_Table_Row_Abstract $row
	 * @return void
	 */
	public function delete(Zend_Db_Table_Row_Abstract $row) {
		$this->_noticeObserver('pre_delete');

		try {
			$row->delete();
			
			$this->_noticeObserver('post_delete');
			
			$this->_setStatus(self::SUCCESS);
		} catch (Zend_Db_Exception $e) {
			$this->_addException($e);
			$this->_setStatus(self::FAILURE);
		}
	}
	
	/**
	 * @param array $data
	 * @return void
	 */
	public function editableUpdate(array $data) {
		$table = $this->getDbTable();
		$db = $table->getAdapter();

		$primaryKey = $table->info(Zend_Db_Table::PRIMARY);

		$db->beginTransaction();
		try {
			foreach ($data as $key => $values) {
				$where = array();
				$primaryValues = explode(KontorX_DataGrid_Cell_Editable_Abstract::SEPARATOR, $key);
				foreach ($primaryKey as $i => $column) {
					if (isset($primaryValues[$i-1])) {
						$where[] = $db->quoteInto($column . ' = ?', $primaryValues[$i-1]);
					}
				}

				// update tylko gdy są dane
				if (count($where)) {
					$where = implode(' AND ', $where);
					$table->update($values, $where);
				}
			}

			$db->commit();

			// notify observers
			$this->_noticeObserver('post_editableUpdate');

			$this->_setStatus(self::SUCCESS);
		} catch (Zend_Db_Table_Exception $e) {
			$db->rollBack();
			$this->_setStatus(self::FAILURE);
			$this->_addMessage($e->getMessage());
		}
	}
	
	/**
	 * @param array $data
	 * @return void
	 */
	public function editableDelete(array $data) {
		$table = $this->getDbTable();
		$db = $table->getAdapter();

		$primaryKey = $table->info(Zend_Db_Table::PRIMARY);

		$db->beginTransaction();
		try {
			foreach ($data as $key => $values) {
				if (is_array($values) && !current($values)) {
					continue;
				} else
				if (!(bool)$values) {
					continue;
				} 

				$where = array();
				$primaryValues = explode(KontorX_DataGrid_Cell_Editable_Abstract::SEPARATOR, $key);
				
				foreach ($primaryKey as $i => $column) {
					if (isset($primaryValues[$i-1])) {
						$where[] = $db->quoteInto($column . ' = ?', $primaryValues[$i-1]);
					}
				}

				// delete tylko gdy są dane
				if (count($where)) {
					$where = implode(' AND ', $where);
					$table->delete($where);
				}
			}

			$db->commit();
			
			// notify observers
			$this->_noticeObserver('post_editableDelete');

			$this->_setStatus(self::SUCCESS);
		} catch (Zend_Db_Table_Exception $e) {
			$db->rollBack();
			$this->_setStatus(self::FAILURE);
			$this->_addMessage($e->getMessage());
		}
	}
}
