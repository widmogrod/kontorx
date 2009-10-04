<?php
require_once 'KontorX/JavaScript/Interface.php';

interface KontorX_Ext_Data_Proxy_Interface extends KontorX_JavaScript_Interface {
		
	/**
	 * @param KontorX_JavaScript_Interface $api
	 * @return KontorX_Ext_Data_Proxy_Interface
	 */
	public function setApi(KontorX_JavaScript_Interface $api);

	/**
	 * @param string $url
	 * @return KontorX_Ext_Data_Proxy_Interface
	 */
	public function setUrl($url);
	
	/**
	 * @param string $method
	 * @return KontorX_Ext_Data_Proxy_Interface
	 */
	public function setMethod($method);
}