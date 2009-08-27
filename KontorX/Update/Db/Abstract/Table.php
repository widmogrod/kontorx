<?php
require_once 'KontorX/Update/Db/Abstract.php';
abstract class KontorX_Update_Db_Abstract_Table extends KontorX_Update_Db_Abstract {

	const ADD_COLUMN = 'ADD_COLUMN';
	const DROP_COLUMN = 'DROP_COLUMN';
	const ADD_INDEX = 'ADD_INDEX';
	const DROP_INDEX = 'DROP_INDEX';

	protected $table;
	
	/**
	 * @var array
	 */
	protected $_sqlOptions = array(
		self::ADD_COLUMN => array('table','name','type','null','after'),
		self::DROP_COLUMN => array('table','name'),
		self::ADD_INDEX => array('table','name','columns'),
		self::DROP_INDEX => array('table','name')
	);

	/**
	 * @var array
	 */
	protected $_sql = array(
		self::ADD_COLUMN => 'ALTER TABLE `:@table` ADD :@name :@type :@null :@after',
		self::DROP_COLUMN => 'ALTER TABLE `:@table` DROP COLUMN :@name',
		self::ADD_INDEX => 'ALTER TABLE `:@table` ADD INDEX `:@name`(:@columns);',
		self::DROP_INDEX => 'ALTER TABLE `:@table` DROP INDEX `:@name`;'
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
		'comment' => ':@comment',
		'columns' => ':@columns'
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
		$options = $this->_getOptions(self::DROP_COLUMN, $options);
		return $this->_execute($this->_sql[self::DROP_COLUMN], $options);
	}
	
	/**
	 * @param string $name
	 * @param array $options
	 * @return bool
	 */
	public function addIndex($name, array $options = array()) {
		$options['name'] = $name;
		$options = $this->_getOptions(self::ADD_INDEX, $options);
		$options['columns'] = sprintf('`%s`', implode('`,`', (array)$options['columns']));
		return $this->_execute($this->_sql[self::ADD_INDEX], $options);
	}

	/**
	 * @param string $name
	 * @param array $options
	 * @return bool
	 */
	public function removeIndex($name, array $options = array()) {
		$options['name'] = $name;
		$options = $this->_getOptions(self::DROP_INDEX, $options);
		return $this->_execute($this->_sql[self::DROP_INDEX], $options);
	}
}