<?php
class Promotor_View_Helper_CatalogDistrictNavigation extends Zend_View_Helper_Navigation {

	/**
	 * @return void
	 */
	public function catalogDistrictNavigation() {
		$container = $this->_getContainer();
		$this->setContainer($container);
		return $this;
	}

	/**
	 * @var Zend_Navigation
	 */
	private $_cont;

	/**
	 * @return Zend_Navigation
	 */
	public function _getContainer() {
		if (null === $this->_cont) {
			$model = new Catalog_Model_District();
			$visitor = new Promotor_Navigation_Recursive_Visitor_CatalogDistrict();

			$rowset = $model->findAllCache();

			$iterator = new KontorX_Navigation_Recursive($rowset);
			$iterator->accept($visitor);
			$this->_cont = $iterator->create();
			$this->_cont = $iterator->getNavigation(); 
		}
		return $this->_cont;
	}
}