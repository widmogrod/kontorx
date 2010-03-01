<?php
require_once 'KontorX/Ext/Abstract.php';

/**
 * @author gabriel
 * 
 */
class KontorX_Ext_PagingToolbar extends KontorX_Ext_Abstract {

	public function toJavaScript($renderToId = null) {
		$options = array(
			'store' => $this->_store,
			'displayInfo' => $this->_displayInfo,
			'pageSize' => $this->_pageSize,
			'prependButtons' => $this->_prependButtons
		);

		if (null !== $this->_plugins)
			$options['plugins'] = $this->_plugins;

		$options = $this->_toJavaScript($options);

		return sprintf('new Ext.PagingToolbar(%s);', $options);
	}
	
	/**
	 * @var KontorX_Ext_Data_Store_Interface|string
	 */
	protected $_store;

	/**
	 * @param KontorX_Ext_Data_Store_Interface|KontorX_JavaScript_Interface|string $store
	 * @return KontorX_Ext_PagingToolbar
	 */
	public function setStore($store) {
		if ($store instanceof KontorX_Ext_Data_Store_Interface
			|| $store instanceof KontorX_JavaScript_Interface 
			|| is_string($store)) {
			$this->_store = $store;
		} else {
			require_once 'KontorX/Exception.php';
			throw new KontorX_Exception('store is not string or instanceof "KontorX_Ext_Data_Store_Interface"');
		}
		return $this;
	}

	/**
	 * @var KontorX_Ext_Data_Store_Interface
	 */
	protected $_plugins;

	/**
	 * @param KontorX_JavaScript_Interface $store
	 * @return KontorX_Ext_PagingToolbar
	 */
	public function setPlugins(KontorX_JavaScript_Interface $plugins) {
		if ($plugins instanceof KontorX_JavaScript_Interface) {
			$this->_plugins = $plugins;
		} else {
			require_once 'KontorX/Exception.php';
			throw new KontorX_Exception('plugins is not string or instanceof "KontorX_JavaScript_Interface"');
		}
		return $this;
	}

	/**
	 * @var bool
	 */
	protected $_displayInfo = true;
	
	/**
	 * @param bool $flag
	 * @return KontorX_Ext_PagingToolbar
	 */
	public function setDisplayInfo($flag = true) {
		$this->_displayInfo = (bool) $flag;
		return $this;
	}
	
	/**
	 * @var integer
	 */
	protected $_pageSize = 30;

	/**
	 * @param integer $pageSize
	 * @return KontorX_Ext_PagingToolbar
	 */
	public function setPageSize($pageSize) {
		$this->_pageSize = (int) $pageSize;
		return $this;
	}

	/**
	 * @var bool
	 */
	protected $_prependButtons = true;

	/**
	 * @param bool $flag
	 * @return KontorX_Ext_PagingToolbar
	 */
	public function setPrependButtons($flag = true) {
		$this->_prependButtons = (bool) $flag;
		return $this;
	}
}