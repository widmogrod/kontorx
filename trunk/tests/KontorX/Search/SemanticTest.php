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
	
	/**
	 * @return KontorX_Search_Semantic_Interpreter_ContextToSeparator
	 */
	protected function _getInterpreterContextToSeparator() {
		if (!class_exists('KontorX_Search_Semantic_Interpreter_ContextToSeparator', false)) {
			require_once 'KontorX/Search/Semantic/Interpreter/ContextToSeparator.php';
		}
		return new KontorX_Search_Semantic_Interpreter_ContextToSeparator();
	}
	
	/**
	 * @return KontorX_Search_Semantic_Logic_AndLogic
	 */
	protected function _getLogicAnd() {
		if (!class_exists('KontorX_Search_Semantic_Logic_AndLogic', false)) {
			require_once 'KontorX/Search/Semantic/Logic/AndLogic.php';
		}
		return new KontorX_Search_Semantic_Logic_AndLogic();
	}
	
	/**
	 * @return KontorX_Search_Semantic_Logic_OrLogic
	 */
	protected function _getLogicOr() {
		if (!class_exists('KontorX_Search_Semantic_Logic_OrLogic', false)) {
			require_once 'KontorX/Search/Semantic/Logic/OrLogic.php';
		}
		return new KontorX_Search_Semantic_Logic_OrLogic();
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
		
		$and1 = $this->_getLogicAnd();
		$and1->addInterpreter($this->_getInterpreterInArrayWeeks(),'week');
		$and1->addInterpreter($this->_getInterpreterDate(),'hour');

		$and2 = $this->_getLogicAnd();
		$and2->addInterpreter($this->_getInterpreterDate(),'hour');
		$and2->addInterpreter($this->_getInterpreterInArrayWeeks(),'week');

		$or = $this->_getLogicOr();
		$or->addInterpreter($and1,'and1');
		$or->addInterpreter($and2,'and2');

		$this->_semantic->addInterpreter($or,'or');
		$this->_semantic->interpret($contextInstance);
		$data = $contextInstance->getOutput();
		$this->dump($data);

		$message = sprintf('Fraza "%s" niepoprawnie rozpoznana', $context);
		$this->assertEqual($data, $correct, $message);
    }
    
	public function testLogic2() {
		$correct = array();
		$context = "ulica Opolska 13, godzina 22, dzień niedziela";
		$contextInstance = new KontorX_Search_Semantic_Context($context);
		
		if (!class_exists('KontorX_Search_Semantic_Interpreter_InArray', false)) {
			require_once 'KontorX/Search/Semantic/Interpreter/InArray.php';
		}

		// ulica
		$streetKeyword = new KontorX_Search_Semantic_Interpreter_InArray(array(
			'ul.','ulica','al.','aleja'
		));
		$streetLogicAnd = $this->_getLogicAnd();
		$streetLogicAnd->addInterpreter($streetKeyword, 'keyword');
		$streetLogicAnd->addInterpreter($this->_getInterpreterContextToSeparator(),'name');
		$this->_semantic->addInterpreter($streetLogicAnd,'ulicaAnd');
		
		// godzina
		$hourKeyword = new KontorX_Search_Semantic_Interpreter_InArray(array(
			'godzina'
		));
		$hourLogicAnd = $this->_getLogicAnd();
		$hourLogicAnd->addInterpreter($hourKeyword,'keyword');
		$hourLogicAnd->addInterpreter($this->_getInterpreterDate(),'hour');
		$hourLogicOr = $this->_getLogicOr();
		$hourLogicOr->addInterpreter($hourLogicAnd,'godzina1');
		$hourLogicOr->addInterpreter($this->_getInterpreterDate(),'godzina1');
		$this->_semantic->addInterpreter($hourLogicOr,'godzinaOr');
//
//		// dzień
//		$dayKeyword = new KontorX_Search_Semantic_Interpreter_InArray(array(
//			'dzień'
//		));
//		$dayLogicAnd = $this->_getLogicAnd();
//		$dayLogicAnd->addInterpreter($dayKeyword,'keyword');
//		$dayLogicAnd->addInterpreter($this->_getInterpreterDate(),'name');
//		$dayLogicOr = $this->_getLogicOr();
//		$dayLogicOr->addInterpreter($dayLogicAnd,'dzień1');
//		$dayLogicOr->addInterpreter($this->_getInterpreterDate(),'dzień2');
//		$this->_semantic->addInterpreter($dayLogicOr,'dzieńOr');

		$this->_semantic->interpret($contextInstance);
		$data = $contextInstance->getOutput();
		$this->dump($data);

		$message = sprintf('Fraza "%s" niepoprawnie rozpoznana', $context);
		$this->assertEqual($data, $correct, $message);
    }
}

$r = new KontorX_Search_SemanticTest();
$r->run(new TextReporter());