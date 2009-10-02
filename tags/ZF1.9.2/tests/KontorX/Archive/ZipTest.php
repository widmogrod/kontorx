<?php
if (!defined('SETUP_TEST')) {
	require_once '../../setupTest.php';
}

/**
 * @see KontorX_Calendar_Month 
 */
require_once 'KontorX/Archive/Zip.php';

class KontorX_Archive_ZipTest extends UnitTestCase {
	
	/**
	 * @var KontorX_Archive_Zip
	 */
	protected $_archive = null;
	
	public function setUp() {
		$this->_archive = new KontorX_Archive_Zip();
	}
	
	public function tearDown() {
		$this->_archive = null;
	}

	public function testResource() {
		$resource = $this->_archive->getResource();
		$result = ($resource instanceof ZipArchive);
		$this->assertTrue($result, 'resource is not "ZipArchive"');
	}
}

$r = new KontorX_Archive_ZipTest();
$r->run(new TextReporter());
	