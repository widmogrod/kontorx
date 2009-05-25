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
	protected static $_messages;

	/**
	 * @return array
	 */
	public function flashMessages() {
		if (null === self::$_messages) {
			require_once 'Zend/Controller/Action/HelperBroker.php';
			$flashMessenger = Zend_Controller_Action_HelperBroker::getStaticHelper('FlashMessenger');
			self::$_messages = $flashMessenger->getMessages();
		}
		return self::$_messages;
	}
}