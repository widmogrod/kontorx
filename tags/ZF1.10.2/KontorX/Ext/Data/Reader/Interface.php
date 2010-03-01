<?php
require_once 'KontorX/JavaScript/Interface.php';

/**
 * @author gabriel
 *
 */
interface KontorX_Ext_Data_Reader_Interface
	extends KontorX_JavaScript_Interface {
	
	/**
	 * @param string $id
	 * @return KontorX_Ext_Data_Reader_Interface
	 */
	public function setId($id);

	/**
	 * @return string
	 */
	public function getId();

	/**
	 * @param string $root
	 * @return KontorX_Ext_Data_Reader_Interface
	 */
	public function setRoot($root);

	/**
	 * @return string
	 */
	public function getRoot();

	/**
	 * @param integer $totalProperty
	 * @return KontorX_Ext_Data_Reader_Interface
	 */
	public function setTotalProperty($totalProperty);

	/**
	 * @return integer
	 */
	public function getTotalProperty();

	/**
	 * @param array $fields
	 * @return KontorX_Ext_Data_Reader_Interface
	 */
	public function setFields(array $fields);

	/**
	 * @return array
	 */
	public function getFields();
}