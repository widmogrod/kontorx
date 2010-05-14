<?php
if (!defined('SETUP_TEST')) 
{
	require_once '../../setupTest.php';
}

require_once 'Zend/Session.php';
Zend_Session::start();
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

	public function testCitySetGetValue()
	{
		$value = "123123123";
		$this->_platnosci->setCity($value);
		$result = $this->_platnosci->getCity();
		$this->assertIdentical($value, $result, 'miasta nie są identyczne');
	}
	
	public function testCityExceptionToSmall()
	{
		$value = "";
		try {
			$this->_platnosci->setCity($value);
			$this->fail("Expect exception");
		} catch(KontorX_Payments_Exception $e) {
			$this->pass();
		}
	}
	
	public function testCityExceptionToLong()
	{
		$value  = "12312312312m,3n12,3mn12,m3n12,m3n12,m3n12,m3n";
		$value .= "12312312312m,3n12,3mn12,m3n12,m3n12,m3n12,m3n";
		$value .= "12312312312m,3n12,3mn12,m3n12,m3n12,m3n12,m3n";
		$value .= "12312312312m,3n12,3mn12,m3n12,m3n12,m3n12,m3n";
		$value .= "12312312312m,3n12,3mn12,m3n12,m3n12,m3n12,m3n";
		$value .= "12312312312m,3n12,3mn12,m3n12,m3n12,m3n12,m3n";
		try {
			$this->_platnosci->setCity($value);
			$this->fail("Expect exception");
		} catch(KontorX_Payments_Exception $e) {
			$this->pass();
		}
	}
	
	public function testStreetSetGetValue()
	{
		$value = "123123123";
		$this->_platnosci->setStreet($value);
		$result = $this->_platnosci->getStreet();
		$this->assertIdentical($value, $result, 'miasta nie są identyczne');
	}
	
	public function testStreetExceptionToSmall()
	{
		$value = "";
		try {
			$this->_platnosci->setStreet($value);
			$this->fail("Expect exception");
		} catch(KontorX_Payments_Exception $e) {
			$this->pass();
		}
	}
	
	public function testStreetExceptionToLong()
	{
		$value  = "12312312312m,3n12,3mn12,m3n12,m3n12,m3n12,m3n";
		$value .= "12312312312m,3n12,3mn12,m3n12,m3n12,m3n12,m3n";
		$value .= "12312312312m,3n12,3mn12,m3n12,m3n12,m3n12,m3n";
		$value .= "12312312312m,3n12,3mn12,m3n12,m3n12,m3n12,m3n";
		$value .= "12312312312m,3n12,3mn12,m3n12,m3n12,m3n12,m3n";
		$value .= "12312312312m,3n12,3mn12,m3n12,m3n12,m3n12,m3n";
		try {
			$this->_platnosci->setStreet($value);
			$this->fail("Expect exception");
		} catch(KontorX_Payments_Exception $e) {
			$this->pass();
		}
	}
	
	public function testPostCodeSetGetValue()
	{
		$value = "123123123";
		$this->_platnosci->setPostCode($value);
		$result = $this->_platnosci->getPostCode();
		$this->assertIdentical($value, $result, 'miasta nie są identyczne');
	}
	
	public function testPostCodeExceptionToSmall()
	{
		$value = "";
		try {
			$this->_platnosci->setPostCode($value);
			$this->fail("Expect exception");
		} catch(KontorX_Payments_Exception $e) {
			$this->pass();
		}
	}
	
	public function testPostCodeExceptionToLong()
	{
		$value  = "12312312312m,3n12,3mn12,m3n12,m3n12,m3n12,m3n";
		$value .= "12312312312m,3n12,3mn12,m3n12,m3n12,m3n12,m3n";
		$value .= "12312312312m,3n12,3mn12,m3n12,m3n12,m3n12,m3n";
		$value .= "12312312312m,3n12,3mn12,m3n12,m3n12,m3n12,m3n";
		$value .= "12312312312m,3n12,3mn12,m3n12,m3n12,m3n12,m3n";
		$value .= "12312312312m,3n12,3mn12,m3n12,m3n12,m3n12,m3n";
		try {
			$this->_platnosci->setPostCode($value);
			$this->fail("Expect exception");
		} catch(KontorX_Payments_Exception $e) {
			$this->pass();
		}
	}
	
	public function testLastNameSetGetValue()
	{
		$value = "123123123";
		$this->_platnosci->setLastName($value);
		$result = $this->_platnosci->getLastName();
		$this->assertIdentical($value, $result, 'miasta nie są identyczne');
	}
	
	public function testLastNameExceptionToSmall()
	{
		$value = "";
		try {
			$this->_platnosci->setLastName($value);
			$this->fail("Expect exception");
		} catch(KontorX_Payments_Exception $e) {
			$this->pass();
		}
	}
	
	public function testLastNameExceptionToLong()
	{
		$value  = "12312312312m,3n12,3mn12,m3n12,m3n12,m3n12,m3n";
		$value .= "12312312312m,3n12,3mn12,m3n12,m3n12,m3n12,m3n";
		$value .= "12312312312m,3n12,3mn12,m3n12,m3n12,m3n12,m3n";
		$value .= "12312312312m,3n12,3mn12,m3n12,m3n12,m3n12,m3n";
		$value .= "12312312312m,3n12,3mn12,m3n12,m3n12,m3n12,m3n";
		$value .= "12312312312m,3n12,3mn12,m3n12,m3n12,m3n12,m3n";
		try {
			$this->_platnosci->setLastName($value);
			$this->fail("Expect exception");
		} catch(KontorX_Payments_Exception $e) {
			$this->pass();
		}
	}

	public function testFirstNameSetGetValue()
	{
		$value = "123123123";
		$this->_platnosci->setFirstName($value);
		$result = $this->_platnosci->getFirstName();
		$this->assertIdentical($value, $result, 'miasta nie są identyczne');
	}
	
	public function testFirstNameExceptionToSmall()
	{
		$value = "";
		try {
			$this->_platnosci->setFirstName($value);
			$this->fail("Expect exception");
		} catch(KontorX_Payments_Exception $e) {
			$this->pass();
		}
	}
	
	public function testFirstNameExceptionToLong()
	{
		$value  = "12312312312m,3n12,3mn12,m3n12,m3n12,m3n12,m3n";
		$value .= "12312312312m,3n12,3mn12,m3n12,m3n12,m3n12,m3n";
		$value .= "12312312312m,3n12,3mn12,m3n12,m3n12,m3n12,m3n";
		$value .= "12312312312m,3n12,3mn12,m3n12,m3n12,m3n12,m3n";
		$value .= "12312312312m,3n12,3mn12,m3n12,m3n12,m3n12,m3n";
		$value .= "12312312312m,3n12,3mn12,m3n12,m3n12,m3n12,m3n";
		try {
			$this->_platnosci->setFirstName($value);
			$this->fail("Expect exception");
		} catch(KontorX_Payments_Exception $e) {
			$this->pass();
		}
	}
	
	
	public function testDescSetGetValue()
	{
		$value = "123123123";
		$this->_platnosci->setDesc($value);
		$result = $this->_platnosci->getDesc();
		$this->assertIdentical($value, $result, 'miasta nie są identyczne');
	}
	
	public function testDescExceptionToSmall()
	{
		$value = "";
		try {
			$this->_platnosci->setDesc($value);
			$this->fail("Expect exception");
		} catch(KontorX_Payments_Exception $e) {
			$this->pass();
		}
	}
	
	public function testDescExceptionToLong()
	{
		$value  = "12312312312m,3n12,3mn12,m3n12,m3n12,m3n12,m3n";
		$value .= "12312312312m,3n12,3mn12,m3n12,m3n12,m3n12,m3n";
		$value .= "12312312312m,3n12,3mn12,m3n12,m3n12,m3n12,m3n";
		$value .= "12312312312m,3n12,3mn12,m3n12,m3n12,m3n12,m3n";
		$value .= "12312312312m,3n12,3mn12,m3n12,m3n12,m3n12,m3n";
		$value .= "12312312312m,3n12,3mn12,m3n12,m3n12,m3n12,m3n";
		try {
			$this->_platnosci->setDesc($value);
			$this->fail("Expect exception");
		} catch(KontorX_Payments_Exception $e) {
			$this->pass();
		}
	}
	
	public function testOrderIdSetGetValue()
	{
		$value = "123123123";
		$this->_platnosci->setOrderId($value);
		$result = $this->_platnosci->getOrderId();
		$this->assertIdentical($value, $result, 'miasta nie są identyczne');
	}
	
	public function testOrderIdExceptionToSmall()
	{
		$value = "";
		try {
			$this->_platnosci->setOrderId($value);
			$this->fail("Expect exception");
		} catch(KontorX_Payments_Exception $e) {
			$this->pass();
		}
	}
	
	public function testOrderIdExceptionToLong()
	{
		$value  = "";
		for ($i = 0; $i < 1026; $i++)
		{
			$value .= $i;
		}
		
		try {
			$this->_platnosci->setOrderId($value);
			$this->fail("Expect exception");
		} catch(KontorX_Payments_Exception $e) {
			$this->pass();
		}
	}

	/*public function testSID_OdPlatnosci()
	{
		$posId 		= '1';
		$sessionId 	= '417419';
		$orderId 	= '';
		$status		= '';
		$amount		= '';
		$desc 		= '';
		$ts 		= '1094205761232';
		$ts			= '';
		$key1 		= '';
		$key2		= '';
		
		$sig1		= 'b6d68525f724a6d69fb1260874924759';
		
		// sig = md5(pos id + session id + order id + status + amount + desc + ts + key2)
//		$sig2 = md5($posId + $sessionId + $ts + $key1);
		$this->assertIdentical($sig1, $sig2, 'SID nie jest identyczny');
	}*/

	/*public function testGetConnection()
	{
		$this->_platnosci->setPosId('20575');
		$this->_platnosci->setPosAuthKey('eNjTjoA');
		$this->_platnosci->setKey1('d11ef8a28ae801e9d1889f9f662d1d42');
		$this->_platnosci->setKey2('f7363e25d17ac6ac21262a90dbdcd4a4');

		$url = $this->_platnosci->getUrlDlaProcedury(KontorX_Payments_Platnosci::ACTION_GET);

		$time = time();
		$sig = md5($this->_platnosci->getPosId() . $this->_platnosci->getSessionId() . $time . $this->_platnosci->getKey1());
		
		$rCurl = curl_init();

		curl_setopt($rCurl, CURLOPT_HEADER, 0);
		curl_setopt($rCurl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($rCurl, CURLOPT_VERBOSE, 1);
		curl_setopt($rCurl, CURLOPT_REFERER, 'kwiatorchidei.pl');
		curl_setopt($rCurl, CURLOPT_URL, $url);

		$sData = curl_exec($rCurl);

		curl_close($rCurl);
	}*/
}

$test = new KontorX_Platnosci_PlatnosciTest();
$test->run(new TextReporter());