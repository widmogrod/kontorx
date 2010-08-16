<?php
/**
 * KontorX_Pdf
 * 
 * Abstrakcja pozwalająca na ujednolicenie sposobu generowania 
 * dokumentu PDF na podstawie przekazanego pliku|składni HTML
 * 
 * @author $Author$
 * @version $Id$
 */
class KontorX_Pdf
{
	/**
	 * Factory method
	 * 
	 * TODO: Dodać mozliwość sprawdzania automatycznego tj.
	 * sprawdzenie jaki adapter jest możliwy do wykorzystania
	 * 
	 * @param KontorX_Pdf_Adapter_Abstract|string $adapter
	 * @param array $options
	 * @throws KontorX_Pdf_Exception
	 * @return KontorX_Pdf
	 */
	public static function factory($adapter, array $options = null)
	{
		$instance = new self();
		
		if (is_string($adapter)) {
			$adapter = ucfirst($adapter);
			$class = $instance->getPluginLoader()->load($adapter);
			$adapter = new $class();
		}

		if (!$adapter instanceof KontorX_Pdf_Adapter_Abstract) {
			$message = 'Adapter is not instanceof "KontorX_Pdf_Adapter_Abstract"';
			require_once 'KontorX/Pdf/Exception.php';
			throw new KontorX_Pdf_Exception($message);
		}
		
		$instance->setAdapter($adapter);

		if (is_array($options))
			$instance->setOptions($options);
		
		return $instance;
	}

	/**
	 */
	protected function __construct()
	{}
	
	/**
	 * Konfiguruj główną klasę jak i adapter
	 * @param array $options
	 */
	public function setOptions(array $options)
	{
		$adapter = $this->getAdapter();
		foreach ($options as $key => $value) 
		{
			$methodName = 'set' . ucfirst($key);
			if (method_exists($this, $methodName)) {
				$this->$methodName($value);
			} elseif (method_exists($adapter, $methodName)) {
				$adapter->$methodName($value);
			}
		}
	}
	
	/**
	 * @var KontorX_Pdf_Adapter_Abstract
	 */
	protected $_adapter;
	
	/**
	 * @param KontorX_Pdf_Adapter_Abstract $adapter
	 */
	public function setAdapter(KontorX_Pdf_Adapter_Abstract $adapter)
	{
		$this->_adapter = $adapter;
	}
	
	/**
	 * @return KontorX_Pdf_Adapter_Abstract
	 */
	public function getAdapter()
	{
		return $this->_adapter;
	}
	
	/**
     * @var Zend_Loader_PluginLoader
     */
    private $_pluginLoader;

    /**
     * Set @see Zend_Loader_PluginLoader
     * @param Zend_Loader_PluginLoader $loader
     */
    public function setPluginLoader(Zend_Loader_PluginLoader $loader) 
    {
        $this->_pluginLoader;
    }

    /**
     * Get @see Zend_Loader_PluginLoader
     * @return Zend_Loader_PluginLoader
     */
    public function getPluginLoader() 
    {
        if (!isset($this->_pluginLoader)) {
            require_once 'Zend/Loader/PluginLoader.php';
            $this->_pluginLoader = new Zend_Loader_PluginLoader(array(
                "KontorX_Pdf_Adapter" => "KontorX/Pdf/Adapter"
            ));
        }

        return $this->_pluginLoader;
    }

    /**
     * @var string
     */
    protected $_fileame;
	
	/**
	 * Ustawienie nazwy jaką nazwę ma posiadać wysyłany do pobrania plik 
	 * podczas wywołania metody @see KontorX_Pdf::output()
	 * 
	 * @param string $fileame
	 */
	public function setFilename($fileame)
	{
		$this->_fileame = basename($fileame);
		// pozbawienie rozszeżenia
		$this->_fileame = str_replace(pathinfo($this->_fileame, PATHINFO_EXTENSION),'', $this->_fileame);
		$this->_fileame = trim($fileame, ' .');
		$this->_fileame .= '.pdf';
	}
	
	/**
	 * Pobranie nazwy pliku
	 * @return string
	 */
	public function getFilename()
	{
		if (null === $this->_fileame)
			$this->setFilename('Document');

		return $this->_fileame;
	}
    
    
    /**
     * @param string $html
     */
    public function setHtml($html)
    {
    	$this->getAdapter()->setHtml($html);
    }

    /**
     * Zwraca ścieżkę do dokumentu PDF, który został wyrenderowany
     * @return string
     */
    public function render() 
    {
    	$adapter = $this->getAdapter();
    	$adapter->render();
    	$filepath = $adapter->getOutputFilepath();
    	return $filepath;
    }

    /**
     * @void
     */
    public function output()
    {
    	if(headers_sent()) {
			$message = 'Headers were already sent.';
			require_once 'KontorX/Pdf/Exception.php';
			throw new KontorX_Pdf_Exception($message);
		}

		$adapter = $this->getAdapter();
    	$adapter->render();

    	$filepath = $adapter->getOutputFilepath();
		$filename = $this->getFilename();

		header('Content-Description: File Transfer');
		header('Cache-Control: public, must-revalidate, max-age=0'); // HTTP/1.1
		header('Pragma: public');
		header('Expires: Sat, 26 Jul 1997 05:00:00 GMT'); // Date in the past
		header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
		// force download dialog
		header('Content-Type: application/force-download');
		header('Content-Type: application/octet-stream', false);
		header('Content-Type: application/download', false);
		header('Content-Type: application/pdf', false);
		// use the Content-Disposition header to supply a recommended filename
		header('Content-Disposition: attachment; filename="'.basename($filename).'";');
		header('Content-Transfer-Encoding: binary');
		header('Content-Length: '.filesize($filepath));

		readfile($filepath);
    }
}