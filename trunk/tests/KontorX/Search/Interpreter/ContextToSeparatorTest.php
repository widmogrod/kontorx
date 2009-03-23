<?php
require_once '../../../setupTest.php';

/**
 * @see KontorX_Search_Semantic_Query_Date 
 */
require_once 'KontorX/Search/Semantic/Interpreter/ContextToSeparator.php';

/**
 * @see KontorX_Search_Semantic_Context 
 */
require_once 'KontorX/Search/Semantic/Context.php';

class KontorX_Search_Semantic_Interpreter_ContextToSeparatorTest extends UnitTestCase {
	
	/**
	 * @var KontorX_Search_Semantic_Interpreter_ContextToSeparator
	 */
	protected $_interpreter = null;
	
	public function setUp() {
		$this->_interpreter = new KontorX_Search_Semantic_Interpreter_ContextToSeparator();
	}
	
	public function tearDown() {
		$this->_interpreter = null;
	}

	public function testContextToSeparator1() {
		$day = 'poniedziałek, wtorek i środa';
		$correct = 'poniedziałek';
		$context = "$day";
		$contextInstance = new KontorX_Search_Semantic_Context($context);

		$this->_interpreter->interpret($contextInstance);
		$data = $contextInstance->getOutput();
		
		$message = sprintf('Znaleziona tresc "%s" w tekscie "%s" jest inna od oczekiwanej "%s"', $data, $context, $correct);
		$this->assertEqual($data, $correct, $message);
    }
    
	public function testContextToSeparator2() {
		$day = 'poniedziałek i wtorek, no ba!';
		$correct = 'poniedziałek i wtorek';
		$context = "$day";
		$contextInstance = new KontorX_Search_Semantic_Context($context);

		$this->_interpreter->interpret($contextInstance);
		$data = $contextInstance->getOutput();
		
		$message = sprintf('Znaleziona tresc "%s" w tekscie "%s" jest inna od oczekiwanej "%s"', $data, $context, $correct);
		$this->assertEqual($data, $correct, $message);
    }
}

$r = new KontorX_Search_Semantic_Interpreter_ContextToSeparatorTest();
$r->run(new TextReporter());