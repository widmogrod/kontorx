<?php
if (!defined('SETUP_TEST')) {
	require_once '../../setupTest.php';
}

/**
 * @see KontorX_Calendar_Month 
 */
require_once 'KontorX/Calendar/Month.php';

class KontorX_Calendar_MonthTest extends UnitTestCase {
	
	protected $_timestamp = 1239002793;

	/**
	 * @var KontorX_Calendar_Month
	 */
	protected $_month = null;
	
	public function setUp() {
		$this->_month = new KontorX_Calendar_Month($this->_timestamp);
	}
	
	public function tearDown() {
		$this->_month = null;
	}
	
	public function testDay() {
		$this->assertEqual($this->_month->getDay(), date('j',$this->_timestamp), "Dni się różnią od siebie");
    }
    
	public function testDays() {
		$this->assertEqual($this->_month->getDays(), date('t',$this->_timestamp), "Liczba dni jest różna");
    }
    
	public function testWeeksCount() {
		$this->assertEqual($this->_month->getWeeksCount(), 5, "Liczna tygodni jest różna");
    }
    
	public function testMonthStartFromDay() {
		$this->assertEqual($this->_month->getMonthStartFromDay(), 2, "Miesiąc rozpoczyna się od środy = 2 (bo od 0)");
    }
    
	public function testCurrentWeek() {
		$this->assertEqual($this->_month->getCurrentWeek(), 2, "Aktualny tydzień 2gi");
    }
    
    public function testWeekDay() {
		$this->assertEqual($this->_month->getWeekDay(), 0, "Aktualny dzień tygodnia 1 (poniedziałek)");
    }
    
 	public function testMonthName() {
		$this->assertEqual($this->_month->getMonthName(), KontorX_Calendar_Month::APRIL, "Zła nazwa miesiąca");
    }
}

$r = new KontorX_Calendar_MonthTest();
$r->run(new TextReporter());