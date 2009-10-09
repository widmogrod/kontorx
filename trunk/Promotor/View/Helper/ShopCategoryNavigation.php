<?php
class Promotor_View_Helper_ShopCategoryNavigation extends Zend_View_Helper_Navigation {

	/**
	 * @param string $proxy
	 * @return Promotor_View_Helper_ShopCategoryNavigation
	 */
	public function ShopCategoryNavigation($proxy = null) {
		if (null !== $proxy) {
			$this->setDefaultProxy($proxy);
		}

		$model = new Shop_Model_Category();
		$container = $model->getNavigation();
		$this->setContainer($container);
		return $this;
	}
}