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
			$adapter = new $class($options);
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
	 * @param array $options
	 */
	public function setOptions(array $options)
	{
		foreach ($options as $key => $value) 
		{
			$methodName = 'set' . ucfirst($key);
			if (method_exists($this, $methodName))
				$this->$methodName($value);
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
     * @param string $html
     */
    public function setHtml($html)
    {
    	$this->getAdapter()->setHtml($html);
    }
    
    /**
     * @void
     */
    public function output()
    {
    	$this->getAdapter()->output();
    }
}