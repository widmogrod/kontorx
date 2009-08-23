<?php
require_once 'KontorX/Update/Interface.php';
abstract class KontorX_Update_Abstract implements KontorX_Update_Interface {

	/**
	 * Statusy
	 * @var string
	 */
	const SUCCESS = KontorX_Update_Manager::SUCCESS;
	const FAILURE = KontorX_Update_Manager::FAILURE;

	/**
	 * @var array
	 */
	protected $_messages = array();

	/**
	 * @return array
	 */
	final public function getMessages() {
		return $this->_messages;
	}

	/**
	 * @param string $message
	 * @return KontorX_Update_AbstractKontorX_Update_Abstract
	 */
	final protected function _addMessage($message) {
		$this->_messages[] = $message;
		return $this;
	}
	
	/**
	 * @param Exception $exception
	 * @return KontorX_Update_Abstract
	 */
	final protected function _addException(Exception $exception) {
		$this->_messages[] = sprintf('%s: %s, %s',
									get_class($exception), 
									$exception->getMessage(), 
									$exception->getTraceAsString());

		return $this;
	}
	
	/**
	 * @var string
	 */
	protected $_status;

	/**
	 * @return string
	 */
	final public function getStatus() {
		return $this->_status;
	}

	/**
	 * @param string $status
	 * @return KontorX_Update_Abstract
	 */
	final public function _setStatus($status) {
		$this->_status = (string) $status;
		return $this;
	}
}