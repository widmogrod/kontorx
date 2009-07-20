<?php
class KontorX_View_Helper_HeadBase extends Zend_View_Helper_Abstract {
	/**
	 * @return KontorX_View_Helper_HeadBase
	 */
	public function headBase() {
		return sprintf(
			'<base href="%s"/>',
			$this->view->baseUrl());
	}
}