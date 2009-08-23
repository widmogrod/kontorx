<?php
if (!defined('SETUP_TEST')) {
	require_once '../../../setupTest.php';
}

require_once 'KontorX/Update/Db/Mysql/Table.php';

class KontorX_Update_Db_MysqlTable extends KontorX_Update_Db_Mysql_Table {
	/**
	 * Update
	 * @return void
	 */
	public function up() {
		
	}
	
	/**
	 * Downgrade
	 * @return void
	 */
	public function down() {
		
	}
}

class KontorX_Update_Db_MysqlTableTest extends UnitTestCase {
	
	/**
	 * @var KontorX_Update_Db_MysqlTable
	 */
	public $table;
	
	public function setUp() {
		require_once 'Zend/Db.php';
		$db = Zend_Db::factory('pdo_mysql',array(
			'dbname' =>'test',
			'username' => 'root',
			'password' => ''
		));
		
		require_once 'Zend/Db/Table/Abstract.php';
		Zend_Db_Table_Abstract::setDefaultAdapter($db);

		$this->table = new KontorX_Update_Db_MysqlTable('table');
	}

	public function tearDown() {
		$this->table = null;
	}

	public function testAddColumn() {
		$result = $this->table->addColumn('col1',array(
			'type' => 'TEXT',
			'null' => 'NOT NULL',
		));
		
		$this->assertTrue($result, 'kolumna nie została utworzona');
    }
    
	public function testRemoveColumn() {
		$result = $this->table->removeColumn('col1');
		$this->assertTrue($result, 'kolumna nie została usunięta');
    }

	public function testRemoveColumnNonExsist() {
		try {
			$result = $this->table->removeColumn('colNotExsists');
			$this->assertNoErrors('Brak exception (Zend_Db_Select_Exception)! Usunięto nie istniejącą kolumne!!');
		} catch (Zend_Db_Select_Exception $e) {
		}
    }
}

$r = new KontorX_Update_Db_MysqlTableTest();
$r->run(new TextReporter());