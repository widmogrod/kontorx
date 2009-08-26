<?php
require_once 'KontorX/Update/Db/Abstract.php';
abstract class KontorX_Update_Db_Abstract_Table extends KontorX_Update_Db_Abstract {

	const ADD_COLUMN = 'ADD_COLUMN';
	const REMOVE_COLUMN = 'REMOVE_COLUMN';

	protected $table;
	
	protected $_sqlOptions = array(
		self::ADD_COLUMN => array('table','name','type','null','after'),
		self::REMOVE_COLUMN => array('table','name')
	);

	/**
	 * @var unknown_type
	 */
	protected $_sql = array(
		self::ADD_COLUMN => 'ALTER TABLE `:@table` ADD :@name :@type :@null :@after',
		self::REMOVE_COLUMN => 'ALTER TABLE `:@table` DROP COLUMN :@name'
	);

	/**
	 * Zmienna mapuje parametry zapytania (@see _sqlOptions)
	 * na wartości, które będą zamieniane bez quotowania! 
	 * 
	 * Wartości bez quotowania zostały występują w zapytaniu z prefiksem ":@"
	 * Watości z quotowaniem są z prefiksem ":"
	 * 
	 * @var array
	 */
	protected $_bindNoQuoted = array(
		'table' => ':@table',
		'type' => ':@type',
		'null' => ':@null',
		'after' => ':@after',
		'name' => ':@name',
		'comment' => ':@comment'
	);

	/**
	 * @param string $table 
	 */
	public function __construct($table) {
		$this->table = (string) $table;
	}

	/**
	 * @param string $name
	 * @param array $options
	 * @return bool
	 */
	public function addColumn($name, array $options = array()) {
		$options['name'] = $name;
		$options = $this->_getOptions(self::ADD_COLUMN, $options);
		return $this->_execute($this->_sql[self::ADD_COLUMN], $options);
	}
	
	/**
	 * @param string $name
	 * @param array $options
	 */
	public function removeColumn($name) {
		$options = array('name' => $name);
		$options = $this->_getOptions(self::REMOVE_COLUMN, $options);
		return $this->_execute($this->_sql[self::REMOVE_COLUMN], $options);
	}
}