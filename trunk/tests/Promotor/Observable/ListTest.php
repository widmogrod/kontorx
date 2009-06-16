<?php
if (!defined('SETUP_TEST')) {
	require_once '../../setupTest.php';
}

/**
 * @see KontorX_Calendar_Weeks
 */
require_once 'Promotor/Observable/List.php';

// Observers
require_once 'Observable/Test.php';

class Promotor_Observable_ListTest extends UnitTestCase {
	
	/**
	 * @var array
	 */
	protected $_options = array(
		'Observable_Test'
	);
	
	/**
	 * @var Promotor_Observable_List
	 */
	protected $_list;
	
	public function setUp() {
		$this->_list = new Promotor_Observable_List($this->_options);
	}
	
	public function tearDown() {
		$this->_list = null;
	}
	
	public function testNotify() {
		$statusBefore = $this->_list->getStatus('Observable_Test');
		$this->assertIdentical($statusBefore, null, 'Status $statusBefore nie jest identyczny');

		$this->_list->notify();

		$statusAfter = $this->_list->getStatus('Observable_Test');
		$this->assertIdentical($statusAfter,
			Promotor_Observable_Observer_Abstract::SUCCESS,
			sprintf('Status $statusAfter "%s" nie jest identyczny', $statusAfter));
		
    }
}

$r = new Promotor_Observable_ListTest();
$r->run(new TextReporter());