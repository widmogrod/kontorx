<?php
require_once 'Zend/View/Helper/Abstract.php';
class KontorX_View_Helper_AbsoluteUrl extends Zend_View_Helper_Abstract {
	public function AbsoluteUrl(array $params, $router = null) {
		// TODO http or https ..
		return 'http://' . getenv('SERVER_NAME') . $this->view->url($params, $router);
	}
}