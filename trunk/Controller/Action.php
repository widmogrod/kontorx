<?php
require_once 'Zend/Controller/Action.php';

/**
 * KontorX_Controller_Action
 * 
 * @category 	KontorX
 * @package 	KontorX_Controller_Action
 * @version 	0.2.2
 * @license		GNU GPL
 * @author 		Marcin `widmogror` Habryn, widmogrod@gmail.com
 */
abstract class KontorX_Controller_Action extends Zend_Controller_Action {

	public function init() {
		$this->_helper->system->addModelIncludePath();
	}

	/**
	 * @Overwrite
	 *
	 * @return Zend_View_Interface
	 */
	public function initView() {
		$view = parent::initView();

		// setup
		$helperPath = 'KontorX/View/Helper';
		$view->addHelperPath($helperPath, 'KontorX_View_Helper');
		
		return $view;
	}

	public function postDispatch() {
		$this->view->messages = $this->_helper->flashMessenger->getMessages();
	}
}