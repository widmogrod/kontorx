<?php
class KontorX_View_Helper_StyleResource extends Zend_View_Helper_Abstract {

	/**
	 * @return string
	 */
	public function styleResource($resourcePath) {
		$template = KontorX_Template::getInstance();

		$resourcePath = ltrim($resourcePath, '/');

		return PUBLIC_TEMPLATES_DIRNAME . '/' . 
			   $template->getActiveStylePath() . '/' . 
			   $resourcePath;
	}
}