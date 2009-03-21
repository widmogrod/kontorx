<?php
require_once '../../../setupTest.php';

/**
 * @see KontorX_Search_Semantic_Query_Date 
 */
require_once 'KontorX/Search/Semantic/Query/Date.php';

class KontorX_Search_Semantic_Query_DateTest extends UnitTestCase {
	
	/**
	 * @var KontorX_Search_Semantic_Query_Date
	 */
	protected $_query = null;
	
	public function setUp() {
		$this->_query = new KontorX_Search_Semantic_Query_Date();
	}
	
	public function tearDown() {
		$this->_query = null;
	}

	public function testDataDDMMYYYYInContent() {
		$date = '11-03-2009';
		$content = "Dzisiaj jest $date";

		$data = $this->_query->query($content);
		
		$message = sprintf('Znaleziona data "%s" w tekscie "%s" jest różna od oczekiwanej "%s"', $data, $content, $date);
		$this->assertEqual($data, $date, $message);
    }

	public function testTimeHHIISSInContent() {
		$time = '13:12:59';
		$content = "Jest godzina $time po południu";

		$data = $this->_query->query($content);
		
		$message = sprintf('Znaleziona godzina "%s" w tekscie "%s" jest różna od oczekiwanej "%s"', $data, $content, $time);
		$this->assertEqual($data, $time, $message);
    }

	public function testTimeHHMMInContent() {
		$time = '11:12';
		$content = "Jest godzina $time po południu";

		$data = $this->_query->query($content);
		
		$message = sprintf('Znaleziona godzina "%s" w tekscie "%s" jest różna od oczekiwanej "%s"', $data, $content, $time);
		$this->assertEqual($data, $time, $message);
    }
    
	public function testTimeHHInContent() {
		$time = '11';
		$content = "Jest godzina $time po południu";

		$data = $this->_query->query($content);
		
		$message = sprintf('Znaleziona godzina "%s" w tekscie "%s" jest różna od oczekiwanej "%s"', $data, $content, $time);
		$this->assertEqual($data, $time, $message);
    }
}

$r = new KontorX_Search_Semantic_Query_DateTest();
$r->run(new TextReporter());