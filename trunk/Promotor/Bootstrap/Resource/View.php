<?php
class Promotor_Bootstrap_Resource_View extends Zend_Application_Resource_ResourceAbstract {
	public function init(array $options = array()) {
		$view = new Zend_View($options);
		Zend_Dojo::enableView($view);

		$view->addHelperPath('advertising/views/helpers','Advertising_View_Helper_');
		$view->addHelperPath('KontorX/View/Helper','KontorX_View_Helper_');
		
//		$view->addHelperPath('ZendX/JQuery/View/Helper','ZendX_JQuery_View_Helper_');
		
		$view->doctype('XHTML1_STRICT');
//		$view->headTitle()->setSeparator(' - ')->append('My Site');
		$view->headMeta()->appendHttpEquiv('Content-Type', 'text/html; charset=utf-8');

		$view->dojo()->setDjConfigOption('parseOnLoad', true)
					 ->setLocalPath('/js/dojo/dojo/dojo.js')
					 ->addStyleSheetModule('dijit.themes.tundra')
					 ->disable();
	
		$viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer');
		$viewRenderer->setView($view);
	
		Zend_Registry::set('view', $view);
	}
}