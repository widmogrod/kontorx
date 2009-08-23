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
		
		$this->assertEqual($expected, $result, 'Lista aktualizacji niepoprawna');
	}
}

$r = new KontorX_Update_ManagerTest();
$r->run(new TextReporter());