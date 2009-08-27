<?php
class Promotor_View_Helper_GalleryAlbum extends Zend_View_Helper_Abstract {

	const PARTIAL = 'galleryAlbum.phtml';
	
	/**
	 * @var integer
	 */
	protected $_primaryKey;
	
	/**
	 * @param integer $primaryKey
	 * @return unknown_type
	 */
	public function galleryAlbum($primaryKey) {
		$this->_primaryKey = $primaryKey;
		return $this;
	}
	
	/**
	 * @var array
	 */
	protected $_data = array();
	
	/**
	 * @param integer $primaryKey
	 * @return array
	 */
	protected function _getData($primaryKey) {
		if (!array_key_exists($primaryKey, $this->_data)) {
			$model = new Gallery_Model_Album();
			if (is_int($primaryKey)) {
				@list($row, $rowset) = $model->fetchAllByIdCache($primaryKey);
				$this->_data[$primaryKey] = $rowset;
			} elseif (is_string($primaryKey)) {
				@list($row, $rowset) = $model->fetchAllByAliasCache($primaryKey);
				$this->_data[$primaryKey] = $rowset;
			} else {
				trigger_error(sprintf('Primary key "%s" is not valid', $primaryKey), E_USER_WARNING);
				return array();
			}
		}
		return $this->_data[$primaryKey];
	}

	/**
	 * @param string $partial
	 * @return Promotor_View_Helper_GalleryAlbum
	 */
	public function render($partial = null) {
		if (null === $partial) {
			$partial = self::PARTIAL;
		}

		$data = $this->_getData($this->_primaryKey);
		return $this->view->partial($partial, array('data' => $data));
	}

	/**
	 * @return string
	 */
	public function __toString() {
		try {
			return $this->render();
		} catch (Exception $e) {
			trigger_error($e->getMessage(), E_USER_WARNING);
			return '';
		}
	}
}