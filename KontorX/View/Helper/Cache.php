<?php
require_once 'KontorX/View/Helper/Cache/Abstract.php';
class KontorX_View_Helper_Cache extends KontorX_View_Helper_Cache_Abstract {
	public function cache() {
		return $this->getCacheInstance();
	}
}