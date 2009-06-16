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
		'Observable_Test_Success',
		'Observable_Test_Failure'
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
	
	public function testNotifySuccess() {
		$statusBefore = $this->_list->getStatus('Observable_Test_Success');
		$this->assertIdentical($statusBefore, null, 'Status $statusBefore nie jest identyczny');

		$this->_list->notify();

		$statusAfter = $this->_list->getStatus('Observable_Test_Success');
		$this->assertIdentical($statusAfter,
			Promotor_Observable_Observer_Abstract::SUCCESS,
			sprintf('Status $statusAfter "%s" nie jest identyczny', $statusAfter));

    }

	public function testNotifyFailure() {
		$statusBefore = $this->_list->getStatus('Observable_Test_Failure');
		$this->assertIdentical($statusBefore, null, 'Status $statusBefore nie jest identyczny');

		$this->_list->notify();

		$statusAfter = $this->_list->getStatus('Observable_Test_Failure');
		$this->assertIdentical($statusAfter,
			Promotor_Observable_Observer_Abstract::FAILURE,
			sprintf('Status $statusAfter "%s" nie jest identyczny', $statusAfter));
    }
}

$r = new Promotor_Observable_ListTest();
$r->run(new TextReporter());