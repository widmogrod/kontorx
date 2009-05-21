<?php
require_once 'KontorX/Archive/Abstract.php';
class KontorX_Archive_Zip extends KontorX_Archive_Abstract {
	/**
	 * @var ZipArchive
	 */
	protected $_resource;

	/**
	 * @return ZipArchive
	 */
	public function getResource() {
		if (null === $this->_resource) {
			$this->_resource = new ZipArchive();
		}
		return $this->_resource;
	}

	public function extract($path) {
		$file = $this->getFile();
		$zip = $this->getResource();
		$zip->open($file);
	}
}