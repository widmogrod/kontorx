<?php
class Promotor_View_Helper_SiteNavigation extends Zend_View_Helper_Navigation {
	const PARTIAL = 'siteNavigation.phtml';
	
	/**
	 * @var KontorX_Db_Table_Tree_Rowset_Abstract
	 */
	protected $_rowset = null;
	
	/**
	 * @param KontorX_Db_Table_Tree_Rowset_Abstract $rowset
	 * @return Promotor_View_Helper_SiteNavigation
	 */
	public function setRowset(KontorX_Db_Table_Tree_Rowset_Abstract $rowset) {
		$this->_rowset = (string) $rowset;
		return $this;
	}

	/**
	 * @return KontorX_Db_Table_Tree_Rowset_Abstract
	 * @throws Zend_Validate_Exception
	 */
	public function getRowset() {
		if (null === $this->_rowset) {
			throw new Zend_Validate_Exception('data rowset do not exsists');
		}
		return $this->_rowset;
	}
	
	/**
	 * @param string $proxy
	 * @return Promotor_View_Helper_SiteNavigation
	 */
	public function siteNavigation($proxy = null) {
		if (null !== $proxy) {
			$this->setDefaultProxy($proxy);
		}
		return $this;
	}
	
	public function render() {
		parent::render($container);
	}
}