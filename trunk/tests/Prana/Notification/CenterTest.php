<?php
if (!defined('SETUP_TEST')) {
	require_once '../../setupTest.php';
}

/**
 * @see KontorX_Calendar_Weeks
 */
require_once 'Prana/Notification/Center.php';

class Promotor_Observable_ListTest extends UnitTestCase {
	
	const TestNotification  = 'TestNotification';
	
	// zmienne do metody pomocniczej - przechowywanie informacji o notyfikacji
	protected $_notificationName;
  	protected $_notificationObject;
  	protected $_notificationUserInfo;
	
	public function setUp() {
		$this->_notificationName = null;
		$this->_notificationObject = null;
		$this->_notificationUserInfo = null;
	}

	public function tearDown() {
		$this->_notificationName = null;
		$this->_notificationObject = null;
		$this->_notificationUserInfo = null;
	}

	/**
	 * Metoda pomocnicza
	 * 
	 * @param Prana_Notification $n
	 * @return unknown_type
	 */
	public function _helper_sampleObserver(Prana_Notification $n) {
		$this->_notificationName = $n->getName();
		$this->_notificationObject = $n->getObject();
		$this->_notificationUserInfo = $n->getUserInfo();
	}
	
	// Tests

	public function testAddObserverSuccess() {
		$result = Prana_Notification_Center::getInstance()->addObserver(array($this,'_helper_sampleObserver'), null);
		$this->assertTrue($result, 'Observer dodany nieprawidlowo');
    }

	public function testAddObserverException() {
		try {
			$result = Prana_Notification_Center::getInstance()->addObserver('_NotExsistingCallableFunction_', null);
			//$this->assertFalse($result, 'Observer nie powinien być dodany');
		} catch (Exception $e) { 
			// TODO!
			$this->assertTrue(true, 'Observer dodany nieprawidlowo');
		}		
    }
    
	public function testRemoveObserverSuccess() {
		$result = Prana_Notification_Center::getInstance()->removeObserver(array($this,'_helper_sampleObserver'));
		$this->assertTrue($result, 'Observer nie został usunięty');
    }
	
	public function testPostNotificationNotifySuccess() {
		Prana_Notification_Center::getInstance()->addObserver(array($this,'_helper_sampleObserver'), self::TestNotification);
		$result = Prana_Notification_Center::postNotificationName(self::TestNotification, null);
		Prana_Notification_Center::getInstance()->removeObserver(array($this,'_helper_sampleObserver'));

		$this->assertTrue($result, 'Powiadomienie nie zostało nadane');
    }

	public function testPostNotificationNameSuccess() {
		Prana_Notification_Center::getInstance()->addObserver(array($this,'_helper_sampleObserver'), self::TestNotification);
		Prana_Notification_Center::postNotificationName(self::TestNotification, null);
		Prana_Notification_Center::getInstance()->removeObserver(array($this,'_helper_sampleObserver'));

		$this->assertEqual($this->_notificationName, self::TestNotification, 'Otrzymano nadane powiadamienie o złej (różnej) nazwie od nazwy nadanej');
    }

	public function testPostNotificationObjectSuccess() {
		Prana_Notification_Center::getInstance()->addObserver(array($this,'_helper_sampleObserver'), self::TestNotification);
		Prana_Notification_Center::postNotificationName(self::TestNotification, $this);
		Prana_Notification_Center::getInstance()->removeObserver(array($this,'_helper_sampleObserver'));

		$this->assertEqual($this->_notificationObject,$this, 'Obiekt nadający powiadamienie jest różny');
    }
}

$r = new Promotor_Observable_ListTest();
$r->run(new TextReporter());