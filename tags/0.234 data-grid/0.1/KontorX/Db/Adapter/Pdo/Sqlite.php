<?php
class KontorX_Db_Adapter_Pdo_Sqlite extends Zend_Db_Adapter_Pdo_Sqlite {
	public function __construct(array $config = array()) {
		parent::__construct($config);
	}

	protected function _connect() {
		parent::_connect();
		
		$this->_connection->sqliteCreateFunction('replace', 'str_replace', 3);
		$this->_connection->sqliteCreateFunction('regexp', 'preg_match', 2);
		
		$this->_connection->sqliteCreateFunction('if', array($this,'_if'), 3);
		$this->_connection->sqliteCreateFunction('int', 'intval', 1);
		$this->_connection->sqliteCreateFunction('concat', array($this,'_concat'), 3); // do 3 ..
		$this->_connection->sqliteCreateFunction('substring_index', array($this,'_substring_index'), 3); // do 3 ..
	}
	
	public function _if($condision, $true, $false){
		return $condision === true ? $true : $false;
	}

	public function _concat($a, $b, $c) {
		return $a . $b . $c;
	}

	public function _substring_index($str, $delimeter, $count) {
		return join($delimeter, array_slice(explode($delimeter, $str),$count));
	}
}
?>
