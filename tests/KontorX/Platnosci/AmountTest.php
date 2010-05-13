<?php
if (!defined('SETUP_TEST')) 
{
	require_once '../../setupTest.php';
}

class KontorX_Platnosci_AmountTest extends UnitTestCase 
{

	public function testIsFloat() 
	{
		$value = 123.12;
		$this->assertTrue(is_float($value), "wartość nie jest typu float"); 
	}
	
	public function testIsFloat2() 
	{
		$value = '123.12';
		$this->assertFalse(is_float($value), "wartość nie jest typu float"); 
	}
	
	public function testIsInteger() 
	{
		$value = '123.12';
		$this->assertFalse(is_integer($value), "wartość nie jest typu float"); 
	}
	
	public function testStringToFloat() 
	{
		$float = 123.12; 
		$value = (float) '123.12';
		$this->assertIdentical($float, $value, "wartość nie jest identyczna"); 
	}

	/**
	 * Test ma znaczenie jeżeli @see testStringToFloat() jest pozytywne
	 */
	public function testIsFloat3()
	{
		$value = (float) '123,12';
		$this->assertTrue(is_float($value), "wartość nie jest typu float"); 
	}
	
	public function testStringToFloat2() 
	{
		$float = 123.12; 
		$value = (float) '123,12'; // 123
		$this->assertNotIdentical($float, $value, "wartość nie jest identyczna"); 
	}

	public function testStringIsNumeric() 
	{
		$value = '123,12';
		$this->assertFalse(is_numeric($value), "nie jest numeryczna"); 
	}

	/**
	 * Test ma znaczenie jeżeli @see testStringToFloat() jest pozytywne
	 */
	public function testIsFloat4()
	{
		$value = (float) '123,12';
		$this->assertTrue(is_float($value), "wartość nie jest typu float"); 
	}

	public function testFloatIsInteger() 
	{
		$value = 123.12;
		$this->assertFalse(is_integer($value), "wartość nie jest typu jest integer a powinien być float"); 
	}
	
	public function testIntegerIsFloat() 
	{
		$value = 123;
		$this->assertFalse(is_float($value), "wartość nie jest typu jest float a powinien być integer"); 
	}
	
	public function testIsFloatOrIsIntegerFalse() 
	{
		$value = '123.12';
		$int = intval($value);
		$flo = floatval($value);

		$this->assertNotEqual($int, $flo, "wartości sa identyczne a powinny być różne"); 
	}

	public function testIsFloatOrIsIntegerTrue() 
	{
		$value = '123';
		$int = intval($value);
		$flo = floatval($value);

		$this->assertEqual($int, $flo, "wartości sa identyczne a powinny być różne"); 
	}
}

$test = new KontorX_Platnosci_AmountTest();
$test->run(new TextReporter());