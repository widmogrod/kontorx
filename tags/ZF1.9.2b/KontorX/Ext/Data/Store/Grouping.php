<?php
require_once 'KontorX/Ext/Data/Store.php';

/**
 * @author gabriel
 *
 */
class KontorX_Ext_Data_Store_Grouping extends KontorX_Ext_Data_Store {

	public function toJavaScript() {
		$options = array(
			'reader' =>$this->getReader(),
			'groupField' => $this->_groupField,
			'sortInfo' => $this->_sortInfo,
			'remoteGroup' => true,
		
		);

		if (null !== $this->_proxy) {
			$options['proxy'] = $this->_proxy;
		} else
		if (null !== $this->_url) {
			$options['url'] = $this->_url;
		}

		$options = $this->_toJavaScript($options);

		return sprintf('new Ext.data.GroupingStore(%s);', $options);
	}

	/**
	 * @var string
	 */
	protected $_groupField;
	
	/**
	 * @param string $filed
	 * @return KontorX_Ext_Data_Store_Grouping
	 */
	public function setGroupField($filed) {
		$this->_groupField = (string) $filed;
		return $this;
	}
	
	
	
	/**
	 * @var string
	 */
	protected $_sortInfo = array();
	
	/**
	 * @param array $sortInfo
	 * @return KontorX_Ext_Data_Store_Grouping
	 */
	public function setSortInfo(array $sortInfo) {
		$this->_sortInfo = $sortInfo;
		return $this;
	}
}