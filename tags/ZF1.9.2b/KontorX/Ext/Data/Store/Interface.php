<?php
require_once 'KontorX/JavaScript/Interface.php';

/**
 * @author gabriel
 *
 */
interface KontorX_Ext_Data_Store_Interface extends KontorX_JavaScript_Interface {

	/**
	 * @param string $url
	 * @return KontorX_Ext_Data_Store_Interface
	 */
	public function setUrl($url);
	
	/**
	 * @param KontorX_Ext_Data_Proxy_Interface $proxy
	 * @return KontorX_Ext_Data_Store_Interface
	 */
	public function setProxy(KontorX_Ext_Data_Proxy_Interface $proxy);
	
	/**
	 * @param KontorX_Ext_Data_Reader_Interface|string $reader
	 * @return KontorX_Ext_Data_Store_Interface
	 */
	public function setReader($reader);
	
	/**
	 * @return KontorX_Ext_Data_Reader_Interface
	 */
	public function getReader();
}