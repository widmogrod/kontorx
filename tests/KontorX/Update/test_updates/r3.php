<?php
class Test_Update_Table extends KontorX_Update_Db_Mysql_Table {

	public function __construct() {
		parent::__construct('mail');
	}
	
	public function up() {
		$result = $this->addColumn('test1',array(
			'type' => 'TEXT',
			'null' => 'NOT NULL',
		));
	}

	public function down() {
		$result = $this->removeColumn('test1');
	}
}