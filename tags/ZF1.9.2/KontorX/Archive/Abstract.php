<?php
require_once 'KontorX/Archive/Interface.php';
abstract class KontorX_Archive_Abstract implements KontorX_Archive_Interface {

	/**
	 * @var string
	 */
	protected $_file = null;
	
	/**
	 * @param $file
	 * @return KontorX_Archive_Abstract
	 * @throws KontorX_Archive_Exception
	 */
	public function setFile($file) {
		if (!is_file($file) || !is_readable($file)) {
			require_once 'KontorX/Archive/Exception.php';
			throw new KontorX_Archive_Exception(sprintf('archive file "%s" do not exsists or is not readable', $file));
		}
		$this->_file = (string) $file;
		return $this;
	}
	
	/**
	 * @return string
	 * @throws KontorX_Archive_Exception
	 */
	public function getFile() {
		if (null === $this->_file) {
			require_once 'KontorX/Archive/Exception.php';
			throw new KontorX_Archive_Exception('archive file is not set');
		}
		return $this->_file;
	}
}