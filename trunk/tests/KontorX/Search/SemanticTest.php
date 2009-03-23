<?php
require_once '../../setupTest.php';

/**
 * @see KontorX_Search_Semantic 
 */
require_once 'KontorX/Search/Semantic.php';

/**
 * @see KontorX_Search_Semantic_Context 
 */
require_once 'KontorX/Search/Semantic/Context.php';

class KontorX_Search_SemanticTest extends UnitTestCase {
	/**
	 * @var KontorX_Search_Semantic
	 */
	protected $_semantic = null;
	
	public function setUp() {
		$this->_semantic = new KontorX_Search_Semantic();
	}
	
	public function tearDown() {
		$this->_semantic = null;
	}
	
	/**
	 * @return KontorX_Search_Semantic_Interpreter_Date
	 */
	protected function _getInterpreterDate() {
	if (!class_exists('KontorX_Search_Semantic_Interpreter_Date', false)) {
			require_once 'KontorX/Search/Semantic/Interpreter/Date.php';
		}
		return new KontorX_Search_Semantic_Interpreter_Date();
	}
	
	/**
	 * @return KontorX_Search_Semantic_Interpreter_InArray
	 */
	protected function _getInterpreterInArrayWeeks() {
		if (!class_exists('KontorX_Search_Semantic_Interpreter_InArray', false)) {
			require_once 'KontorX/Search/Semantic/Interpreter/InArray.php';
		}
		return new KontorX_Search_Semantic_Interpreter_InArray(array(
			'poniedziałek','wtorek','środa','czwartek','piątek','sobota','niedziela'
		));		
	}
	
	/**
	 * @return KontorX_Search_Semantic_Interpreter_ArrayKeyExsists
	 */
	protected function _getInterpreterArrayKeyExsistsWeeks() {
		if (!class_exists('KontorX_Search_Semantic_Interpreter_ArrayKeyExsists', false)) {
			require_once 'KontorX/Search/Semantic/Interpreter/ArrayKeyExsists.php';
		}
		return new KontorX_Search_Semantic_Interpreter_ArrayKeyExsists(array(
			'poniedziałek' => 1,
			'wtorek' => 2,
			'środa' => 3,
			'czwartek' => 4,
			'piątek' => 5,
			'sobota' => 6,
			'niedziela' => 7
		));
	}

	function testInterpreterInArray1() {
		$correct = array('weeks' => 'poniedziałek');
		$context = 'Dzisiaj jest poniedziałek';
		$contextInstance = new KontorX_Search_Semantic_Context($context);
		
		$this->_semantic->addInterpreter($this->_getInterpreterInArrayWeeks(), 'weeks');

		$this->_semantic->interpret($contextInstance);

		$data = $contextInstance->getOutput();
		$this->dump($data, "Wynik zapytania: '$context'");

		$this->assertIdentical($data, $correct, "Interpreter nie znalazl poniedzialku");
    }
    
	public function testLogic1() {
		$correct = array('week'=>'poniedziałek','date'=>11);
		$context = "lekarz przyjmuje w 12 poniedziałek o 11";
		$contextInstance = new KontorX_Search_Semantic_Context($context);
		
		require_once 'KontorX/Search/Semantic/Logic/AndLogic.php';
		$and1 = new KontorX_Search_Semantic_Logic_AndLogic();
		$and1->addInterpreter($this->_getInterpreterInArrayWeeks(),'week');
		$and1->addInterpreter($this->_getInterpreterDate(),'hour');

		$and2 = new KontorX_Search_Semantic_Logic_AndLogic();
		$and2->addInterpreter($this->_getInterpreterDate(),'hour');
		$and2->addInterpreter($this->_getInterpreterInArrayWeeks(),'week');

		require_once 'KontorX/Search/Semantic/Logic/OrLogic.php';
		$or = new KontorX_Search_Semantic_Logiccxc_OrLogic();
		$or->addInterpreter($and1,'and1');
		$or->addInterpreter($and2,'and2');

		$this->_semantic->addInterpreter($or,'or');
		$this->_semantic->interpret($contextInstance);
		$data = $contextInstance->getOutput();
		$this->dump($data);

		$message = sprintf('Fraza "%s" niepoprawnie rozpoznana', $context);
		$this->assertEqual($data, $correct, $message);
    }
}

$r = new KontorX_Search_SemanticTest();
$r->run(new TextReporter());