<?php
require_once 'KontorX/Update/Db/Abstract.php';
abstract class KontorX_Update_Db_Abstract_Table extends KontorX_Update_Db_Abstract {

	const ADD_COLUMN = 'ADD_COLUMN';
	const REMOVE_COLUMN = 'REMOVE_COLUMN';

	protected $table;
	
	protected $_sqlOptions = array(
		self::ADD_COLUMN => array('table','name','type','null'),
		self::REMOVE_COLUMN => array('table','name')
	);

	protected $_sql = array(
		self::ADD_COLUMN => 'ALTER TABLE `:@table` ADD `:name` :@type :@null',
		self::REMOVE_COLUMN => 'ALTER TABLE `:@table` DROP COLUMN `:name`'
	);

	/**
	 * @var array
	 */
	protected $_bindNoQuoted = array(
		'table' => ':@table',
		'type' => ':@type',
		'null' => ':@null',
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
	public function addColumn($name, array $options) {
		$options['name'] = $name;
		$options = $this->_getOptions(self::ADD_COLUMN, $options);
		return $this->_execute($this->_sql[self::ADD_COLUMN], $options);
	}
	
	/**
	 * @param string $name
	 * @param array $options
	 */
	public function removeColumn($name) {
		$options['name'] = $name;
		$options = $this->_getOptions(self::REMOVE_COLUMN, $options);
		return $this->_execute($this->_sql[self::REMOVE_COLUMN], $options);
	}
}