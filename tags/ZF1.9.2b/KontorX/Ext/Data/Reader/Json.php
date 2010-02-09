<?php
require_once 'KontorX/Ext/Abstract.php';
require_once 'KontorX/Ext/Data/Reader/Interface.php';

/**
 * @author gabriel
 *
 */
class KontorX_Ext_Data_Reader_Json
	extends KontorX_Ext_Abstract
	implements KontorX_Ext_Data_Reader_Interface {

	public function toJavaScript() {
		$options = array(
			'idProperty' => $this->getId(),     
			'root' => $this->getRoot(),
			'totalProperty' => $this->getTotalProperty(), 
    		'fields' => $this->getFields()
		);

		$options = $this->_toJavaScript($options);

		return sprintf('new Ext.data.JsonReader(%s);', $options);
	}

	/**
	 * @var array
	 */
	protected $_totalProperty = 'count';

	/**
	 * @param string $totalProperty
	 * @return KontorX_Ext_Data_Reader_Json
	 */
	public function setTotalProperty($totalProperty) {
		$this->_totalProperty = (string) $totalProperty;
		return $this;
	}
	
	public function getTotalProperty() {
		return $this->_totalProperty;
	}
	
	/**
	 * @var string
	 */
	protected $_id = 'id';

	/**
	 * @param string $id
	 * @return KontorX_Ext_Data_Reader_Json
	 */
	public function setId($id) {
		$this->_id = (string) $id;
		return $this;
	}
	
	public function getId() {
		return $this->_id;
	}

	/**
	 * @var string
	 */
	protected $_root = 'rowset';

	/**
	 * @param string $root
	 * @return KontorX_Ext_Data_Reader_Json
	 */
	public function setRoot($root) {
		$this->_root = (string) $root;
		return $this;
	}
	
	public function getRoot() {
		return $this->_root;
	}
	
	/**
	 * @var array
	 */
	protected $_fields = array();
	
	/**
	 * @param array $fields
	 * @return KontorX_Ext_Data_Reader_Json
	 */
	public function setFields(array $fields) {
		$this->_fields = $fields;
		return $this;
	}
	
	public function getFields() {
		return $this->_fields;
	}
}