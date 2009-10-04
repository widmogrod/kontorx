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

		$options = $this->_toJavaScript($options);

		return sprintf('new Ext.data.ArrayReader(%s);', $options);
	}
}