<?php
class Promotor_Application_Resource_View extends Zend_Application_Resource_ResourceAbstract {
	public function init(array $options = array()) {
		$view = new Zend_View($options);
		Zend_Dojo::enableView($view);

		$view->addHelperPath('advertising/views/helpers','Advertising_View_Helper_');
		$view->addHelperPath('KontorX/View/Helper','KontorX_View_Helper_');
		$view->addHelperPath('Promotor/View/Helper','Promotor_View_Helper_');
		
		$view->doctype('XHTML1_TRANSITIONAL');
		$view->headMeta()->appendHttpEquiv('Content-Type', 'text/html; charset=utf-8');

		/* @var $dojo Zend_Dojo_View_Helper_Dojo */
		$dojo = $view->getHelper('Dojo');
		$dojo->dojo()
//			->registerModulePath('promotor','../../promotor/dojo/')
//			->requireModule('promotor.admin')
			->setDjConfigOption('parseOnLoad', true)
			 ->setLocalPath('/js/dojo/dojo/dojo.js')
			 ->addStyleSheetModule('dijit.themes.tundra');
					 
		$viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer');
		$viewRenderer->setView($view);
	
		Zend_Registry::set('Zend_View', $view);
		return $view;
	}
}