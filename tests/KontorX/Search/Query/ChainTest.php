<?php
require_once '../../../setupTest.php';

/**
 * @see KontorX_Search_Semantic_Query_Date 
 */
require_once 'KontorX/Search/Semantic/Query/Chain.php';

class KontorX_Search_Semantic_Query_ChainTest extends UnitTestCase {
	
	/**
	 * @var KontorX_Search_Semantic_Query_Chain
	 */
	protected $_query = null;
	
	public function setUp() {
		$this->_query = new KontorX_Search_Semantic_Query_Chain();
	}
	
	public function tearDown() {
		$this->_query = null;
	}
 	
	/**
	 * @var KontorX_Search_Semantic_Query_Date
	 */
	private $_queryDate = null; 
	
	/**
	 * @return KontorX_Search_Semantic_Query_Date
	 */
	protected function _getQueryDate() {
		if (null === $this->_queryDate) {
			require_once 'KontorX/Search/Semantic/Query/Date.php';
			$this->_queryDate = new KontorX_Search_Semantic_Query_Date();
		}
		return $this->_queryDate;
	}
	
	/**
	 * @var KontorX_Search_Semantic_Query_InArray
	 */
	private $_queryInArrayWeeks = null; 
	
	/**
	 * @return KontorX_Search_Semantic_Query_InArray
	 */
	protected function _getQueryInArrayWeeks() {
		if (null === $this->_queryInArrayWeeks) {
			require_once 'KontorX/Search/Semantic/Query/InArray.php';
			$this->_queryInArrayWeeks = new KontorX_Search_Semantic_Query_InArray(array(
				'poniedziałek','wtorek','środa','czwartek','piątek','sobota','niedziela'
			));
		}
		return $this->_queryInArrayWeeks;
	}

//	public function testChainEmpty() {
//		$content = "To jes treśc";
//		try {
//			$result = $this->_query->query($content);
//			$this->fail("Pusty łancuch 'Query' nie rzucił wyjątku");
//		} catch (KontorX_Search_Semantic_Exception $e) {
//			$this->assertError($e->getMessage(),'Wiadomosć');
//		}
//    }
	
	public function testChainElementsDateAndInArray1() {
		$correct = array('poniedziałek',11);
		$content = "poniedziałek 11";
		
		$this->_query->addQuery($this->_getQueryInArrayWeeks());
		$this->_query->addQuery($this->_getQueryDate());

		$data = $this->_query->query($content);

		$message = sprintf('Fraza "%s" niepoprawnie rozpoznana', $content);
		$this->assertEqual($data, $correct, $message);
    }

	public function testChainElementsDateAndInArray2() {
		$content = "może 11";
		
		$this->_query->addQuery($this->_getQueryInArrayWeeks());
		$this->_query->addQuery($this->_getQueryDate());

		$data = $this->_query->query($content);
		
		$message = sprintf('Fraza "%s" nie powinna zostać rozpoznana', $content);
		$this->assertEqual($data, null, $message);
    }
}

$r = new KontorX_Search_Semantic_Query_ChainTest();
$r->run(new TextReporter());