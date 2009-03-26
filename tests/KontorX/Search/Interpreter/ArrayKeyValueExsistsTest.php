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
    
	public function testArrayKeyValueExsistsMultiCorrectTrue() {
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
    // @todo Może nowy interpreter, który będzie alizował treść sorawdzał dopasowanie i odwołanie
    // do konkretnych rekoród .. przypisywał wagi.. zalatuje SI
	/*public function testArrayKeyValueExsistsBigArray() {
		$correct = "Niepubliczna Służba Zdrowia";
		$correctResult = true;
		$context = "Niepubliczna Służba Zdrowia";
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
                ),
                'kontorx_numeric_key_2' => 
                array (
                  'key' => 'Pakiet usług dla indywidualnych klientów',
                  'value' => '7',
                ),
                'kontorx_numeric_key_3' => 
                array (
                  'key' => 'Akceptacja kart kredytowych',
                  'value' => '8',
                ),
                'kontorx_numeric_key_4' => 
                array (
                  'key' => 'Obsługa w języku angielskim',
                  'value' => '9',
                ),
                'kontorx_numeric_key_5' => 
                array (
                  'key' => 'Parking',
                  'value' => '10',
                ),
                'kontorx_numeric_key_6' => 
                array (
                  'key' => 'Udogodnienia dla niepełnosprawnych',
                  'value' => '11',
                ),
                'kontorx_numeric_key_7' => 
                array (
                  'key' => 'Zapewniamy tłumacza',
                  'value' => '12',
                ),
                'kontorx_numeric_key_8' => 
                array (
                  'key' => 'Zapraszamy również "trudnych" pacjentów',
                  'value' => '13',
                ),
                'kontorx_numeric_key_9' => 
                array (
                  'key' => 'Przyjmujemy pacjentów wymagających specjalnej opieki',
                  'value' => '14',
                ),
                'kontorx_numeric_key_10' => 
                array (
                  'key' => 'Miejsce zabaw dla dzieci',
                  'value' => '15',
                ),
                'kontorx_numeric_key_11' => 
                array (
                  'key' => 'Możliwe rabaty',
                  'value' => '16',
                ),
                'kontorx_numeric_key_12' => 
                array (
                  'key' => 'Możliwa wcześniejsza kalkulacja',
                  'value' => '17',
                ),
                'kontorx_numeric_key_13' => 
                array (
                  'key' => 'Darmowa kosultacja',
                  'value' => '18',
                ),
                'kontorx_numeric_key_14' => 
                array (
                  'key' => 'Witamy starszych pacjentów',
                  'value' => '19',
                ),
                'kontorx_numeric_key_15' => 
                array (
                  'key' => 'Mile widziane dzieci',
                  'value' => '20',
                ),
                'kontorx_numeric_key_16' => 
                array (
                  'key' => 'Nagłe przypadki bez rejestracji',
                  'value' => '21',
                ),
                'kontorx_numeric_key_17' => 
                array (
                  'key' => 'Zniżki dla dzieci',
                  'value' => '23',
                ),
                'kontorx_numeric_key_18' => 
                array (
                  'key' => 'Zniżki dla studentów',
                  'value' => '24',
                ),
                'kontorx_numeric_key_19' => 
                array (
                  'key' => 'Możliwe wizyty po godzinach',
                  'value' => '25',
                ),
                'kontorx_numeric_key_20' => 
                array (
                  'key' => 'SMS przypominający o wizycie ',
                  'value' => '26',
                ),
                'kontorx_numeric_key_21' => 
                array (
                  'key' => 'Raty',
                  'value' => '27',
                ))
		);
		
		$result = $this->_interpreter->interpret($contextInstance);
		$this->assertIdentical($result, $correctResult, "Interpretacja kontekstu powinna zwrócić 'true'");
		$data = $contextInstance->getOutput();

		$this->dump($data);
		
		$message = sprintf('Znaleziona dzień "%s" w tekscie "%s" jest inny od oczekiwanego "%s"', $data, $context, $correct);
		$this->assertEqual($data, $correct, $message);
    }*/    
}

$r = new KontorX_Search_Semantic_Interpreter_ArrayKeyValueExsistsTest();
$r->run(new TextReporter());