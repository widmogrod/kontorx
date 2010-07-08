<?php
require_once 'KontorX/Sisi/Action/Interface.php';

class KontorX_Sisi_Action_Home implements KontorX_Sisi_Action_Interface
{
	/**
     * @param KontorX_Sisi $sisi
     * @return void
     */
    public function run(KontorX_Sisi $sisi) {
    	$response = $sisi->getResponse();
    	if ($response instanceof KontorX_Sisi_Response_Html) {
    		$response->setScriptName('home');
    	}
    }
}
