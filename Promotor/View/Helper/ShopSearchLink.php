<?php
class Promotor_View_Helper_ShopSearchLink extends Zend_View_Helper_Abstract
{
	public function shopSearchLink($name, array $attribs)
	{
		$intersect = array_intersect_key((array) $_GET, $attribs);
		$isActive = ($intersect == $attribs);
		
		$class = $isActive ? 'active' : '';
		
		$uri = $this->view->url(array('action'=>'index'),'shop-search');
		$uri .= '?' . http_build_query($attribs);
		
		$link = '<a href="%s" class="shop_filter_price %s">%s</a>';
		$link = sprintf($link, $uri, $class, $name);
		
		return $link;
	}
}