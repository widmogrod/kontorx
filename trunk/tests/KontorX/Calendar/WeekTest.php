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
    
	public function testHasDayTrue() {
    	$this->_week->rewind();
    	$day = $this->_week->current();
    	$this->assertTrue($this->_week->hasDay($day), "Dzień powinien należeć do tygodnia");
    }
    
	public function testHasDayFalse() {
    	$this->_week->rewind();
    	// wyjście poza zasieng tygodnia
    	$this->_week->preview();
    	$day = $this->_week->current();
    	$this->assertFalse($this->_week->hasDay($day), "Dzień NIE powinien należeć do tygodnia");
    }
    
    public function testValidRange1() {
    	$this->_week->rewind();
    	// wyjście poza zasieng tygodnia
    	$this->_week->preview();
    	$this->assertFalse($this->_week->valid(), "Zasięg tygodnia nie można przekroczyć");
    }
    
	public function testValidRange2() {
    	$this->_week->rewind();
    	$this->_week->next();
    	$this->_week->next();
    	$this->_week->next();
    	$this->_week->next();
    	$this->_week->next();
    	$this->_week->next();
    	$this->_week->next();
    	$this->_week->next();
    	$this->_week->next();
    	$this->_week->next();
    	// wyjście poza zasieng tygodnia
    	$this->assertFalse($this->_week->valid(), "Zasięg tygodnia nie można przekroczyć");
    }
    
	public function testValidRange3() {
    	$this->_week->rewind();
    	// wyjście poza zasieng tygodnia
    	$this->assertTrue($this->_week->valid(), "Zasięg tygodnia nie powinien zostać przekroczony");
    }
}

$r = new KontorX_Calendar_WeekTest();
$r->run(new TextReporter());