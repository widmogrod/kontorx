<?php
class Promotor_View_Helper_SiteNavigation extends Zend_View_Helper_Abstract 
{
	/**
	 * @return Zend_View_Helper_Navigation
	 */
	public function getNavigation() 
	{
		return $this->view->getHelper('navigation');
	}

	/**
	 * @param string $proxy
	 * @return Promotor_View_Helper_SiteNavigation
	 */
	public function siteNavigation($proxy = null) 
	{
		$model = new Site_Model_Site();
		$container = $model->getNavigation();
		
		$navigation = $this->getNavigation();
		$navigation->setContainer($container);
		
		if (null !== $proxy)
			$navigation->setDefaultProxy($proxy);

		return $navigation;
	}

	public function __call($method, $params)
	{
		$navigation = $this->getNavigation();
		return call_user_func_array(array($navigation, $method), $params);
	}
}