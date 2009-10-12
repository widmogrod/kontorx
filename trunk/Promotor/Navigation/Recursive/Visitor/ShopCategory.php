<?php
class Promotor_Navigation_Recursive_Visitor_ShopCategory implements KontorX_Navigation_Recursive_Visitor_Interface {
	public function prepare(array $current) {
		$current['label'] = @$current['name'];

		if (strlen(@$current['alias']) > 0) {
			$params = array('alias' => $current['alias']);
		} else {
			$params	= array('alias' => $current['id']);
		}

		$current['route'] = 'shop-category';
		$current['route'] = 'shop-category';
		$current['resetParams'] = true;
		$current['params'] = $params;

		return $current;
	}
}