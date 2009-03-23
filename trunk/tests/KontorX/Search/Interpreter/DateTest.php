<?php
require_once '../../../setupTest.php';

/**
 * @see KontorX_Search_Semantic_Query_Date 
 */
require_once 'KontorX/Search/Semantic/Interpreter/Date.php';

/**
 * @see KontorX_Search_Semantic_Context 
 */
require_once 'KontorX/Search/Semantic/Context.php';

class KontorX_Search_Semantic_Interpreter_DateTest extends UnitTestCase {
	
	/**
	 * @var KontorX_Search_Semantic_Interpreter_Date
	 */
	protected $_interpreter = null;
	
	public function setUp() {
		$this->_interpreter = new KontorX_Search_Semantic_Interpreter_Date();
	}
	
	public function tearDown() {
		$this->_interpreter = null;
	}

	public function testDataDDMMYYYYInContent() {
		$date = '11-03-2009';
		$context = "Dzisiaj jest $date";
		$contextInstance = new KontorX_Search_Semantic_Context($context);

		$this->_interpreter->interpret($contextInstance);
		$data = $contextInstance->getOutput();
		
		$message = sprintf('Znaleziona data "%s" w tekscie "%s" jest różna od oczekiwanej "%s"', $data, $context, $date);
		$this->assertEqual($data, $date, $message);
    }

	public function testTimeHHIISSInContent() {
		$time = '13:12:59';
		$context = "Jest godzina $time po południu";
		$contextInstance = new KontorX_Search_Semantic_Context($context);

		$this->_interpreter->interpret($contextInstance);
		$data = $contextInstance->getOutput();
		
		$message = sprintf('Znaleziona godzina "%s" w tekscie "%s" jest różna od oczekiwanej "%s"', $data, $context, $time);
		$this->assertEqual($data, $time, $message);
    }

	public function testTimeHHMMInContent() {
		$time = '11:12';
		$context = "Jest godzina $time po południu";
		$contextInstance = new KontorX_Search_Semantic_Context($context);

		$this->_interpreter->interpret($contextInstance);
		$data = $contextInstance->getOutput();
		
		$message = sprintf('Znaleziona godzina "%s" w tekscie "%s" jest różna od oczekiwanej "%s"', $data, $context, $time);
		$this->assertEqual($data, $time, $message);
    }
    
	public function testTimeHHInContent() {
		$time = '11';
		$context = "Jest godzina $time po południu";
		$contextInstance = new KontorX_Search_Semantic_Context($context);

		$this->_interpreter->interpret($contextInstance);
		$data = $contextInstance->getOutput();
		
		$message = sprintf('Znaleziona godzina "%s" w tekscie "%s" jest różna od oczekiwanej "%s"', $data, $context, $time);
		$this->assertEqual($data, $time, $message);
    }
    
	public function testTimeHHInContentFalse() {
		$time = '37';
		$correct = array();
		$resultCorrect = false;
		$context = "Jest godzina $time po południu";
		$contextInstance = new KontorX_Search_Semantic_Context($context);

		$result = $this->_interpreter->interpret($contextInstance);
		$data = $contextInstance->getOutput();
		$this->dump($data);
		
		$this->assertIdentical($result, $resultCorrect, "Wynik identyfikacji jest różny od 'false'");
		
		$message = sprintf('Znaleziona godzina "%s" w tekscie "%s" jest różna od oczekiwanej "%s"', $data, $context, $correct);
		$this->assertEqual($data, $correct, $message);
    }
    
	public function testNoTimeInContent() {
		$time = 'o brak';
		$correct = array();
		$context = "Jest godzina $time po południu";
		$contextInstance = new KontorX_Search_Semantic_Context($context);

		$this->_interpreter->interpret($contextInstance);
		$data = $contextInstance->getOutput();

		$message = sprintf('Znaleziona godzina "%s" w tekscie "%s" jest różna od oczekiwanej "%s"', $data, $context, $correct);
		$this->assertIdentical($data, $correct, $message);
    }
}

$r = new KontorX_Search_Semantic_Interpreter_DateTest();
$r->run(new TextReporter());