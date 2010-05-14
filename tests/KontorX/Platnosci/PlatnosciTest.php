<?php
if (!defined('SETUP_TEST')) 
{
	require_once '../../setupTest.php';
}

require_once 'KontorX/Payments/Platnosci.php';

class KontorX_Platnosci_PlatnosciTest extends UnitTestCase 
{
	/**
	 * @var KontorX_Payments_Platnosci
	 */
	protected $_platnosci;

	public function setUp()
	{
		$this->_platnosci = new KontorX_Payments_Platnosci();
	}

	public function tearDown()
	{
		$this->_platnosci = null;
	}
	
	public function testAmountInteger() 
	{
		$amount = 123;
		$this->_platnosci->setAmount($amount);
		$result = $this->_platnosci->getAmount();
		
		$this->assertIdentical($amount, $result, 'wartośc zamówienia nie jest identyczna');
	}
	
	public function testAmountFloatInGrosze() 
	{
		$amount = 123.12;
		$value  = 123;
		$this->_platnosci->setAmount($amount);
		$result = $this->_platnosci->getAmount();

		$this->dump($result);
		$this->assertIdentical($value, $result, 'zamowienia nie jest w groszach');
	}
	
	public function testAmountFloatNotInGrosze() 
	{
		$amount = 123.12;
		$value  = 12312;
		$this->_platnosci->setAmount($amount, false);
		$result = $this->_platnosci->getAmount();

		$this->dump($result);
		$this->assertIdentical($value, $result, 'zamowienia nie jest w groszach');
	}
	
	public function testAmountStringFloat() 
	{
		$amount = '123.12';
		$value  = 12312;
		$this->_platnosci->setAmount($amount);
		$result = $this->_platnosci->getAmount();

		$this->assertIdentical($value, $result, 'zamowienia nie jest w groszach');
	}
	
	public function testAmountStringInteger() 
	{
		$amount = '123';
		$value  = 123;
		$this->_platnosci->setAmount($amount);
		$result = $this->_platnosci->getAmount();

		$this->assertIdentical($value, $result, 'zamowienia nie jest w groszach');
	}

	public function testGetConnection()
	{
		
	}
}

$test = new KontorX_Platnosci_PlatnosciTest();
$test->run(new TextReporter());