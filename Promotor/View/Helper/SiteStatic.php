<?php
class Promotor_View_Helper_SiteStatic extends Zend_View_Helper_Abstract {
	const PARTIAL = 'siteStatic.phtml';
	
	/**
	 * @var string
	 */
	protected $_alias = null;
	
	/**
	 * @param $alias
	 * @return Promotor_View_Helper_SiteStatic
	 */
	public function setAlias($alias) {
		$this->_alias = (string) $alias;
		return $this;
	}
	
	/**
	 * @param $alias
	 * @return Promotor_View_Helper_SiteStatic
	 */
	public function siteStatic($alias = null) {
		if (null !== $alias) {
			$this->setAlias($alias);
		}
		return $this;
	}

	/**
	 * @var array
	 */
	protected $_data = array();
	
	/**
	 * @param integer $alias
	 * @return array
	 */
	protected function _getData($alias = null) {
		if (!array_key_exists($alias, $this->_data)) {
			$model = new Site_Model_Site();
			$data = $model->findByAliasCache($alias);
			$this->_data[$alias] = $data;
		}
		return $this->_data[$alias];
	}
	
	/**
	 * @param string $partial
	 * @return Promotor_View_Helper_GalleryAlbum
	 */
	public function render($partial = null) {
		if (null === $partial) {
			$partial = self::PARTIAL;
		}

		$data = $this->_getData($this->_alias);
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