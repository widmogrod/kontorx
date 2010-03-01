<?php
class Prana_Notification {
	
	/**
	 * @var string
	 */
	protected $_name;
	
	/**
	 * @var object
	 */
	protected $_object;
	
	/**
	 * @var array
	 */
	protected $_userInfo;
	
	/**
	 * @param string $name
	 * @param object $object
	 * @param array $userInfo
	 * @return void
	 */
	public function __construct($name, $object, array $userInfo = array()) {
		$this->_name = (string) $name;
		$this->_object = (object) $object;
		$this->_userInfo = $userInfo;
	}

	/**
	 * @return string
	 */
	public function getName() {
		return $this->_name;
	}

	/**
	 * @return object
	 */
	public function getObject() {
		return $this->_object;
	}

	/**
	 * @return array
	 */
	public function getUserInfo() {
		return $this->_userInfo;
	}
}