<?php
if (!defined('SETUP_TEST')) {
	require_once '../../setupTest.php';
}


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
	
	public function testQuotedText() {
		$correctCurrent1 = "to";
		$correctCurrent2 = "jest";
		// @todo str_replace .. stądten dysonans w wartościach
		$correctCurrent3 = "cytowany ,  12a";
		$correctCurrent4 = "22 tekst";
		$correctCurrent5 = "a";
		$correctCurrent6 = "jak!";
		$context = 'to jest "cytowany, 12a" "22 tekst" a jak!';
		
		$this->_context->setInput($context);
		
		$current1 = $this->_context->current(); // to
		$this->assertEqual($current1, $correctCurrent1, sprintf("Next '%s' różny od '%s'", $current1, $correctCurrent1));
		
		$this->_context->next();
		
		$current2 = $this->_context->current(); // jest
		$this->assertEqual($current2, $correctCurrent2, sprintf("Next '%s' różny od '%s'", $current2, $correctCurrent2));
		
		$this->_context->next();
		
		$current3 = $this->_context->current(); // cytowany, 12a
		$this->assertEqual($current3, $correctCurrent3, sprintf("Next '%s' różny od '%s'", $current3, $correctCurrent3));
		
		$this->_context->next();
		
		$current4 = $this->_context->current(); // 22 tekst
		$this->assertEqual($current4, $correctCurrent4, sprintf("Next '%s' różny od '%s'", $current4, $correctCurrent4));
		
		$this->_context->next();
		
		$current5 = $this->_context->current(); // a
		$this->assertEqual($current5, $correctCurrent5, sprintf("Next '%s' różny od '%s'", $current5, $correctCurrent5));
		
		$this->_context->next();
		
		$current6 = $this->_context->current(); // jak!
		$this->assertEqual($current6, $correctCurrent6, sprintf("Next '%s' różny od '%s'", $current6, $correctCurrent6));

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
//		$this->dump($result);
		$this->assertEqual($result, $correct, sprintf("Output nie jest taki sam jak oczekiwany '%s'", $correct));
    }
}

$r = new KontorX_Search_Semantic_ContextTest();
$r->run(new TextReporter());