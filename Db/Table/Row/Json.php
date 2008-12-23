<?php
require_once 'Zend/Db/Table/Row/Abstract.php';
class KontorX_Db_Table_Row_Json extends Zend_Db_Table_Row_Abstract {
	public function toJson() {
	    require_once 'Zend/Json.php';
	    return Zend_Json::encode($this->toArray());
	}

	public function __toString() {
		return $this->toJson();
	}
}