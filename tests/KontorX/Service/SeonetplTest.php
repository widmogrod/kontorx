<?php
if (!defined('SETUP_TEST')) {
	require_once '../../setupTest.php';
}

/**
 * @see KontorX_Service_Seonetpl 
 */
require_once 'KontorX/Service/Seonetpl.php';

class KontorX_Service_SeonetplTest extends UnitTestCase {
	/**
	 * @var KontorX_Service_Seonetpl
	 */
	protected $_seonetpl = null;
	
	public function setUp() 
	{
		$this->_seonetpl = new KontorX_Service_Seonetpl();
		$this->_seonetpl->setUsername('ligol');
		$this->_seonetpl->setPassword('sen00k3');
	}
	
	public function tearDown() 
	{
		$this->_seonetpl = null;
	}
	
	public function testIsAuthorisedSuccess() 
	{
		$this->_seonetpl->authorise();
		
		$result = $this->_seonetpl->isAuthorised();
		$this->assertTrue($result, 'User is not authorised');
	}
	
	public function testIsAuthorisedFailed() 
	{	
		$this->_seonetpl->setUsername('NoExists');
		$this->_seonetpl->authorise();
		$result = $this->_seonetpl->isAuthorised();
		$this->assertFalse($result, 'User is authorised!');
	}
	
	public function testGetLinkSetSuccess() 
	{	
		$this->_seonetpl->authorise();
		$result = $this->_seonetpl->getLinkSets();
		
		$this->assertTrue(is_array($result), 'There is no link sets for user');
	}
	
	public function testGetExportDataForFirstLinkSet() 
	{	
		$this->_seonetpl->authorise();
		$links = $this->_seonetpl->getLinkSets();
		
		$result = false;
		$linkSetId = null;
		foreach ($links as $link)
		{
			$linkSetId = $link['id'];
			$result = $this->_seonetpl->exportDataForLinkSet($linkSetId);
			break;
		}

		$this->assertTrue(is_array($result), 'There is no export data for user link set = '.$linkSetId);
	}
	
	public function testGetExportDataForCustomLinkSet() 
	{	
		$this->_seonetpl->authorise();
		$links = $this->_seonetpl->getLinkSets();

		$linkSetId = 91794; // 2rsystem.com.pl
		$result = $this->_seonetpl->exportDataForLinkSet($linkSetId);

		$this->assertTrue(is_array($result), 'There is no export data for user link set = '.$linkSetId);
	}
	
//	public function testSetExportDataForCustomLinkSet() 
//	{	
//		$this->_seonetpl->authorise();
//		$links = $this->_seonetpl->getLinkSet();
//
//		$linkSetId = 91794; // 2rsystem.com.pl
//		$data = $this->_seonetpl->exportDataForLinkSet($linkSetId);
//		$result = $this->_seonetpl->importDataForLinkSet($data, $linkSetId);
//
//		$this->assertTrue($result, 'Failed importing data for link set = '.$linkSetId);
//	}

	public function testGetSetLinksSuccess() 
	{	
		$linkSetId = 91794; // 2rsystem.com.pl
		
		$result = $this->_seonetpl->getSetLinks($linkSetId);
		$this->assertTrue(is_array($result), 'There is no link sets for user');
	}
	
	public function testSetLinksMaxSuccess() 
	{	
		$max = 3000;
		$linkId = 5441591; // inteligentny bdynek
		$linkSetId = 91794; // 2rsystem.com.pl

		$result = $this->_seonetpl->setLinksMax($max, $linkId, $linkSetId);
		$this->assertTrue($result, 'Can\'t set $max do link');
	}
	
	public function testSetLinksPerdaySuccess() 
	{	
		$perday = 20;
		$linkId = 5441591; // inteligentny bdynek
		$linkSetId = 91794; // 2rsystem.com.pl

		$result = $this->_seonetpl->setLinksPerday($perday, $linkId, $linkSetId);
		$this->assertTrue($result, 'Can\'t set perday do link');
	}
	
	public function testSetLinksPrioritySuccess() 
	{	
		$priority = 10;
		$linkId = 5441591; // inteligentny bdynek
		$linkSetId = 91794; // 2rsystem.com.pl

		$result = $this->_seonetpl->setLinksPriority($priority, $linkId, $linkSetId);
		$this->assertTrue($result, 'Can\'t set $priority do link');
	}
	
//	public function testDeleteLinkSuccess() 
//	{	
//		$linkId = 6696314;
//		$linkSetId = 91794; // 2rsystem.com.pl
//
//		$result = $this->_seonetpl->deleteLink($linkId, $linkSetId);
//		$this->assertTrue($result, 'Can\'t delete link');
//	}
	
	public function testDeleteLinkFailure() 
	{	
		$linkId = 123321123;
		$linkSetId = 91794; // 2rsystem.com.pl

		$result = $this->_seonetpl->deleteLink($linkId, $linkSetId);
		$this->assertFalse($result, 'Can\'t delete link');
	}
}

$r = new KontorX_Service_SeonetplTest();
$r->run(new TextReporter());