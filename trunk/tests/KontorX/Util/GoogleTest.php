<?php
if (!defined('SETUP_TEST')) 
{
	require_once '../../setupTest.php';
}

define('GOOGLE_FILENAME_TEST_1', dirname(__FILE__) . '/google.html');
define('GOOGLE_FILENAME_TEST_2', dirname(__FILE__) . '/google.places.html');

require_once 'KontorX/Util/Google.php';
class KontorX_Util_GoogleTest extends UnitTestCase 
{

	/**
	 * @var KontorX_Util_Google
	 */
	protected $_google;
	
	public function setUp() 
	{
	    $this->_google = new KontorX_Util_Google('mostowy.com.pl');
	    $this->_google->setKeyword('tłumacz przysięgły angielskiego kraków');
	    $this->_google->setData(file_get_contents(GOOGLE_FILENAME_TEST_2));
	}

	public function tearDown() 
	{
	    $this->_google = null;
	}

	public function testGoogleLiveResult() 
	{
		$google = new KontorX_Util_Google('google.pl');
		$position = $google->position('google.pl', 10);
		//$this->dump($position);
		$this->assertNotEqual(0, $position, "pozycja w google jest = 0, fail!");
	}
	
	public function testGoogleTypeXpathLiveResult() 
	{
		$google = new KontorX_Util_Google('google.pl');
		$google->setType(KontorX_Util_Google::TYPE_XPATH);
		$position = $google->position('google.pl', 10);

		/*
		$this->dump(array(
		    '$position' => $position,
		    'organic' => $google->getOrganicPosition(),
		    'local' => $google->getLocalPosition()
		));
		*/
	
		$this->assertNotEqual(0, $position, "pozycja w google via xpath jest = 0, fail!");
		
		
	}
	
	public function testGetGoogleHtmlForPlaces()
	{
	    if (file_exists(GOOGLE_FILENAME_TEST_2)) {
	        return;
	    }

		$googleHtmlData = $this->_google->getData();
		
		$this->assertTrue($googleHtmlData, 'Nie udało się pobrać danch z google lokalne wyszukiwanie');
		
		if ($googleHtmlData)
		{
		    $result = file_put_contents(GOOGLE_FILENAME_TEST_2, $googleHtmlData);
		    $this->assertTrue($result, 'Nie udało się zapisać danch z google lokalne wyszukiwanie');
		}
	}
	
    public function testOnPage()
	{
	    $this->_google->setOnPage(13);
	    $result = $this->_google->getOnPage();
	    $this->assertEqual(13, $result, 'Incorrect value "OnPpage" set-get');
	}
	
    public function testPerPage()
	{
	    $this->_google->setPerPage(13);
	    $result = $this->_google->getPerPage();
	    $this->assertEqual(13, $result, 'Incorrect value "PerPage" set-get');
	}
	
    public function testSiteUri()
	{
	    $value = 'mostowy.com.pl';
	    $this->_google->setSiteUri($value);
	    $result = $this->_google->getSiteUri();
	    $this->assertEqual($value, $result, 'Incorrect value "SiteUri" set-get');
	}
	
    public function testData()
	{
	    $value = file_get_contents(GOOGLE_FILENAME_TEST_1);
	    $this->_google->setData($value);
	    $result = $this->_google->getData();
	    $this->assertEqual($value, $result, 'Incorrect value "Data" set-get');
	}
	
    public function testGoogleLocalSearchViaXpath()
	{
	    $position = $this->_google->getLocalPosition();
	    /*
	    $this->dump(array(
		    'organic' => $this->_google->getOrganicPosition(),
		    'local' => $this->_google->getLocalPosition()
		));
		*/
	    $this->assertNotEqual($position, 1, sprintf('S/K: "tłumacz przysięgły angielskiego kraków" jest na nieprawidłowym miejscu! %s',$position));
	}
	
    public function testGoogleLocalSearchViaXpathFromLocalData()
	{
	    $data = file_get_contents(GOOGLE_FILENAME_TEST_2);
	    $this->_google->setData($data);
	    $position = $this->_google->getLocalPosition();
	    
	    $this->dump(array(
		    'organic' => $this->_google->getOrganicPosition(),
		    'local' => $this->_google->getLocalPosition()
		));

	    $this->assertNotEqual($position, 2, sprintf('S/K: "tłumacz przysięgły angielskiego kraków" jest na nieprawidłowym miejscu! %s',$position));
	}
	
//	public function testGoogleResultTypeXpathTestKeyword() 
//	{
//		$link = 'fryzjerzy.krakow.pl';
//		$keyword = 'fryzjerzy krak�w';
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
//		$this->assertNotEqual(0, $positionA, "pozycja strony via xpath '".$link."' w google.pl pod s�owem kluczowym '".$keyword."' jest nieprawid�owa");
//		$this->assertNotEqual(0, $positionB, "pozycja strony via default '".$link."' w google.pl pod s�owem kluczowym '".$keyword."' jest nieprawid�owa");
//	}
}

$test = new KontorX_Util_GoogleTest();
$test->run(new TextReporter());