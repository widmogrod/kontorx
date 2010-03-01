<?php
if (!defined('SETUP_TEST')) {
	require_once '../../../setupTest.php';
}

/**
 * @see KontorX_Search_Semantic_Query_Date 
 */
require_once 'KontorX/Search/Semantic/Interpreter/ArrayKeyExsists.php';

/**
 * @see KontorX_Search_Semantic_Context 
 */
require_once 'KontorX/Search/Semantic/Context.php';

class KontorX_Search_Semantic_Interpreter_ArrayKeyExsistsTest extends UnitTestCase {
	
	/**
	 * @var KontorX_Search_Semantic_Interpreter_ArrayKeyExsists
	 */
	protected $_interpreter = null;
	
	public function setUp() {
		$this->_interpreter = new KontorX_Search_Semantic_Interpreter_ArrayKeyExsists(array(
			'poniedziałek' => 1,
			'wtorek' => 2,
			'środa' => 3,
			'czwartek' => 4,
			'piątek' => 5,
			'sobota' => 6,
			'niedziela' => 7
		));
	}
	
	public function tearDown() {
		$this->_interpreter = null;
	}

	public function testArrayKeyExsistsTrue() {
		$day = 'poniedziałek';
		$correct = 1;
		$correctResult = true;
		$context = "Dzisiaj jest $day";
		$contextInstance = new KontorX_Search_Semantic_Context($context);

		$result = $this->_interpreter->interpret($contextInstance);
		$this->assertIdentical($result, $correctResult, "Interpretacja kontekstu powinna zwrócić 'true'");

		$data = $contextInstance->getOutput();
		
		$message = sprintf('Znaleziona dzień "%s" w tekscie "%s" jest inny od oczekiwanego "%s"', $data, $context, $correct);
		$this->assertEqual($data, $correct, $message);
    }
    
	public function testArrayKeyExsistsFalse() {
		$day = 'pon';
		$correct = array();
		$correctResult = false;
		$context = "Dzisiaj jest $day";
		$contextInstance = new KontorX_Search_Semantic_Context($context);

		$result = $this->_interpreter->interpret($contextInstance);
		$this->assertIdentical($result, $correctResult, "Interpretacja kontekstu powinna zwrócić 'false'");

		$data = $contextInstance->getOutput();
		
		$message = sprintf('Znaleziona dzień "%s" w tekscie "%s" jest inny od oczekiwanego "%s"', $data, $context, $correct);
		$this->assertEqual($data, $correct, $message);
    }
    
	public function testArrayKeyExsistsMultiCorrectTrue() {
		$day = 'poniedziałek wtorek środa';
		$correct = 1;
		$correctResult = true;
		$context = "Dzisiaj jest $day";
		$contextInstance = new KontorX_Search_Semantic_Context($context);

		$result = $this->_interpreter->interpret($contextInstance);
		$this->assertIdentical($result, $correctResult, "Interpretacja kontekstu powinna zwrócić 'true'");
		$data = $contextInstance->getOutput();

		$this->dump($data);
		
		$message = sprintf('Znaleziona dzień "%s" w tekscie "%s" jest inny od oczekiwanego "%s"', $data, $context, $correct);
		$this->assertEqual($data, $correct, $message);
    }
}

$r = new KontorX_Search_Semantic_Interpreter_ArrayKeyExsistsTest();
$r->run(new TextReporter());