<?php
require_once 'KontorX/Sisi/Action/Interface.php';
require_once 'Products.php';

class KontorX_Sisi_Action_Products implements KontorX_Sisi_Action_Interface
{
	/**
     * @param KontorX_Sisi $sisi
     * @return void
     */
    public function run(KontorX_Sisi $sisi) {
    	$response = $sisi->getResponse();
    	if ($response instanceof KontorX_Sisi_Response_Html) {
    		$response->setScriptName('asite');
    	}

    	$products = new Products(PRODUCTS_PATHNAME);
    	$products->setActiveCategoryId($sisi->getParam('id'));
    	$categories = $products->getCategories();
    	$category = $products->getCategory();
    	
    	$result = array(
    		'categories' => $categories,
    		'category' => $category,
    		'activeId' => $products->getActiveCategoryId()
    	);

    	$response->setData($result);
    }
}
