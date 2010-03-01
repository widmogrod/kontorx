<?php
require_once 'KontorX/Ext/Abstract.php';
require_once 'KontorX/Ext/Data/Store/Interface.php';

/**
 * @author gabriel
 *
 */
class KontorX_Ext_Data_Store
		extends KontorX_Ext_Abstract 
		implements KontorX_Ext_Data_Store_Interface {

	/**
	 * @param Zend_Config|array $options
	 * @return void
	 */
	public function __construct($options = array()) {
		if ($options instanceof Zend_Config) {
			$this->setOptions($options->toArray());
		} elseif (is_array($options)) {
			$this->setOptions($options);
		}
	}

	/**
	 * @var string
	 */
	protected $_url;
	
	/**
	 * @param string $url
	 * @return KontorX_Ext_Data_Store
	 */
	public function setUrl($url) {
		$this->_url = (string) $url;
		return $this;
	}
	
	/**
	 * @var KontorX_Ext_Data_Proxy_Interface
	 */
	protected $_proxy;
	
	/**
	 * @param KontorX_Ext_Data_Proxy_Interface $proxy
	 * @return KontorX_Ext_Data_Store
	 */
	public function setProxy(KontorX_Ext_Data_Proxy_Interface $proxy) {
		$this->_proxy = $proxy;
		return $this;
	}
	
	/**
	 * @var KontorX_Ext_Data_Reader
	 */
	protected $_reader;

	/**
	 * @param KontorX_Ext_Data_Reader_Interface $reader
	 * @return KontorX_Ext_Data_Store
	 */
	public function setReader($reader) {
		$this->_reader = $reader;
		return $this;
	}

	/**
	 * @return KontorX_Ext_Data_Reader_Interface
	 */
	public function getReader() {
		if (!$this->_reader instanceof KontorX_Ext_Data_Reader_Interface) {
    		if (is_string($this->_reader)) {
	    		if (!class_exists($this->_reader)) {
	    			require_once 'Zend/Loader.php';
	    			Zend_Loader::loadClass($this->_reader);
	    		}
	
	    		/* @var $this->_reader KontorX_Ext_Data_Reader_Interface */
	    		$this->_reader = new $this->_reader();
	    	} else {
	    		require_once 'KontorX/DataGrid/Exception.php';
	    		throw new KontorX_Exception(
	    				sprintf('Ext_Reader "%s" is not instance of "KontorX_Ext_Data_Reader_Interface"',
	    						is_object($this->_reader)
	    							? get_class($this->_reader)
	    							: (string) $this->_reader));
	    	}
    	}
		return $this->_reader;
	}
	
	public function toJavaScript() {
		$options = array(
			'reader' =>$this->getReader()
		);

		if (null !== $this->_proxy) {
			$options['proxy'] = $this->_proxy;
		} else
		if (null !== $this->_url) {
			$options['url'] = $this->_url;
		}

		$options = $this->_toJavaScript($options);

		return sprintf('new Ext.data.Store(%s);', $options);
	}
}