<?php
class Promotor_View_Helper_SiteNavigation extends Zend_View_Helper_Navigation {
	/**
	 * @param string $proxy
	 * @return Promotor_View_Helper_SiteNavigation
	 */
	public function siteNavigation($proxy = null) {
		if (null !== $proxy) {
			$this->setDefaultProxy($proxy);
		}

		$model = new Site_Model_Site();
		$container = $model->getNavigation();
		
		$this->setContainer($container);
		return $this;
	}
}