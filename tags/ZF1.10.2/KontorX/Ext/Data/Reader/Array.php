<?php
require_once 'KontorX/Ext/Data/Reader/Json.php';

/**
 * @author gabriel
 *
 */
class KontorX_Ext_Data_Reader_Array
	extends KontorX_Ext_Data_Reader_Json {

	public function toJavaScript() {
		$options = array(
			'idProperty' => $this->getId(),     
    		'fields' => $this->getFields()
		);

		if (null !== $this->_data)
			$options['data'] = $this->_data;

		$options = $this->_toJavaScript($options);

		return sprintf('new Ext.data.ArrayReader(%s);', $options);
	}
	
	/**
	 * @var KontorX_JavaScript_Interface
	 */
	protected $_data;
	
	/**
	 * @param KontorX_JavaScript_Interface $data
	 * @return KontorX_Ext_Data_Reader_Array
	 */
	public function setData(KontorX_JavaScript_Interface $data) {
		$this->_data = $data;
		return $this;
	}
}