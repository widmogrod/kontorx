<?php
class Promotor_View_Helper_ShopSearchTag extends Zend_View_Helper_Abstract
{
	public function shopSearchTag($name = null, $value = null)
	{
		$tagModel = new Shop_Model_ProductTag();
		$multiOptions = $tagModel->findAllForMultiOptionsCache(true);

		/* @var	$urlParams KontorX_View_Helper_UrlParams */
		$urlParams = $this->view->getHelper('UrlParams');

		$name = (null !== $name) ? $name : 'tag_id';
		$value = $urlParams->urlParams($name, $value);
		
		// w tym miejscu sztuczka, która pozwala odszukać 
		// rekord po jego polu `alias` i przekazać do pola wyboru jego ID
		if (!is_numeric($value) && is_string($value)) {
			$value = $tagModel->findByAliasCache($value);
			$value = is_array($value) ? $value['id'] : null; 
		}

		$attribs = array(
			'class' 	=> 'shop_filter_tag'
		);

		return $this->view->formSelect($name, $value, $attribs, $multiOptions);
	}
}