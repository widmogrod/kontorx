<?php
class Promotor_Navigation_Recursive_Visitor_CatalogDistrict
	implements KontorX_Navigation_Recursive_Visitor_Interface {

	public function prepare(array $current) {
		$current['label'] = strip_tags($current['name']);
		$current['visible'] = true;

		$current['title'] = strlen(@$current['meta_title'])
			? strip_tags($current['meta_title'])
			: strip_tags($current['name']);

		if (strlen(@$current['url']) > 0) {
			$params = array(
				'url' => $current['url'],
				'id' => $current['id']
			);

			$current['route'] = 'catalog-category';
			$current['params'] = $params;
		} else {
			$current['route']  		= 'default';
			$current['action'] 		= 'index';
			$current['controller'] 	= 'list';
			$current['module'] 		= 'catalog';
			$current['params']  	= array('id' => $current['id']);
		}
		return $current;
	}
}