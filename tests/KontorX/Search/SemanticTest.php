<?php
require_once '../../setupTest.php';
require_once 'KontorX/Search/Semantic.php';

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

	function testQueryInArray() {
		$content = 'Dzisiaj jest poniedziałek';
		$correct = array(array('poniedziałek'));
		
		$this->_semantic->addQuery($this->_getQueryInArrayWeeks());

		$data = $this->_semantic->query($content);

		$this->dump($data, "Wynik zapytania: '$content'");
		$this->assertIdentical($data, $correct, "Query nie znalazl poniedzialku");
    }
}

$r = new KontorX_Search_SemanticTest();
$r->run(new TextReporter());