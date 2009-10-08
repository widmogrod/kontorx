<?php
class Promotor_View_Helper_GalleryImage extends Zend_View_Helper_Abstract {

	/**
	 * @var string
	 */
	protected $_partial;
	
	/**
	 * @var string
	 */
	protected $_module;
	
	/**
	 * @var integer
	 */
	protected $_limitRand;
	
	/**
	 * @param integer|string $limitRand
	 * @return Promotor_View_Helper_GalleryImage
	 */
	public function setLimitRand($limitRand) {
		$this->_limitRand = $limitRand;
		return $this;
	}

	/**
	 * @param integer $limitRand
	 * @param string $partial
	 * @param string $module
	 * @return Promotor_View_Helper_GalleryImage
	 */
	public function galleryImage($limitRand = null, $partial = null, $module = null) {
		if (null !== $partial) {
			$this->_partial = $partial;
		}
		if (null !== $module) {
			$this->_module = $module;
		}
		if (null !== $limitRand) {
			$this->_limitRand = $limitRand;
		}
		return $this;
	}
	
	/**
	 * @var array
	 */
	protected $_data = array();
	
	/**
	 * @param integer $limitRand
	 * @return array
	 */
	protected function _getData($limitRand) {
		if (!array_key_exists($limitRand, $this->_data)) {
			/* @var $model Promotor_Model_Abstract */
			$model = new Gallery_Model_Image();
			
			if (Zend_Registry::isRegistered('Zend_Cache_Hour')) {
				$cache = $model->getResultCache();
				$model->setResultCache(Zend_Registry::get('Zend_Cache_Hour'));
				$this->_data[$limitRand] = $model->fetchRandCache($limitRand);

				$model->setResultCache($cache);
			} else {
				$this->_data[$limitRand] = $model->fetchRand($limitRand);
			}
		}
		return $this->_data[$limitRand];
	}

	/**
	 * @param string $partial
	 * @param string $module
	 * @return Promotor_View_Helper_GalleryAlbum
	 */
	public function render($partial = null, $module = null) {
		$rowset = $this->_getData($this->_limitRand);

		if (null === $partial) {
			$partial = $this->_partial;
		}
		if (null === $module) {
			$module = $this->_module;
		}
		
		if (null !== $partial) {
			/* @var $partial Zend_View_Helper_Partial */
			$partial = $this->view->getHelper('partial');
			return $partial->partial($partial, $module, array('rowset' => $rowset));
		}

		if (count($rowset) == 1) {
			return sprintf('<img src="/upload/gallery/polaroid/%s" alt="%s" />', $rowset[0]['image'], $rowset[0]['name']);
		}

		return $rowset;
	}

	/**
	 * @return string
	 */
	public function __toString() {
		try {
			return (string) $this->render();
		} catch (Exception $e) {
			trigger_error($e->getMessage(), E_USER_WARNING);
			return '';
		}
	}
}