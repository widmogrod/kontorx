<?php
require_once 'Zend/View/Helper/Abstract.php';
/**
 * Celem pomocnika jest zwracanie wiadomości z @see Zend_Controller_Action_Helper_FlashMessenger
 * @author gabriel
 */
class KontorX_View_Helper_FlashMessages extends Zend_View_Helper_Abstract {

	/**
	 * @var array
	 */
	protected $_messages = array();

	public function __construct() {
		require_once 'Zend/Controller/Action/HelperBroker.php';
		if (!Zend_Controller_Action_HelperBroker::hasHelper('flashMessenger')) {
			require_once 'Zend/Controller/Action/Helper/FlashMessenger.php';
			$flashMessenger = new Zend_Controller_Action_Helper_FlashMessenger();
			Zend_Controller_Action_HelperBroker::addHelper($flashMessenger);
		} else {
			$flashMessenger = Zend_Controller_Action_HelperBroker::getExistingHelper('flashMessenger');
		}
		$this->_messages = $flashMessenger->getMessages();
	}

	/**
	 * @return array
	 */
	public function flashMessages() {
		return $this->_messages;
	}
}