<?php
if (!defined('SETUP_TEST')) 
{
	require_once '../../setupTest.php';
}

require_once 'KontorX/Util/Google.php';
class KontorX_Util_GoogleTest extends UnitTestCase 
{

	/**
	 * @var string
	 */
	protected $_updatePath;
	
	public function setUp() {}
	
	public function tearDown() {}

	public function testGoogleResult() 
	{
		$google = new KontorX_Util_Google('google.pl');
		$position = $google->position('google.pl', 10);
		$this->dump($position);
		$this->assertNotEqual(0, $position, "pozycja w google jest = 0, fail!");
	}
	
	public function testGoogleTypeXpathResult() 
	{
		$google = new KontorX_Util_Google('google.pl');
		$google->setType(KontorX_Util_Google::TYPE_XPATH);
		$position = $google->position('google.pl', 10);
		$this->dump($position);
		$this->assertNotEqual(0, $position, "pozycja w google via xpath jest = 0, fail!");
	}
	
//	public function testGoogleResultTypeXpathTestKeyword() 
//	{
//		$link = 'fryzjerzy.krakow.pl';
//		$keyword = 'fryzjerzy krak—w';
//		
//		$google = new KontorX_Util_Google($link);
//		$google->setType(KontorX_Util_Google::TYPE_XPATH);
//		$positionA = $google->position($keyword, 200);
//		
//		$google->setType(KontorX_Util_Google::TYPE_DEFAULT);
//		$positionB = $google->position($keyword, 200);
//		
//		var_dump($positionA);
//		var_dump($positionB);
//		
//		$this->assertNotEqual(0, $positionA, "pozycja strony via xpath '".$link."' w google.pl pod s¸owem kluczowym '".$keyword."' jest nieprawid¸owa");
//		$this->assertNotEqual(0, $positionB, "pozycja strony via default '".$link."' w google.pl pod s¸owem kluczowym '".$keyword."' jest nieprawid¸owa");
//	}
}

$test = new KontorX_Util_GoogleTest();
$test->run(new TextReporter());