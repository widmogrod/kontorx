<?php
class Promotor_Navigation_Recursive_Visitor_Site implements KontorX_Navigation_Recursive_Visitor_Interface {
	public function prepare(array $current) {
		$current['label'] = $current['name'];

		if ('' != $current['alias']) {
			$current['route'] = 'site';
			$current['params'] = array('alias' => $current['alias']);
		} else {
			$current['action'] 	= 'display';
			$current['controller'] = 'site';
			$current['module'] 	= 'site';
			$current['params'] = array('id' => $current['id']);
		}
		return $current;
	}
}