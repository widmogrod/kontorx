<?php
require_once 'KontorX/Ftp/Adapter/Abstract.php';

/**
 * @author gabriel
 * @version $Id$
 * 
 * TODO: Wziąć pod uwagę tlumienie błędów "@"!
 */
class KontorX_Ftp_Adapter_Ftp extends KontorX_Ftp_Adapter_Abstract
{
	/**
	 * @return void 
	 */
	public function connect()
	{
		if (null === $this->_connection)
		{
			$server   = $this->getServer();
			$username = $this->getUsername();
			$password = $this->getPassword();

			$this->_connection = @ftp_connect($server);
			
			if (!$this->_connection)
			{
				$this->_connection = null;
				require_once 'KontorX/Ftp/Adapter/Exception.php';
				throw new KontorX_Ftp_Adapter_Exception('Unable to connect to server "'.$server.'"');
			}

			$isLogged = @ftp_login($this->_connection, $username, $password); 
			if (!$isLogged)
			{
				$this->_connection = null;
				require_once 'KontorX/Ftp/Adapter/Exception.php';
				throw new KontorX_Ftp_Adapter_Exception('Unable to login to server "'.$server.'"');
			}
		}
	}

	/**
	 * @return void 
	 */
	public function close()
	{
		if (null !== $this->_connection)
		{
			ftp_close($this->_connection);
			$this->_connection = null;
		}
	}
	
	/**
	 * List files in given direcotry
	 * @param string $directory
	 * @param bool $moreInfo
	 * @return array 
	 */
	public function ls($directory, $moreInfo = false)
	{
		$this->connect();
		
		$result = array();
		
		if ($moreInfo) {
			$rawFileLists = (array) ftp_rawlist($this->_connection, $directory);
			foreach($rawFileLists as $rawFileList) {
				$result[] = $this->_parseRawList($rawFileList, $directory);
			}
		} else {
			$result = ftp_nlist($this->_connection, $directory);
		}

		return $result;
	}
	
	/**
	 * Download a file from server
	 * @param string $localFile
	 * @param string $remoteFile
	 * @param mixed $model
	 * @return bool
	 */
	public function get($localFile, $remoteFile, $model = null)
	{
		$this->connect();
		
		if (null === $model)
		{
			$model = FTP_BINARY;
		}
		
		return ftp_get($this->_connection, $localFile, $remoteFile, $model);
	}

	/**
	 * Upload file to the server
	 * @param string $remoteFile
	 * @param string $localFile
	 * @param mixed $model
	 * @return bool
	 */
	public function put($remoteFile, $localFile, $model = null)
	{
		$this->connect();

		if (null === $model)
		{
			$model = FTP_BINARY;
		}

		return ftp_put($this->_connection, $remoteFile, $localFile, $model);
	}
	
	/**
	 * Delete file on the server
	 * @param string $path
	 * @return bool
	 */
	public function delete($path)
	{
		$this->connect();
		return ftp_delete($this->_connection, $path);
	}
	
	/**
     * Reneme file on the server
     * @param string $currentFilename
     * @param string $newFilename
     * @return bool
     */
	public function rename($currentFile, $newFile)
	{
	    $this->connect();
        return ftp_rename($this->_connection, $currentFile, $newFile);
	}
}