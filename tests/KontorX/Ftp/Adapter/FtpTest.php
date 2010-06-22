<?php
if (!defined('SETUP_TEST')) {
	require_once '../../../setupTest.php';
}

require_once 'KontorX/Ftp.php';

class KontorX_Ftp_Adapter_FtpTest extends UnitTestCase
{
	/**
	 * Please set Your personal options
	 * 
	 * @var array
	 */
	protected $_options = array(
		'server'   => 'ftp.example.com',
		'username' => 'root',
		'password' => '********'
	);

	/**
	 * @var KontorX_Ftp_Adapter_Ftp
	 */
	protected $_adapter;
	
	public function setUp()
	{
		/**
		 * Please set Your personal options !
		 */
		$ftp = KontorX_Ftp::factory('ftp',$this->_options);
		
		$this->_adapter = $ftp->getAdapter();
	}
	
	public function tearDown()
	{
		$this->_adapter->close();
	}
	
	public function testConnection()
	{
		try {
			$this->_adapter->connect();
		} catch(Exception $e) {
			$this->fail("Połączenie do serwera nie zostało nawiązane:", $e->getMessage());
		}
	}

	public function testListDir()
	{
		$result = $this->_adapter->ls('.');
		$this->assertTrue(is_array($result), "Oczekiwana jest tablica z listą katalogów");
	}
	
	public function testListDirWithMoreInfo()
	{
		$result = $this->_adapter->ls('.', true);
		$this->assertTrue(is_array($result), "Oczekiwana jest tablica z listą katalogów");
		$this->dump($result);
	}
	
	public function testPutFile()
	{
		$remoteFile = 'httpdocs/remoteTestPutFile.txt';
		$localFile = 'testPutFile.txt';

		$result = $this->_adapter->put($remoteFile, $localFile);
		$this->assertTrue($result, 'Wystąpił błąd w trakcie wysyłania pliku na serwer');
	}
	
	public function testGetFile()
	{
		$localFile = 'testGetFile.txt';
		$remoteFile = 'httpdocs/remoteTestPutFile.txt';

		$result = $this->_adapter->get($localFile, $remoteFile);
		$this->assertTrue($result, 'Wystąpił błąd w trakcie pobierania pliku z serwera');
	}
	
	public function testEqualPutAndGetFile()
	{
		$putLocalFile = 'testPutFile.txt';
		$getLocalFile = 'testGetFile.txt';
		$result = (file_get_contents($putLocalFile) == file_get_contents($getLocalFile));
		$this->assertTrue($result, 'Plik przesłany na serwer i ponowie pobrany z serwera są sobie różne!');
		
		unlink($getLocalFile);
	}
	
	public function testDeleteFile()
	{
		$path = 'httpdocs/remoteTestPutFile.txt';
		$result = $this->_adapter->delete($path);
		$this->assertTrue($result, 'Wystąpił błąd w trakcie usuwania pliku na serwera');
	}

}

$r = new KontorX_Ftp_Adapter_FtpTest();
$r->run(new TextReporter());
