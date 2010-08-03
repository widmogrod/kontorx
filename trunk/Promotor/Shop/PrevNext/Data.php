<?php
class Promotor_Shop_PrevNext_Data
{
	/**
	 * @var string
	 */
	protected $_groupName;

	/**
	 * Pobierz nazwe grupy
	 * 
	 * @param string $name;
	 */
	public function setGroupName($name) {
		$this->_groupName = $name;
	}
	
	/**
	 * Pobierz nazwy grupy
	 * 
	 * @return string
	 */
	public function getGroupName() {
		return $this->_groupName;
	}

	/**
	 * @var array
	 */
	protected $_prevData;
	
	/**
	 * Ustaw dane poprzedniego produktu
	 * 
	 * @param array $product
	 */
	public function setPrevData(array $product) {
		$this->_prevData = $product;
	}
	
	/**
	 * Pobierz dane produktu poprzedniego
	 * 
	 * @return array
	 */
	public function getPrevData() {
		return $this->_prevData;
	}
	
	/**
	 * @var array
	 */
	protected $_nextData;
	
	/**
	 * Ustaw dane następnego produktu
	 * 
	 * @param array $product
	 */
	public function setNextData(array $product) {
		$this->_nextData = $product;
	}
	
	/**
	 * Pobierz dane produktu następnego
	 * 
	 * @return array
	 */
	public function getNextData() {
		return $this->_nextData;
	}
}