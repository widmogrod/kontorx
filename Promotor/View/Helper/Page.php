<?php
class Promotor_View_Helper_Page extends Zend_View_Helper_Abstract {

	public function page($alias, $partial) {
		$model = new Page_Model_Page();
		$data = $model->findByAliasCache($alias);
		return $this->view->partial($partial, $data);
	}
}