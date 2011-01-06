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
		$result = $this->_seonetpl->getLinkSet();
		
		$this->assertTrue(is_array($result), 'There is no link sets for user');
	}
	
	public function testGetExportDataForFirstLinkSet() 
	{	
		$this->_seonetpl->authorise();
		$links = $this->_seonetpl->getLinkSet();
		
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
		$links = $this->_seonetpl->getLinkSet();

		$linkSetId = 91794; // 2rsystem.com.pl
		$result = $this->_seonetpl->exportDataForLinkSet($linkSetId);

		$this->assertTrue(is_array($result), 'There is no export data for user link set = '.$linkSetId);
	}
}

$r = new KontorX_Service_SeonetplTest();
$r->run(new TextReporter());