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

	public function testGoogleResout() 
	{
		$google = new KontorX_Util_Google('google.pl');
		$position = $google->position('google.pl', 10);
		$this->assertEqual(1, $position, "pozycje nie są identyczne");
	}
}

$test = new KontorX_Util_GoogleTest();
$test->run(new TextReporter());