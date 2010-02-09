<?php
require_once 'KontorX/Update/Abstract.php';
abstract class KontorX_Update_File_Abstract extends KontorX_Update_Abstract {

	/**
	 * @param string $pathname
	 * @return void
	 */
	public function __construct($pathname) {
		$this->_pathname = (string) $pathname;
	}

	/**
	 * @var string
	 */
	protected $_pathname;

	/**
	 * @return string
	 */
	public function getPathname() {
		return $this->_pathname;
	}	
}