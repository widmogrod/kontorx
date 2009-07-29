<?php
require_once 'Zend/Controller/Action.php';

/**
 * KontorX_Controller_Action
 * 
 * @category 	KontorX
 * @package 	KontorX_Controller_Action
 * @license		GNU GPL
 */
abstract class KontorX_Controller_Action extends Zend_Controller_Action {

	/*public function init() {
		$this->_helper->system->addModelIncludePath();
	}*/
	
	public function postDispatch() {
		$this->view->messages = $this->_helper->flashMessenger->getMessages();
	}
}