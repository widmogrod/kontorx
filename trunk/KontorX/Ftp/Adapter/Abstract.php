<?php
require_once 'KontorX/Ftp/Adapter/Interface.php';

abstract class KontorX_Ftp_Adapter_Abstract implements KontorX_Ftp_Adapter_Interface
{	
	/**
	 * @var resource
	 */
	protected $_connection;

	/**
	 * @var string
	 */
	protected $_server;
	
	/**
	 * @param string $server
	 */
	public function setServer($server)
	{
		$this->_server = (string) $server;
	}

	/**
	 * @return string 
	 */
	public function getServer()
	{
		return $this->_server;
	}
	
	/**
	 * @param string $username
	 * @param string $password
	 */
	public function setLogin($username, $password)
	{
		$this->setUsername($username);
		$this->setPassword($password);
	}
	
	/**
	 * @var string
	 */
	protected $_username;
	
	/**
	 * @param string $password
	 */
	public function setUsername($username)
	{
		$this->_username = (string) $username;
	}
	
	/**
	 * @return string 
	 */
	public function getUsername()
	{
		return $this->_username;
	}
	
	/**
	 * @var string
	 */
	protected $_password;
	
	/**
	 * @param string $password
	 */
	public function setPassword($password)
	{
		$this->_password = (string) $password;
	}
	
	/**
	 * @return string 
	 */
	public function getPassword()
	{
		return $this->_password;
	}
	
	/**
	 * @return bool
	 */
	public function isConnected()
	{
		return (null !== $this->_connection);
	}

	/**
	 * 
	 */
	public function __destruct()
	{
		$this->close();
	}

	/**
	 * @param string $rawOutput
	 * @result array
	 */
	protected function _parseRawList($rawOutput)
	{
		// to nie jest najlepsze rozwiązanie ale zawsze ogranicza pole błędu
		if (preg_match('#(\d{4}-\d{2}-\d{2})#', $rawOutput, $matched)) {
			// drwxr-xr-x 3 gabriel gabriel 4096 2010-06-22 00:29 Adapter
			$info = sscanf($rawOutput, "%s %d %s %s %d %s %s %s");
			list($permissions, $files, $user, $group, $filesize, $date, $hour, $filename) = $info;
		} else {
			// drwxr-xr-x 3 gabriel gabriel 4096 Jan 1 00:29 Adapter
			$info = sscanf($rawOutput, "%s %d %s %s %d %s %d %s %s");
			list($permissions, $files, $user, $group, $filesize, $month, $day, $year, $filename) = $info;
			$date = $month . ' ' . $day . ' ' . $year;
			$hour = '';
		}

		$result = array(
			'filename' => $filename,
			'filetype' => ((substr($permissions,0,1) == 'd') ? 'DIR' : 'FILE'),
			'files' => $files,
			'filesize' => $filesize,
			'permissions' => $permissions,
			'user'  => $user,
			'group' => $group,
			'filemtime' => strtotime("$date, $hour")
		);
		
		return $result;
	}
}
