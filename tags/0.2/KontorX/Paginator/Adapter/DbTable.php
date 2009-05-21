<?php
require_once 'Zend/Paginator/Adapter/DbSelect.php';
class KontorX_Paginator_Adapter_DbTable extends Zend_Paginator_Adapter_DbSelect {
	public function __construct(Zend_Db_Table_Abstract $table) {
        $this->_select = $table->select();
    }
}