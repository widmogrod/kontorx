<?php
if (!defined('SETUP_TEST')) {
	require_once '../../../setupTest.php';
}

require_once 'KontorX/Import/Adapter/Csv.php';

class KontorX_Import_Adapter_CsvTest extends UnitTestCase
{
	protected $_filepath;

	public function setUp() 
	{
	    $this->_filepath = dirname(__FILE__) . '/CsvTest.csv';
	}
	
	public function tearDown()
	{
		$this->_filepath = null;
	}

    public function testCreateNewInstanceFileExistsSuccess()
	{
		$instance = new KontorX_Import_Adapter_Csv($this->_filepath);
		$this->assertIsA($instance, 'KontorX_Import_Adapter_Csv', 'Utworzony obiekt nie jest typu "KontorX_Import_Adapter_Csv"');
	}
	
    public function testCreateNewInstanceFileExistsFailure()
	{
	    try {
	        $instance = new KontorX_Import_Adapter_Csv('file_not_exists.csv');
	    } catch (Exception $e) {
	        $this->assertIsA($e, 'KontorX_Import_Exception', 'Zucony wyjątek nie jest typu "KontorX_Import_Exception"');
	    }
	}
	
    public function testFilename()
	{
	    $instance = new KontorX_Import_Adapter_Csv($this->_filepath);
	    $this->assertEqual($instance->getFilename(), $this->_filepath, 'Nazwa pliku się nie zgadza');
	}
	
    public function testOpen()
	{
	    $instance = new KontorX_Import_Adapter_Csv($this->_filepath);
	    $instance->open();
	    
	    $this->assertTrue($instance->isOpen(), 'Plik nie został otwarty');
	}
	
    public function testClose()
	{
	    $instance = new KontorX_Import_Adapter_Csv($this->_filepath);
	    $instance->open();
	    $instance->close();
	    
	    $this->assertFalse($instance->isOpen(), 'Plik nie został zamknięty');
	}
	
	
    public function testToArray()
	{
	    $instance = new KontorX_Import_Adapter_Csv($this->_filepath);
	    $result = $instance->toArray();
	    $comparsion = array(
	        array('Wiersz1','wartosc','123','Który to jest znak'),
            array('Wiersz2','wartosc','432','Który to jest znak*&@!^#')
	    );
	    //$this->dump($result);
	    $this->assertIdentical($result, $comparsion, 'toArray zwrócił nieoczekiwany wyjątek');
	}
}

$r = new KontorX_Import_Adapter_CsvTest();
$r->run(new TextReporter());
