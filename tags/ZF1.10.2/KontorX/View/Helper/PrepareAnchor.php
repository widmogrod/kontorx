<?php
require_once 'Zend/View/Helper/Abstract.php';
class KontorX_View_Helper_PrepareAnchor extends Zend_View_Helper_Abstract {

	/**
	 * @param string $anchor
	 * @param bool $schema
	 * @return string
	 */
	public function prepareAnchor($anchor, $schema = true) {
		$anchor = trim($anchor);
		$anchor = str_ireplace(
			array('http://','https://'),
			'',
			$anchor);

		if ($schema) {
			return $anchor = 'http://' . $anchor;
		}

		return $anchor;
	}

	public function direct($anchor = null, $schema = true) {
		if (null !== $anchor) {
			return $this->prepareAnchor($anchor);
		}
	}
}