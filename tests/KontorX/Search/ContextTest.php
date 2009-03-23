<?php
require_once '../../setupTest.php';

/**
 * @see KontorX_Search_Semantic_Context 
 */
require_once 'KontorX/Search/Semantic/Context.php';

class KontorX_Search_Semantic_ContextTest extends UnitTestCase {
	
	/**
	 * @var KontorX_Search_Semantic_Context
	 */
	protected $_context = null;
	
	public function setUp() {
		$this->_context = new KontorX_Search_Semantic_Context();
	}
	
	public function tearDown() {
		$this->_context = null;
	}

	public function testSetGet() {
		$correct = "Dzisiaj jest poniedziałek";
		$context = "Dzisiaj jest poniedziałek";
		
		$this->_context->setInput($context);
		$result = $this->_context->getInput();

		$this->assertEqual($result, $correct, sprintf("Input nie jest taki sam jak oczekiwany '%s'", $correct));
    }
    
	public function testOutput() {
		$correct = array();
		$context = "Dzisiaj jest poniedziałek";
		
		$this->_context->setOutput($context);
		$this->_context->clearOutput();
		$result = $this->_context->getOutput();

		$this->assertEqual($result, $correct, sprintf("Output nie jest taki sam jak oczekiwany '%s'", $correct));
    }
    
	public function testOutput2() {
		$correct = "Dzisiaj jest poniedziałek";
		$context = "Dzisiaj jest poniedziałek";
		
		$this->_context->setOutput($context);
		$result = $this->_context->getOutput();

		$this->assertEqual($result, $correct, sprintf("Output nie jest taki sam jak oczekiwany '%s'", $correct));
    }
    
	public function testOutput3() {
		$correct = "Dzisiaj jest poniedziałek";
		$context = "Dzisiaj jest poniedziałek";
		
		$this->_context->setInput($context);
		$this->_context->next();
		$result = $this->_context->getInput();

		$this->assertEqual($result, $correct, sprintf("Output nie jest taki sam jak oczekiwany '%s'", $correct));
    }
    
	public function testOutputRemove() {
		$correct = "jest poniedziałek";
		$context = "Dzisiaj jest poniedziałek";
		
		$this->_context->setInput($context);
		$this->_context->remove();
		$result = $this->_context->getInput();

		$this->assertEqual($result, $correct, sprintf("Output nie jest taki sam jak oczekiwany '%s'", $correct));
    }	
}

$r = new KontorX_Search_Semantic_ContextTest();
$r->run(new TextReporter());