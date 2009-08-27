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