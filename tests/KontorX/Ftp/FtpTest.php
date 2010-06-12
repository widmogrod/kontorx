<?php
if (!defined('SETUP_TEST')) {
	require_once '../../setupTest.php';
}

require_once 'KontorX/Ftp.php';

class KontorX_Ftp_FtpTest extends UnitTestCase 
{
	/**
	 * Please set Your personal options
	 * 
	 * @var array
	 */
	protected $_options = array(
		'server'   => 'ftp.example.com',
		'username' => 'your_usernmae',
		'password' => 'yout_password'
	);
	
	public function setUp() 
	{
	}
	
	public function tearDown() 
	{
	}
	
	public function testGetProtocol()
	{
		$url = 'ftp://ftp.widmogrod.info';
		$result = parse_url($url, PHP_URL_SCHEME);
		$this->assertEqual('ftp', $result, 'Oczekiwany URL Sheme jest inny niż FTP');
	}
	
	public function testCreateFactory()
	{
		$ftp = KontorX_Ftp::factory('ftp');
		$this->assertIsA($ftp, 'KontorX_Ftp', 'Utworzony obiekt nie jest typu "KontorX_Ftp"');
	}
	
	public function testCheckAdapterTypeFtp()
	{
		$ftp = KontorX_Ftp::factory('ftp');
		$adapter = $ftp->getAdapter();
		$this->assertIsA($adapter, 'KontorX_Ftp_Adapter_Ftp', 'Adapter nie jest typu "KontorX_Ftp_Adapter_Ftp"');
	}
	
	public function testConnectFailure()
	{
		$ftp = KontorX_Ftp::factory('ftp', array(
			'server' => 'ftp.noexists.info',
			'username' => 'usernamame',
			'password' => 'password'
		));
		
		try {
			$ftp->getAdapter()->connect();
			$this->fail("Połączenie do serwera nie może zostać nawiązane");
		} catch(Exception $e) {
			$this->assertIsA($e, 'KontorX_Ftp_Adapter_Exception', "Wyjątej nie jest instancją 'KontorX_Ftp_Adapter_Exception'!");
		}
	}
	
	public function testConnectSuccessLoginFailure()
	{
		$ftp = KontorX_Ftp::factory('ftp', array(
			'server' => 'ftp.widmogrod.info',
			'username' => 'non_user',
			'password' => 'non_password'
		));
		
		try {
			$ftp->getAdapter()->connect();
			$this->fail("Połączenie do serwera nie może zostać nawiązane");
		} catch(Exception $e) {
			$this->assertIsA($e, 'KontorX_Ftp_Adapter_Exception', "Wyjątej nie jest instancją 'KontorX_Ftp_Adapter_Exception'!");
		}
	}
	
	public function testConnectSuccessLoginSuccess()
	{
		$ftp = KontorX_Ftp::factory('ftp', $this->_options);
		
		try {
			$ftp->getAdapter()->connect();
		} catch(Exception $e) {
			$this->fail("Połączenie do serwera nie zostało nawiązane:", $e->getMessage());
		}
	}
	
	public function testPutFile()
	{
		$ftp = KontorX_Ftp::factory('ftp', $this->_options);

		$remoteFile = 'httpdocs/remoteTestPutFile.txt';
		$localFile = 'testPutFile.txt';

		$result = $ftp->put($remoteFile, $localFile);
		$this->assertTrue($result, 'Wystąpił błąd w trakcie wysyłania pliku na serwer');
	}
	
	public function testGetFile()
	{
		$ftp = KontorX_Ftp::factory('ftp', $this->_options);

		$localFile = 'testGetFile.txt';
		$remoteFile = 'httpdocs/remoteTestPutFile.txt';

		$result = $ftp->get($localFile, $remoteFile);
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
		$ftp = KontorX_Ftp::factory('ftp', $this->_options);

		$path = 'httpdocs/remoteTestPutFile.txt';
		$result = $ftp->delete($path);
		$this->assertTrue($result, 'Wystąpił błąd w trakcie usuwania pliku na serwera');
	}
}

$r = new KontorX_Ftp_FtpTest();
$r->run(new TextReporter());