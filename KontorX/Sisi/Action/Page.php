<?php
require_once 'KontorX/Sisi/Action/Interface.php';
require_once 'Pages.php';

class KontorX_Sisi_Action_Page implements KontorX_Sisi_Action_Interface
{
	/**
     * @param KontorX_Sisi $sisi
     * @return void
     */
    public function run(KontorX_Sisi $sisi) {
    	$response = $sisi->getResponse();
    	if ($response instanceof KontorX_Sisi_Response_Html) {
    		$response->setScriptName('index');
    	}

    	$pages = new Pages(PAGE_PATHNAME);
    	$pages->setActivePageId($sisi->getParam('id','index'));
    	$page = $pages->getPage();

    	$result = array(
    		'page' => $page,
    	);

    	$response->setData($result);
    }
}
