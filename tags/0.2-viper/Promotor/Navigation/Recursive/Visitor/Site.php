<?php
class Promotor_Navigation_Recursive_Visitor_Site implements KontorX_Navigation_Recursive_Visitor_Interface {
	public function prepare(array $current) {
		$current['label'] = @$current['name'];
		if (strlen(@$current['alias']) > 0) {
			$params = array('alias' => $current['alias']);
			if (strlen(@$current['locale']) > 0) {
				$params['locale'] = $current['locale'];
			}
			$current['route'] = 'site';
			$current['params'] = $params;
		} else {
			$current['route']  		= 'default';
			$current['action'] 		= 'display';
			$current['controller'] 	= 'site';
			$current['module'] 		= 'site';
			$current['params']  	= array('id' => $current['id']);
		}
		return $current;
	}
}