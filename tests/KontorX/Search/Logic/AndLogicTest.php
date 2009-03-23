<?php
require_once '../../../setupTest.php';

/**
 * @see KontorX_Search_Semantic_Query_Date 
 */
require_once 'KontorX/Search/Semantic/Logic/AndLogic.php';

/**
 * @see KontorX_Search_Semantic_Context 
 */
require_once 'KontorX/Search/Semantic/Context.php';

class KontorX_Search_Semantic_Logic_AndLogicTest extends UnitTestCase {
	
	/**
	 * @var KontorX_Search_Semantic_Logic_AndLogic
	 */
	protected $_logic = null;
	
	public function setUp() {
		$this->_logic = new KontorX_Search_Semantic_Logic_AndLogic();
	}
	
	public function tearDown() {
		$this->_logic = null;
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

	public function testAndForDateAndInArrayWeek() {
		$date = '11-03-2009';
		$correct = array();
		$context = "Dzisiaj jest poniedziałek $date";
		$contextInstance = new KontorX_Search_Semantic_Context($context);

		$this->_logic->addInterpreter($this->_getInterpreterDate(),'hour');
		$this->_logic->addInterpreter($this->_getInterpreterInArrayWeeks(),'week');
		$this->_logic->interpret($contextInstance);
		$data = $contextInstance->getOutput();
		
		$this->dump($data);
		
		$message = sprintf('Znalezione wyrażenie w tekscie "%s" jest różna od oczekiwanego', $context);
		$this->assertEqual($data, $correct, $message);
    }
    
	public function testAndForInArrayWeekAndDate() {
		$date = '11-03-2009';
		$correct = array('hour' => $date, 'week' => 'poniedziałek');
		$context = "Dzisiaj jest poniedziałek $date";
		$contextInstance = new KontorX_Search_Semantic_Context($context);

		$this->_logic->addInterpreter($this->_getInterpreterInArrayWeeks(),'week');
		$this->_logic->addInterpreter($this->_getInterpreterDate(),'hour');
		$this->_logic->interpret($contextInstance);
		$data = $contextInstance->getOutput();
		
		$this->dump($data);
		
		$message = sprintf('Znalezione wyrażenie w tekscie "%s" jest różna od oczekiwanego', $context);
		$this->assertEqual($data, $correct, $message);
    }
}

$r = new KontorX_Search_Semantic_Logic_AndLogicTest();
$r->run(new TextReporter());