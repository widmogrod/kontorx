<?php
require_once 'KontorX/Install/Update/Db/Abstract.php';
class KontorX_Install_Update_Db_Mysqli extends KontorX_Install_Update_Db_Abstract {
	
	const CREATE_DATABASE = 'CREATE_DATABASE';
	const CREATE_TABLE 	  = 'CREATE_TABLE';

	const TYPE = ':type';

	protected $_sql = array(
		self::CREATE_DATABASE => 'CREATE DATABASE :database',
		self::CREATE_TABLE 	  => 'CREATE TABLE `:database`.`:table` (:columns) ENGINE = :engine ;',
		self::CREATE_COLUMN	  =>'`:name` :type :null :ai :index :comment'
	);

	/**
	 * @var string
	 */
	protected $_engine = 'MYISAM';

	/**
	 * @var array
	 */
	protected $_columns = array();

	protected function addColumn($name, $type, array $options) {
		if (!array_key_exists($name, $this->_columns)) {
			$this->_columns[$name] = $options;
			// TODO Check avalibel types
			$this->_columns[$name][self::TYPE] = $type;
		}
	}
}