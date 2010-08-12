<?php
class Promotor_View_Helper_ShopSearchCategory extends Zend_View_Helper_Abstract
{
	public function shopSearchCategory($name = null)
	{
		$categoryModel = new Shop_Model_Category();
		$rowset = $categoryModel->findForFrontAsRowsetCache();

		// Ratuje przed błędem keszowania
		if ($rowset instanceof KontorX_Db_Table_Tree_Rowset_Abstract)
			$rowset->setTable($categoryModel->getDbTable());

		/* @var	$urlParams KontorX_View_Helper_UrlParams */
		$urlParams = $this->view->getHelper('UrlParams');

		$name = (null !== $name) ? $name : 'category_id';
		$value = $urlParams->urlParams($name);
		
		// w tym miejscu sztuczka, która pozwala odszukać 
		// rekord po jego polu `alias` i przekazać do pola wyboru jego ID
		if (!is_numeric($value) && is_string($value)) {
			$value = $categoryModel->findByAliasCache($value);
			$value = is_array($value) ? $value['id'] : null; 
		}

		$attribs = array(
			'firstNull' => true,
			'labelCol' 	=> 'name',
			'valueCol' 	=> 'id',
			'class' 	=> 'shop_filter_category'
		);
		$options = $rowset;

		return $this->view->formSelectTree($name, $value, $attribs, $options);
	}
}