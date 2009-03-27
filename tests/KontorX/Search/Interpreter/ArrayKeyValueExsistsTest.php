<?php
if (!defined('SETUP_TEST')) {
	require_once '../../../setupTest.php';
}

/**
 * @see KontorX_Search_Semantic_Query_Date 
 */
require_once 'KontorX/Search/Semantic/Interpreter/ArrayKeyValueExsists.php';

/**
 * @see KontorX_Search_Semantic_Context 
 */
require_once 'KontorX/Search/Semantic/Context.php';

class KontorX_Search_Semantic_Interpreter_ArrayKeyValueExsistsTest extends UnitTestCase {
	
	/**
	 * @var KontorX_Search_Semantic_Interpreter_ArrayKeyValueExsists
	 */
	protected $_interpreter = null;
	
	public function setUp() {
		$this->_interpreter = new KontorX_Search_Semantic_Interpreter_ArrayKeyValueExsists(array(
    		array('key' => 'poniedziałek',
    			  'value' => 1),
    		array('key' => 'wtorek',
    			  'value' => 2),
    		array('key' => 'środa',
    			  'value' => 3),
    		array('key' => 'czwartek',
    			  'value' => 4),
    		array('key' => 'piątek',
    			  'value' => 5),
    		array('key' => 'sobota',
    			  'value' => 6),
    		array('key' => 'niedziela',
    			  'value' => 7)
		));
	}
	
	public function tearDown() {
		$this->_interpreter = null;
	}

	public function testArrayKeyValueExsistsTrue() {
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
    
	public function testArrayKeyValueExsistsFalse() {
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
    
	public function testArrayKeyValueExsists2True() {
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
    
	public function testArrayKeyValueExsistsMultiCorrectTrue() {
		$day = 'poniedziałek wtorek środa';
		$correct = array(1,2,3);
		$correctResult = true;
		$context = "Dzisiaj jest $day";
		$contextInstance = new KontorX_Search_Semantic_Context($context);

		$this->_interpreter->setMulti(true);
		$result = $this->_interpreter->interpret($contextInstance);
		$this->assertIdentical($result, $correctResult, "Interpretacja kontekstu powinna zwrócić 'true'");
		$data = $contextInstance->getOutput();

		$this->dump($data);
		
		$message = sprintf('Znaleziona dzień "%s" w tekscie "%s" jest inny od oczekiwanego "%s"', $data, $context, $correct);
		$this->assertEqual($data, $correct, $message);
    }

    // @todo Może nowy interpreter, który będzie alizował treść sorawdzał dopasowanie i odwołanie
    // do konkretnych rekoród .. przypisywał wagi.. zalatuje SI
	public function testArrayKeyValueExsistsBigArray() {
		$correct = '5';
		$correctResult = true;
		$context = 'jak sie masz "Niepubliczna Służba Zdrowia" no ba!';
		$contextInstance = new KontorX_Search_Semantic_Context($context);

		$this->_interpreter = new KontorX_Search_Semantic_Interpreter_ArrayKeyValueExsists(
			array (
                'kontorx_numeric_key_0' => 
                array (
                  'key' => 'Niepubliczna Służba Zdrowia',
                  'value' => '5',
                ),
                'kontorx_numeric_key_1' => 
                array (
                  'key' => 'Pakiet usług dla firm',
                  'value' => '6',
                )
           )
		);
		
		$result = $this->_interpreter->interpret($contextInstance);
		$this->assertIdentical($result, $correctResult, "Interpretacja kontekstu powinna zwrócić 'true'");
		$data = $contextInstance->getOutput();

//		$this->dump($data);
		
		$message = sprintf('Znaleziona dzień "%s" w tekscie "%s" jest inny od oczekiwanego "%s"', $data, $context, $correct);
		$this->assertEqual($data, $correct, $message);
    } 
}

$r = new KontorX_Search_Semantic_Interpreter_ArrayKeyValueExsistsTest();
$r->run(new TextReporter());