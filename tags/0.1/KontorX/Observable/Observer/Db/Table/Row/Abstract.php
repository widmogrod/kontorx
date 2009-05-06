<?php
/**
 * KontorX_Observable_Observer_Db_Table_Row_Abstract
 * 
 * @category 	KontorX
 * @package 	KontorX_Observable
 * @version 	0.1.2
 * @license		GNU GPL
 * @author 		Marcin `widmogror` Habryn, widmogrod@gmail.com
 */
abstract class KontorX_Observable_Observer_Db_Table_Row_Abstract extends KontorX_Observable_Observer_Abstract {
	const ADD = 1;
	const DELETE = 2;

	protected $_acceptStatus = array(null, KontorX_Observable_Abstract::SUCCESS);
	
	/**
	 * Enter description here...
	 *
	 * @var Zend_Db_Table_Row_Abstract
	 */
	protected $_row = null;

	protected $_type = null;

	public function __construct(Zend_Db_Table_Row_Abstract $row, $type) {
		$this->_checkType($type);
		$this->_row = $row;
		$this->_type = $type;
	}

	public function update(KontorX_Observable_Abstract $observable, array $data = array()) {
		switch ($this->getType()) {
			case self::ADD: $this->_add($observable, $data); break;
			case self::DELETE: $this->_delete($observable); break;
		}
	}

	protected function _add(KontorX_Observable_Abstract $observable, array $data) {
		$row = $this->getRow();
		$row->setFromArray($data);
		
		$db = $row->getTable()->getAdapter();

		try {
			$db->beginTransaction();

			$row->save();
			$observable->setStatus(KontorX_Observable_Abstract::SUCCESS);

			$db->commit();
		} catch (Zend_Db_Table_Row_Exception $e) {
			$db->rollBack();

			$message = $e->getMessage() . "\n" . $e->getTraceAsString();
			$observable->addMessage($message, KontorX_Observable_Abstract::CRITICAL);
			$observable->setStatus(KontorX_Observable_Abstract::CRITICAL);
		}
	}

	protected function _delete(KontorX_Observable_Abstract $observable) {
		$row = $this->getRow();
		$db = $row->getTable()->getAdapter();

		try {
			$db->beginTransaction();

			$row->delete();
			$observable->setStatus(KontorX_Observable_Abstract::SUCCESS);

			$db->commit();
		} catch (Zend_Db_Table_Row_Exception $e) {
			$db->rollBack();

			$message = $e->getMessage() . "\n" . $e->getTraceAsString();
			$observable->addMessage($message, KontorX_Observable_Abstract::CRITICAL);
			$observable->setStatus(KontorX_Observable_Abstract::CRITICAL);
		}
	}

	public function getType() {
		return $this->_type;
	}

	/**
	 * Enter description here...
	 *
	 * @return Zend_Db_Table_Row_Abstract
	 */
	public function getRow() {
		return $this->_row;
	}

	protected function _checkType($type) {
		switch ($type) {
			case self::ADD:
			case self::DELETE:
				return true;
		}
		
		$message = "Unknown or invalid type!";
		throw new KontorX_Observable_Exception($message);
	}
}
?>