<?php
/**
 * Klasa oparta na wzorcu abstract method
 * 
 * Dobiera odpowiedni adapter w zależności od protokolu.
 * Połaczenie jest realizowane wtedy gdy jest potrzebne ("Laizy Load")
 * 
 * @author gabriel
 * @version $Id$
 */
class KontorX_Ftp
{
	const FTP = 'FTP';

	/**
	 * @return void 
	 */
	protected function __construct()
	{}

	/**
	 * @param array $options
	 */
	public function setOptions(array $options)
	{
		foreach ($options as $name => $option)
		{
			$method = 'set'.ucfirst($name);
			if (method_exists($this, $method))
			{
				$this->$method($option);
			} else
			if (method_exists($this->_adapter, $method))
			{
				$this->_adapter->$method($option);
			}
		}
	}
	
	/**
	 * @param unknown_type $adapter
	 * @param array|Zend_Config $options
	 * @return KontorX_Ftp
	 */
	public static function factory($adapter, $options = null)
	{
		$instance = new self();

		if (is_string($adapter))
		{
			// sprawdzam w prosty sposób czy nie podano url do servera 
			if (strlen(($scheme = parse_url($adapter, PHP_URL_SCHEME))) > 2)
			{
				// url do serwera został podany zatem ekstrachuje protokół
				$options['server'] = $adapter;
				$adapter = $scheme;
			}

			$adapter = $instance->getPluginLoader()->load($adapter);
			$adapter = new $adapter();
		}

		if (!$adapter instanceof KontorX_Ftp_Adapter_Abstract)
		{
			require_once 'KontorX/Ftp/Adapter/Exception.php';
			throw new KontorX_Ftp_Adapter_Exception('Adapter is not instance of "KontorX_Ftp_Adapter_Abstract"');
		}

		$instance->setAdapter($adapter);
		
		if ($options instanceof Zend_Config)
		{
			$options = $options->toArray();
		}

		if (is_array($options))
		{
			$instance->setOptions($options);
		}
		
		return $instance;
	}

	/**
	 * @var Zend_Loader_PluginLoader
	 */
	protected $_pluginLoader;
	
	/**
	 * @param Zend_Loader_PluginLoader $pluginLoader
	 */
	public function setPluginLoader(Zend_Loader_PluginLoader $pluginLoader)
	{
		$this->_pluginLoader = $pluginLoader;
	}
	
	/**
	 * @return Zend_Loader_PluginLoader
	 */
	public function getPluginLoader()
	{
		if (null === $this->_pluginLoader)
		{
			require_once 'Zend/Loader/PluginLoader.php';
			$this->_pluginLoader = new Zend_Loader_PluginLoader();
			$this->_pluginLoader->addPrefixPath('KontorX_Ftp_Adapter','KontorX/Ftp/Adapter');
		}
		return $this->_pluginLoader;
	}

	/**
	 * @var KontorX_Ftp_Adapter_Abstract
	 */
	protected $_adapter;
	
	/**
	 * @param KontorX_Ftp_Adapter_Abstract $adapter
	 */
	public function setAdapter(KontorX_Ftp_Adapter_Abstract $adapter)
	{
		$this->_adapter = $adapter;
	}
	
	/**
	 * @return KontorX_Ftp_Adapter_Abstract
	 */
	public function getAdapter()
	{
		return $this->_adapter;
	}
	
	/**
	 * @var string
	 */
	protected $_directory;
	
	/**
	 * @param string $directory
	 */
	public function setDirectory($directory)
	{
		$this->_directory = rtrim((string) $directory,'/');
	}

	/**
	 * Przytuj
	 * @param string $path
	 * @return string
	 */
	protected function _preperePath($path)
	{
		return $this->_directory . '/' . ltrim($path, '/');
	}
	
	
	/**
	 * List files in given direcotry
	 * @param string $directory
	 * @param bool $moreInfo
	 * @return array 
	 */
	public function ls($directory, $moreInfo = false)
	{
		$directory = (null === $directory)
			? $this->_directory 
			: $directory;

		return $this->_adapter->ls($directory, $moreInfo);
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
		$remoteFile = $this->_preperePath($remoteFile);
		return $this->_adapter->get($localFile, $remoteFile, $model);
	}

	/**
	 * Upload file to the server
	 * @param string $remoteFile
	 * @param string $localFile
	 * @param mixed $model
	 * @return bool
	 */
	public function put($remoteFile, $localFile, $model = FTP_BINARY)
	{
		$remoteFile = $this->_preperePath($remoteFile);
		return $this->_adapter->put($remoteFile, $localFile, $model);
	}
	
	/**
	 * Delete file on the server
	 * @param string $path
	 * @return bool
	 */
	public function delete($path)
	{
		$path = $this->_preperePath($path);
		return $this->_adapter->delete($path);
	}
}
