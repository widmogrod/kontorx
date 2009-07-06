<?php
require_once 'Zend/Db/Table/Rowset/Abstract.php';
class KontorX_Db_Table_Rowset_Json extends Zend_Db_Table_Rowset_Abstract {
	
	public function toJson() {
	    require_once 'Zend/Json.php';
	    return Zend_Json::encode($this->toArray());
	}
}