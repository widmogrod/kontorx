<?php
class Promotor_View_Helper_CatalogDistrictNavigation extends Zend_View_Helper_Navigation {

	/**
	 * @return void
	 */
	public function catalogDistrictNavigation() {
		return $this;
	}

	/**
	 * @var Zend_Navigation_Container
	 */
	protected $_container;

	/**
	 * @return Zend_Navigation_Container
	 */
	public function getContainer() {
		if (null === $this->_container) {
			$model = new Catalog_Model_District();
			$visitor = new Promotor_Navigation_Recursive_Visitor_CatalogDistrict();

			$rowset = $model->findAllCache();

			$navigation = new KontorX_Navigation_Recursive($rowset);
			$navigation->accept(new Promotor_Navigation_Recursive_Visitor_CatalogDistrict());
			$this->_container = $navigation->create();
		}
		return $this->_container;
	}
}