<?php
require_once 'Zend/Db/Table/Row/Abstract.php';

class KontorX_Db_Table_Row extends Zend_Db_Table_Row_Abstract {

//	public function isCachable() {
//		return $this->isConnected()
//			? $this->_table->isCaching()
//			: false;
//	}
//
//	public function delete() {
//		$where = $this->_getWhereQuery();
//
//		parent::delete();
//
//		if ($this->isCachable()) {
//			$primary = current($this->_primary);
//			$schema  = $this->_table->info(Zend_Db_Table::SCHEMA);
//			$name	 = $this->_table->info(Zend_Db_Table::NAME);
//			
//			$id = md5($schema . $name . $this->{$primary});
//			$cleanTags = array(('row' . $schema . $name));
//
//			$cache = $this->_table->getDefaultRowsetCache();
//			$cache->remove($id);
//			$cache->clean(Zend_Cache::CLEANING_MODE_MATCHING_TAG, $cleanTags);
//		}
//	}
}
?>