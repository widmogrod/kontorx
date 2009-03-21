<?php
require_once '../../setupTest.php';
require_once 'KontorX/Search/Semantic.php';

class KontorX_Search_SemanticTest extends UnitTestCase {

function testQueryInArray() {
		$semantic = new KontorX_Search_Semantic();

		require_once 'KontorX/Search/Semantic/Query/InArray.php';
		$semantic->addQuery(new KontorX_Search_Semantic_Query_InArray(array(
			'poniedziałek' => 1,
			'poniedzialek' => 1,
			'wtorek' => 2,
			'środa' => 3,
			'sroda' => 3
		)));

		$content = 'Dzisiaj jest poniedziałek';
		$result = $semantic->query($content);

		$this->dump($result, "Wynik zapytania: '$content'");
		
		$corectResult = array(array('poniedziałek' => 1));
		$this->assertIdentical($result, $corectResult, "Query nie znalazl poniedzialku");
    }
}

$r = new KontorX_Search_SemanticTest();
$r->run(new TextReporter());