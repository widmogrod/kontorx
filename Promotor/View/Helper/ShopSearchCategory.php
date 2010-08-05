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

		if (null === $name)
			$name = 'category_id';

		$value = $this->view->urlParams($name);

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