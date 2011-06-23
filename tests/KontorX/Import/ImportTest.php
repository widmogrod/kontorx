<?php
if (!defined('SETUP_TEST')) {
	require_once '../../setupTest.php';
}

require_once 'KontorX/Import.php';

class KontorX_Import_ImportTest extends UnitTestCase 
{
    protected $_filepath;
	
	public function setUp() 
	{
	    $this->_filepath = dirname(__FILE__) . '/ImportTest.csv';
	}
	
	public function tearDown() 
	{
	}
	
	public function testCreateFactory()
	{
		$adapter = KontorX_Import::factory($this->_filepath);
		$this->assertIsA($adapter, 'KontorX_Import_Adapter_Csv', 'Utworzony obiekt nie jest typu "KontorX_Import_Adapter_Csv"');
	}
}

$r = new KontorX_Import_ImportTest();
$r->run(new TextReporter());
