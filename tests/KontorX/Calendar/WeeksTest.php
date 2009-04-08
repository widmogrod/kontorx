<?php
if (!defined('SETUP_TEST')) {
	require_once '../../setupTest.php';
}

/**
 * @see KontorX_Calendar_Weeks
 */
require_once 'KontorX/Calendar/Weeks.php';

class KontorX_Calendar_WeeksTest extends UnitTestCase {
	
	protected $_timestamp = 1239002793;

	/**
	 * @var KontorX_Calendar_Weeks
	 */
	protected $_weeks = null;
	
	public function setUp() {
		$this->_weeks = new KontorX_Calendar_Weeks($this->_timestamp);
	}
	
	public function tearDown() {
		$this->_weeks = null;
	}
	
	public function testKey_WeekNumber() {
		$this->assertEqual($this->_weeks->key(), 15, "Numer tygodnia jest nieprawidłowy");
    }
    
	public function testCount_WeeksCount() {
		$this->assertEqual($this->_weeks->count(), 53, "Liczba tygodni w roku jest nieprawidłowa");
    }

	public function testNextPrev() {
		$current = $this->_weeks->current();

		$this->_weeks->next();
		$next = $this->_weeks->current();
		
		$this->_weeks->preview();
		$prev = $this->_weeks->current();
		
		$this->assertEqual($current, $prev, "Tygodnie nie są identyczne");
		$this->assertNotEqual($current, $next, "Tygodnie nie powinny być identyczne");
    }
    
    public function testRewind() {
    	$this->_weeks->rewind();
    	$number = $this->_weeks->key();
    	$this->assertEqual($number, 1, "Numer tygodnia nie jest prawidłowy");
    }

    public function testMonthLimit() {
    	$this->_weeks->setMonthLimit(true);
    	$this->assertEqual($this->_weeks->getMinWeek(), 14, "Ograniczenie tygodnia do miesiąca jest nieprawidłowe - min");
    	$this->assertEqual($this->_weeks->getMaxWeek(), 18, "Ograniczenie tygodnia do miesiąca jest nieprawidłowe - max");
    }
}

$r = new KontorX_Calendar_WeeksTest();
$r->run(new TextReporter());