<?php
if (!defined('SETUP_TEST')) {
	require_once '../../setupTest.php';
}

require_once 'KontorX/Update/Manager.php';

class KontorX_Update_ManagerTest extends UnitTestCase {

	/**
	 * @var string
	 */
	protected $_updatePath;
	
	public function setUp() {
		require_once 'Zend/Db.php';
		$db = Zend_Db::factory('pdo_mysql',array(
			'dbname' =>'test',
			'username' => 'root',
			'password' => ''
		));
		
		require_once 'Zend/Db/Table/Abstract.php';
		Zend_Db_Table_Abstract::setDefaultAdapter($db);

		$this->_updatePath = dirname(__FILE__) . '/test_updates/';
	}
	
	public function tearDown() {
		
	}

	public function testInitNoUpdatePath() {
		try {
			$manager = new KontorX_Update_Manager();
			$this->assertNoErrors('Nie przekazano ścieżko - brak KontorX_Update_Exception');
		} catch (Exception $e) {
			$this->assertIsA($e, 'KontorX_Update_Exception', 'Niewłaściwy wyjatek');
		}
	}
	
	public function testInitUpdatePathNoExsists() {
		try {
			$manager = new KontorX_Update_Manager('/no-exsists-path/');
			$this->assertNoErrors('Ścieżka nie powinna istnieć, brak KontorX_Update_Exception');
		} catch (Exception $e) {
			$this->assertIsA($e, 'KontorX_Update_Exception', 'Niewłaściwy wyjatek');
		}
	}

	public function testInitUpdatePath() {
		$manager = new KontorX_Update_Manager($this->_updatePath);
		$expected = array(
			0 => 'r0.sql',
			1 => 'r1.sql',
			3 => 'r3.php'
		);

		$result = $manager->getUpdateFileList();
		$this->dump($result);
		$this->assertEqual($expected, $result, 'Lista aktualizacji niepoprawna');
	}

	public function testUpdate() {
		$manager = new KontorX_Update_Manager($this->_updatePath);
		$result = $manager->update();
		$this->assertTrue($result, 'Aktualizacja nie została wykonana');
	}

	/**
	 * Dezaktualizacja nie może zostać wykonana bo
	 * pliki r0.sql i r1.sql nie są downgradowane!
	 * @return void
	 */
	public function testDowngradeFalse() {
		$manager = new KontorX_Update_Manager($this->_updatePath);
		$result = $manager->downgrade();
		
		$this->dump($result);
		$this->assertFalse($result, 'Dezaktualizacja została wykonana!');
	}
	
	public function testDowngradeTrue() {
		$manager = new KontorX_Update_Manager($this->_updatePath);
		$result = $manager->downgrade(KontorX_Update_Manager::FORCE);
		$this->assertTrue($result, 'Dezaktualizacja nie została wykonana');
	}
}

$r = new KontorX_Update_ManagerTest();
$r->run(new TextReporter());