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
	 * @var bool
	 */
	protected $_cacheEnabled = true;
	
	/**
	 * @param bool $flag
	 * @return Promotor_View_Helper_GalleryAlbum
	 */
	public function cacheEnable($flag = true) {
		$this->_cacheEnabled = (bool) $flag;
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
			$this->_data[$primaryKey] = $model->fetchAllByIdCache($primaryKey);
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
		}
		
	}
}