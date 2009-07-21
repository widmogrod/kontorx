<?php
class Promotor_View_Helper_Site_Childrens extends Promotor_View_Helper_Site_Abstract {

	/**
	 * @var integer
	 */
	protected $_depthLevel;
	
	/**
	 * @param integer $level
	 */
	public function setDepthLevel($level) {
		$this->_depthLevel = $level;
	}

	/**
	 * @var Zend_Navigation
	 */
	protected $_navigation;
	
	/**
	 * @return Zend_Navigation
	 */
	public function getNavigation() {
		return $this->_navigation;
	}
	
	/**
	 * @return Zend_Navigation_Container  
	 */
	public function render() {
		/* @var $model Site_Model_Site */
		$model = $this->_site->getModel();
		/* @var $rowset KontorX_Db_Table_Tree_Rowset_Abstract */
		$rowset = $model->findChildrens($this->getIdentification(), $this->_depthLevel);

		if (!$rowset instanceof KontorX_Db_Table_Tree_Rowset_Abstract) {
			return '';
		}

		$recursive = new RecursiveIteratorIterator($rowset, RecursiveIteratorIterator::SELF_FIRST);
		$navigation = new KontorX_Navigation_Recursive($rowset);
		$navigation->accept(new Promotor_Navigation_Recursive_Visitor_Site());

		return $this->_navigation = $navigation->create();
	}
}