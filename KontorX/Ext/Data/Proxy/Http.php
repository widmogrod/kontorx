<?php
require_once 'KontorX/Ext/Abstract';
require_once 'KontorX/Ext/Data/Proxy/Interface.php';

class KontorX_Ext_Data_Proxy_Http
		extends KontorX_Ext_Abstract
		implements KontorX_Ext_Data_Proxy_Interface {

	public function __construct($url, $method = null, KontorX_JavaScript_Interface $api = null) {
		$this->setUrl($url);
		if (null !== $method) {
			$this->setMethod($method);
		}
		if (null !== $api) {
			$this->setApi($api);
		}
	}
			
	public function toJavaScript() {
		$options = array(
			'url' => $this->_url,
			'method' => $this->_method
		);

		if (null !== $this->_api) {
			$options['api'] = $this->_api;
		}

		$options = $this->_toJavaScript($options);

		return sprintf('new Ext.data.HttpProxy(%s);', $options);
	}

	/**
	 * @var KontorX_JavaScript_Interface
	 */
	protected $_api;

	public function setApi(KontorX_JavaScript_Interface $api) {
		$this->_api = $api;
		return $this;
	} 

	/**
	 * @var string
	 */
	protected $_url;
	
	public function setUrl($url) {
		$this->_url = (string) $url;
		return $this;
	}
	
	/**
	 * @var string
	 */
	protected $_method = 'GET';

	public function setMethod($method) {
		$this->_method = (string) $method;
		return $this;
	}
}