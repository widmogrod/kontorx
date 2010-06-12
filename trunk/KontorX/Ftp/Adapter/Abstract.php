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
}