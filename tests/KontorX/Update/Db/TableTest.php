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
		$this->table->addColumn('col1',array(
			'type' => 'TEXT',
			'null' => 'NOT NULL',
		));
		
		$this->assertEqual(
				$this->table->getStatus(), 
				KontorX_Update_Db_MysqlTable::SUCCESS, 
				'kolumna nie została utworzona');

		$this->assertNotEqual(
				$this->table->getStatus(), 
				KontorX_Update_Db_MysqlTable::FAILURE, 
				'niewłaściwy status.. wynika że kolumna została utworzona');

		$this->assertEqual(
				$this->table->getMessages(),
				array(),
				sprintf('Nie powinno być wiadomości!: %s',
						array_map(array($this, 'dump'), $this->table->getMessages())));
    }
    
	public function testRemoveColumn() {
		$result = $this->table->removeColumn('col1');
		$this->assertTrue($result, '');
		
		$this->assertEqual(
				$this->table->getStatus(), 
				KontorX_Update_Db_MysqlTable::SUCCESS, 
				'kolumna nie została usunięta');

		$this->assertNotEqual(
				$this->table->getStatus(), 
				KontorX_Update_Db_MysqlTable::FAILURE, 
				'niewłaściwy status..');

		$this->assertEqual(
				$this->table->getMessages(),
				array(),
				sprintf('Nie powinno być wiadomości!: %s',
						array_map(array($this, 'dump'), $this->table->getMessages())));
    }

	public function testRemoveColumnNonExsist() {
		try {
			$result = $this->table->removeColumn('colNotExsists');
			$this->assertNoErrors('Brak exception (Zend_Db_Select_Exception)! Usunięto nie istniejącą kolumne!!');
		} catch (Zend_Db_Select_Exception $e) {
		}
    }
    
	public function testAddIndex() {
		$result = $this->table->addIndex('test_index', array(
			'columns' => array('id')
		));
		
		$this->assertTrue($result, '');
		
		$this->assertEqual(
				$this->table->getStatus(), 
				KontorX_Update_Db_MysqlTable::SUCCESS, 
				'index nie została dodany');

		$this->assertNotEqual(
				$this->table->getStatus(), 
				KontorX_Update_Db_MysqlTable::FAILURE, 
				'niewłaściwy status..');

		$this->assertEqual(
				$this->table->getMessages(),
				array(),
				sprintf('Nie powinno być wiadomości!: %s',
						array_map(array($this, 'dump'), $this->table->getMessages())));
    }

	public function testRemoveIndex() {
		$this->table->removeIndex('test_index');
		
		$this->assertEqual(
				$this->table->getStatus(), 
				KontorX_Update_Db_MysqlTable::SUCCESS, 
				'index nie została usunięta');

		$this->assertNotEqual(
				$this->table->getStatus(), 
				KontorX_Update_Db_MysqlTable::FAILURE, 
				'niewłaściwy status..');

		$this->assertEqual(
				$this->table->getMessages(),
				array(),
				sprintf('Nie powinno być wiadomości!: %s',
						array_map(array($this, 'dump'), $this->table->getMessages())));
    }
}

$r = new KontorX_Update_Db_MysqlTableTest();
$r->run(new TextReporter());