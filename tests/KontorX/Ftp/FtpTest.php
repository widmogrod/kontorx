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
		'server'   => 'widmogrod.info',
		'username' => 'widmogrod',
		'password' => 'for6ba!'
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
	
    public function testRenameAFile()
    {
        $ftp = KontorX_Ftp::factory('ftp', $this->_options);

        $currentFilename = 'httpdocs/remoteTestPutFile.txt';
        $newFilename = $currentFilename.'.bak';
        
        $result = $ftp->rename($currentFilename, $newFilename);
        $this->assertTrue($result, 'Wystąpił błąd w trakcie zmiany nazwy pliku na serwera');
    }
    
    public function testRenameBFile()
    {
        $ftp = KontorX_Ftp::factory('ftp', $this->_options);

        $currentFilename = 'httpdocs/remoteTestPutFile.txt.bak';
        $newFilename = 'httpdocs/remoteTestPutFile.txt';

        $result = $ftp->rename($currentFilename, $newFilename);
        $this->assertTrue($result, 'Wystąpił błąd w trakcie zmiany nazwy pliku na serwera');
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
	
	public function testListRawParse()
	{
		$rawList = array(
			"drwxr-xr-x 3 gabriel gabriel 4096 2010-06-12 16:22 Adapter",
			"-rw-r--r-- 1 gabriel gabriel 4338 2010-06-21 22:11 FtpTest.php",
			"-rw-r--r-- 1 gabriel gabriel   11 2010-06-12 16:22 testPutFile.txt",
			"drwxr-x--- 5 widmogrod 504   4096 Oct        1     2009 anon_ftp"
		);

		$result = array();

		foreach($rawList as $list) {
			// to nie jest najlepsze rozwiązanie ale zawsze ogranicza pole błędu
			if (preg_match('#(\d{4}-\d{2}-\d{2})#', $list, $matched)) {
				$info = sscanf($list, "%s %d %s %s %d %s %s %s");
				list($permisions, $size, $user, $group, $filesize, $date, $hour, $filename) = $info;
			} else {
				$info = sscanf($list, "%s %d %s %s %d %s %d %s %s");
				list($permisions, $size, $user, $group, $filesize, $month, $day, $year, $filename) = $info;
				$date = $month . ' ' . $day . ' ' . $year;
				$hour = '';
			}

			$result[] = array(
				'type' => ((substr($permisions,0,1) == 'd') ? 'DIR' : 'FILE'),
				'filesize' => $filesize,
				'permisions' => $permisions,
				'user' => $user,
				'group' => $group,
				'time' => strtotime("$date, $hour")
			);
		}

		var_dump($result);

		$success = array(
		  array (
			"type"=> "DIR",
			"filesize"=> 4096,
			"permisions"=> "drwxr-xr-x",
			"user"=> "gabriel",
			"group"=>"gabriel",    
			"time"=>1276352520
		  ),                   
		  array (
			"type"=> "FILE",
			"filesize"=> 4338,
			"permisions"=> "-rw-r--r--",
			"user"=> "gabriel",
			"group"=>"gabriel",    
			"time"=>1277151060
		  ),
		  array (
			"type"=> "FILE",
			"filesize"=> 11,
			"permisions"=> "-rw-r--r--",
			"user"=> "gabriel",
			"group"=>"gabriel",    
			"time"=>1276352520
		  ),
		  array (
			"type"=> "DIR",
			"filesize"=> 4096,
			"permisions"=> "drwxr-x---",
			"user"=> "widmogrod",
			"group"=>"504",    
			"time"=>1254348000
		  )
		);

		$this->assertIdentical($result, $success, 'Wartości sparsowane "rawList" nie są identyczne!');
	}
}

$r = new KontorX_Ftp_FtpTest();
$r->run(new TextReporter());
