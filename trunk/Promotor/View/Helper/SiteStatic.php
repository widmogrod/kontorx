<?php
class Promotor_View_Helper_SiteStatic extends Zend_View_Helper_Abstract {
	const PARTIAL = 'siteStatic.phtml';
	
	/**
	 * @var string
	 */
	protected $_url = null;
	
	/**
	 * @param $url
	 * @return Promotor_View_Helper_SiteStatic
	 */
	public function setUrl($url) {
		$this->_url = (string) $url;
		return $this;
	}
	
	/**
	 * @param $url
	 * @return Promotor_View_Helper_SiteStatic
	 */
	public function siteStatic($url = null) {
		if (null !== $url) {
			$this->setUrl($url);
		}
		return $this;
	}

	/**
	 * @var array
	 */
	protected $_data = array();
	
	/**
	 * @param integer $url
	 * @return array
	 */
	protected function _getData($url = null) {
		if (!array_key_exists($url, $this->_data)) {
			$model = new Site_Model_Site();
			$data = $model->findByUrlCache($url);
			$this->_data[$url] = $data;
		}
		return $this->_data[$url];
	}
	
	/**
	 * @param string $partial
	 * @return Promotor_View_Helper_GalleryAlbum
	 */
	public function render($partial = null) {
		if (null === $partial) {
			$partial = self::PARTIAL;
		}

		$data = $this->_getData($this->_url);
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