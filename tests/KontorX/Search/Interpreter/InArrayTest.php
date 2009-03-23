<?php
require_once '../../../setupTest.php';

/**
 * @see KontorX_Search_Semantic_Query_Date 
 */
require_once 'KontorX/Search/Semantic/Interpreter/InArray.php';

/**
 * @see KontorX_Search_Semantic_Context 
 */
require_once 'KontorX/Search/Semantic/Context.php';

class KontorX_Search_Semantic_Interpreter_InArrayTest extends UnitTestCase {
	
	/**
	 * @var KontorX_Search_Semantic_Interpreter_InArray
	 */
	protected $_interpreter = null;
	
	public function setUp() {
		$this->_interpreter = new KontorX_Search_Semantic_Interpreter_InArray(array(
			'poniedziałek','wtorek','środa','czwartek','piątek','sobota','niedziela'
		));
	}
	
	public function tearDown() {
		$this->_interpreter = null;
	}

	public function testInArrayTrue() {
		$day = 'poniedziałek';
		$correct = $day;
		$context = "Dzisiaj jest $day";
		$contextInstance = new KontorX_Search_Semantic_Context($context);

		$this->_interpreter->interpret($contextInstance);
		$data = $contextInstance->getOutput();
		
		$message = sprintf('Znaleziona dzień "%s" w tekscie "%s" jest inny od oczekiwanego "%s"', $data, $context, $correct);
		$this->assertEqual($data, $correct, $message);
    }
    
	public function testInArrayFalse() {
		$day = 'pon';
		$correct = array();
		$context = "Dzisiaj jest $day";
		$contextInstance = new KontorX_Search_Semantic_Context($context);

		$this->_interpreter->interpret($contextInstance);
		$data = $contextInstance->getOutput();
		
		$message = sprintf('Znaleziona dzień "%s" w tekscie "%s" jest inny od oczekiwanego "%s"', $data, $context, $correct);
		$this->assertEqual($data, $correct, $message);
    }
    
	public function testInArrayMultiCorrectTrue() {
		$day = 'poniedziałek wtorek środa';
		$correct = "poniedziałek";
		$context = "Dzisiaj jest $day";
		$contextInstance = new KontorX_Search_Semantic_Context($context);

		$this->_interpreter->interpret($contextInstance);
		$data = $contextInstance->getOutput();
		
		$message = sprintf('Znaleziona dzień "%s" w tekscie "%s" jest inny od oczekiwanego "%s"', $data, $context, $correct);
		$this->assertEqual($data, $correct, $message);
    }
}

$r = new KontorX_Search_Semantic_Interpreter_InArrayTest();
$r->run(new TextReporter());