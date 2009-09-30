<?php
class Promotor_View_Helper_GalleryAlbum extends Zend_View_Helper_Abstract {

	const PARTIAL = 'galleryAlbum.phtml';
	
	/**
	 * @var integer
	 */
	protected $_primaryKey;
	
	/**
	 * @param integer|string $primaryKey
	 * @return Promotor_View_Helper_GalleryAlbum
	 */
	public function setPrimaryKey($primaryKey) {
		$this->_primaryKey = $primaryKey;
		return $this;
	}
	
	/**
	 * @var integer
	 */
	protected $_albumType;
	
	/**
	 * @param string $type
	 * @return Promotor_View_Helper_GalleryAlbum
	 */
	public function setAlbumType($type) {
		$this->_albumType = $type;
		return $this;
	}

	/**
	 * @param integer|string $primaryKey
	 * @return Promotor_View_Helper_GalleryAlbum
	 */
	public function galleryAlbum($primaryKey) {
		$this->_primaryKey = (null === $primaryKey)
			? null
			: strtolower((string) $primaryKey);

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
				$this->_data[$primaryKey] = $model->fetchAllByIdCache($primaryKey);
			} elseif (is_string($primaryKey)) {
				$this->_data[$primaryKey] = $model->fetchAllByAliasCache($primaryKey);
			} else {
				trigger_error(sprintf('Primary key "%s" is not valid', $primaryKey), E_USER_WARNING);
				return array();
			}
		}
		return $this->_data[$primaryKey];
	}

	/**
	 * @param string $name
	 * @return Promotor_View_Helper_GalleryAlbum
	 */
	public function render($name = null) {
		/* @var $partial Zend_View_Helper_Partial */
		$partial = $this->view->getHelper('partial');

		list($row, $rowset) = $this->_getData($this->_primaryKey);
		
		$model = array(
			'data' => $rowset,
			'row' => $row,
			'rowset' => $rowset,
			'displayContent' => false,
		);
		
		switch ($this->_albumType) {
			case 'jshorizont':
			case 'autoviewer':
			case 'simpleviewer':
			case 'tiltviewer':
			case 'postcardviewer':
				$model['type'] = $this->_albumType;
				$name = 'album/display-' . $this->_albumType . '.phtml';
				return $partial->partial($name, 'gallery', $model);

			default:
				$name = (null === $name)
					? $name = self::PARTIAL
					: $name;

				return $partial->partial($name, null, $model);
		}
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