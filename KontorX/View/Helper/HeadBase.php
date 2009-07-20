<?php
class KontorX_View_Helper_HeadBase extends Zend_View_Helper_Abstract {
	/**
	 * @return KontorX_View_Helper_HeadBase
	 */
	public function headBase() {
		return $this;
	}

	/**
	 * @return string
	 */
	public function render() {
		return sprintf(
			'<base href="%s"/>',
			$this->view->getHelper('baseUrl'));
	}

	/**
	 * @return string
	 */
	public function __toString() {
		return $this->render();
	}
}