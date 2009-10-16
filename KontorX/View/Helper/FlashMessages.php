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

	/**
	 * @var Zend_Controller_Action_Helper_FlashMessenger
	 */
	protected $_flashMessanger;
	
	/**
	 * @return array
	 */
	public function flashMessages() {
		return $this->get();
	}

	/**
	 * @return array
	 */
	public function get() {
		$fm = $this->getFlashMessenger();

		$result = array();
		if (is_array($messages = $fm->getMessages())) {
			$result += $messages;
			$fm->clearMessages();
		}

		if ($fm->hasCurrentMessages()) {
			$result += $fm->getCurrentMessages();
			$fm->clearCurrentMessages();
		}

		return $result;
	}

	/**
	 * @return Zend_Controller_Action_Helper_FlashMessenger
	 */
	public function getFlashMessenger() {
		if (null === $this->_flashMessanger) {
			require_once 'Zend/Controller/Action/HelperBroker.php';
			if (!Zend_Controller_Action_HelperBroker::getStaticHelper('flashMessenger')) {
				require_once 'Zend/Controller/Action/Helper/FlashMessenger.php';
				$this->_flashMessanger = new Zend_Controller_Action_Helper_FlashMessenger();
				Zend_Controller_Action_HelperBroker::addHelper($this->_flashMessanger);
			} else {
				$this->_flashMessanger = Zend_Controller_Action_HelperBroker::getExistingHelper('flashMessenger');
			}
		}
		return $this->_flashMessanger;
	}
}