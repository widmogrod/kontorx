<?php
/**
 * @author gabriel
 */
interface KontorX_Db_Table_Tree_Row_Interface {

	/**
	 * Return depth of row nest..
	 * @return integer
	 */
	public function getDepth();
	
	/**
	 * Ustawia obiekt @see Zend_Db_Table_Row_Abstract rodzica
	 * @param Zend_Db_Table_Row_Abstract $row
	 */
	public function setParentRow(Zend_Db_Table_Row_Abstract $row);

	/**
	 * @param bool $flag
	 * @return void
	 */
	public function setRoot($flag = true);
	
	/**
	 * @return bool
	 */
	public function isRoot ();
	
	/**
	 * Znajdz potomków
	 * @return object
	 */
	public function findDescendant();
	
	/**
	 * Znajdz rodziców rodzica ;]
	 * @return object
	 */
	public function findParents();

	/**
	 * @return object
	 */
	public function findChildrens();
}