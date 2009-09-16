<?php
class Promotor_View_Helper_SiteAttachment extends Zend_View_Helper_Abstract {

	const NEWS = 'news';
	const IMAGE = 'image';
	const MEDIA = 'media';

	/**
	 * @var string
	 */
	protected $_alias;
	
	/**
	 * @var integer
	 */
	protected $_page;
	
	/**
	 * @var integer
	 */
	protected $_rowCount;
	
	/**
	 * @var string
	 */
	protected $_type = self::NEWS;
	
	/**
	 * @var string
	 */
	protected $_partial = 'newsAttachment.phtml';
	
	/**
	 * @param string $alias
	 * @param string $type
	 * @param string $partial
	 * @return Promotor_View_Helper_SiteAttachment 
	 */
	public function siteAttachment($alias, $type = null, $partial = null) {
		$this->setAlias($alias);
		if (null !== $type) {
			$this->setType($type);
		}
		if (null !== $partial) {
			$this->setPartial($partial);
		}
		return $this;
	}
	
	/**
	 * @param integer $page
	 * @return Promotor_View_Helper_SiteAttachment 
	 */
	public function setPage($page) {
		$this->_page = (integer) $page;
		return $this;
	}
	
	/**
	 * @param integer $rowCount
	 * @return Promotor_View_Helper_SiteAttachment 
	 */
	public function setRowCount($rowCount) {
		$this->_rowCount = (integer) $rowCount;
		return $this;
	}

	/**
	 * @param string $alias
	 * @return Promotor_View_Helper_SiteAttachment 
	 */
	public function setAlias($alias) {
		$this->_alias = (string) $alias;
		return $this;
	}
	
	/**
	 * @param string $partial
	 * @return Promotor_View_Helper_SiteAttachment 
	 */
	public function setPartial($partial) {
		$this->_partial = (string) $partial;
		return $this;
	}
	
	/**
	 * @param string $type
	 * @return Promotor_View_Helper_SiteAttachment 
	 */
	public function setType($type) {
		$type = strtolower((string) $type);
		switch ($type) {
			case self::NEWS:
				$this->_type = $type;
				break;
		}
		return $this;
	}

	public function render($partial = null) {
		$data = $this->_getData();
		if (null === $partial) {
			$partial = $this->_partial;
		}
		return $this->view->partial($partial, $data);
	}
	
	/**
	 * @return array
	 */
	protected function _getData() {
		switch ($this->_type) {
			case self::NEWS:
			case self::IMAGE:
			case self::MEDIA:
				$model = new Site_Model_Site();
				$rowset = $model->findAttachments($this->_alias, $this->_type, $this->_rowCount, $this->_page);
				return array($this->_type => array('rowset' => $rowset));
		}

		// @todo Exception!
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