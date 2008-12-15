<?php
require_once 'Zend/Db/Table/Rowset/Abstract.php';
class KontorX_Db_Table_Rowset_Json extends Zend_Db_Table_Rowset_Abstract {
	
    public function toArray(){
        foreach ($this->_rows as $i => $row) {
            $this->_data[(int) $i] = $row->toArray() + array('asd' => 'asdasd');
        }
        return $this->_data;
    }
	
	public function toJson() {
	    require_once 'Zend/Json.php';
	    return Zend_Json::encode($this->toArray());
	}

	public function __toString() {
		return $this->toJson();
	}
}