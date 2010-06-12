<?php
interface KontorX_Ftp_Adapter_Interface
{
	/**
	 * @param string $server
	 */
	public function setServer($server);

	/**
	 * @param string $username
	 * @param string $password
	 */
	public function setLogin($username, $password);
	
	/**
	 * @return bool
	 */
	public function isConnected();
	
	/**
	 * @return void 
	 */
	public function connect();

	/**
	 * @return void 
	 */
	public function close();
	
	/**
	 * List files in givendirecotry
	 * @return array 
	 */
	public function ls($directory);
	
	/**
	 * Download a file from server
	 * @param string $localFile
	 * @param string $remoteFile
	 * @param mixed $model
	 * @return bool
	 */
	public function get($localFile, $remoteFile, $model = null);
	
	/**
	 * Upload file to the server
	 * @param string $remoteFile
	 * @param string $localFile
	 * @param mixed $model
	 * @return bool
	 */
	public function put($remoteFile, $localFile, $model = null);

	/**
	 * Delete file on the server
	 * @param string $path
	 * @return bool
	 */
	public function delete($path);
}