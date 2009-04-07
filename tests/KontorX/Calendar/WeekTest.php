<?php
if (!defined('SETUP_TEST')) {
	require_once '../../setupTest.php';
}

/**
 * @see KontorX_Calendar_Week 
 */
require_once 'KontorX/Calendar/Week.php';

class KontorX_Calendar_WeekTest extends UnitTestCase {
	
	protected $_timestamp = 1239002793;

	/**
	 * @var KontorX_Calendar_Week
	 */
	protected $_week = null;
	
	public function setUp() {
		$this->_week = new KontorX_Calendar_Week($this->_timestamp);
	}
	
	public function tearDown() {
		$this->_week = null;
	}
	
	public function testKey_WeekNumber() {
		$this->assertEqual($this->_week->key(), 1, "Numer dnia jest nieprawidłowy");
    }
    
	public function testNextPrev() {
		$current = $this->_week->current();

		$this->_week->next();
		$next = $this->_week->current();
		
		$this->_week->preview();
		$prev = $this->_week->current();
		
		$this->assertEqual($current, $prev, "Dni nie są identyczne");
		$this->assertNotEqual($current, $next, "Dni nie powinny być identyczne");
    }
    
    public function testRewind() {
    	$this->_week->rewind();
    	$number = $this->_week->key();
    	$this->assertEqual($number, 1, "Numer dnia nie jest prawidłowy");
    }
}

$r = new KontorX_Calendar_WeekTest();
$r->run(new TextReporter());