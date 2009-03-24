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

	public function testOutputRemoveAndNext1() {
		$correctCurrent1 = "Dzisiaj";
		$correctCurrent2 = null;
		$correctCurrent3 = "jest";
		$correct = "jest poniedziałek";
		$context = "Dzisiaj jest poniedziałek";
		
		$this->_context->setInput($context);
		$current1 = $this->_context->current(); // Dzisiaj
		$this->assertEqual($current1, $correctCurrent1, sprintf("Next '%s' różny od '%s'", $current1, $correctCurrent1));

		$this->_context->remove(); 				// remove Dzisiaj
		$current2 = $this->_context->current(); // null
		$this->assertEqual($current2, $correctCurrent2, sprintf("Next '%s' różny od '%s'", $current2, $correctCurrent2));

		$this->_context->next();				// move pointer to: jest
		$current3 = $this->_context->current(); // jest
		$this->assertEqual($current3, $correctCurrent3, sprintf("Next '%s' różny od '%s'", $current3, $correctCurrent3));

		$result = $this->_context->getInput();
		$this->assertEqual($result, $correct, sprintf("Output nie jest taki sam jak oczekiwany '%s'", $correct));
    }
    
	public function testOutputRemoveAndNext2() {
		$correctCurrent1 = "Dzisiaj";
		$correctCurrent2 = null;
		$correctCurrent3 = null;
		$correct = array();
		$context = "Dzisiaj";
		
		$this->_context->setInput($context);
		$current1 = $this->_context->current(); // Dzisiaj
		$this->assertEqual($current1, $correctCurrent1, sprintf("Next '%s' różny od '%s'", $current1, $correctCurrent1));

		$this->_context->remove(); 				// remove Dzisiaj
		$current2 = $this->_context->current(); // null
		$this->assertIdentical($current2, $correctCurrent2, sprintf("Next '%s' różny od '%s'", $current2, $correctCurrent2));

		$this->_context->next();				// null
		$current3 = $this->_context->current(); // null
		$this->assertEqual($current3, $correctCurrent3, sprintf("Next '%s' różny od '%s'", $current3, $correctCurrent3));

		$result = $this->_context->getInput();
		$this->dump($result);
		$this->assertEqual($result, $correct, sprintf("Output nie jest taki sam jak oczekiwany '%s'", $correct));
    }
}

$r = new KontorX_Search_Semantic_ContextTest();
$r->run(new TextReporter());