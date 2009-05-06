<?php
class KontorX_Db_Decorator_eZ extends ezcDbHandler {
	/**
	 * @var Zend_Db_Adapter_Abstract
	 */
	protected $_db = null;

	public function __construct(Zend_Db_Adapter_Abstract $db) {
		$this->_db = $db;
		$config = $db->getConfig();
//		Zend_Debug::dump($config);
		parent::__construct($c, 'sqlite:///home/gabriel/workspace/php/self/simple/application/sqlite.db');
	}

	public function __call($name, array $argumensts = array()) {
		if (!method_exists($this->_db, $name)){
			$error = "method `$name` do not exsists";
			throw new KontorX_Db_Decorator_Exception($error);
		}

		return call_user_method_array($name, $this->_db, $argumensts);
	}
	
	public function getName() {
		return 'sqlite';
	}
}
?>