<?php
require_once 'Promotor/Observable/Observer/Interface.php';
abstract class Promotor_Observable_Observer_Abstract implements Promotor_Observable_Observer_Interface {
	const SUCCESS = 'SUCCESS';
	const FAILURE = 'FAILURE';
	
	/**
	 * @return string
	 */
	final public function getName() {
		return get_class($this);
	}
	
	/**
	 * @var string
	 */
	private $_status = null;
	
	/**
	 * @return string
	 */
	public function getStatus() {
		$status = $this->_status;
		return $status;
	}

	/**
	 * @param string $status
	 * @return Promotor_Observable_Observer_Abstract
	 */
	protected function _setStatus($status) {
		$this->_status = $status;
	}
	
	/**
	 * @var mixed
	 */
	private $_result = null;
	
	/**
	 * @return string
	 */
	public function getResult() {
		return $this->_result;
	}

	/**
	 * @param string $result
	 * @return Promotor_Observable_Observer_Abstract
	 */
	protected function _setResult($result) {
		$this->_result = $result;
	}
	
	/**
	 * @var array
	 */
	private $_messages = array();
	
	/**
	 * @param bool $withExceptions
	 * @return array
	 */
	public function getMessages($withExceptions = true) {
		$messages = $this->_messages;
		if ($withExceptions) {
			foreach ($this->_exception as $exception) {
				$message = $exception->getMessage() . "\n" . $exception->getTraceAsString();
				$messages[] = $message;
			}
		}
		return $messages;
	}

	/**
	 * @param array $messages
	 * @return Promotor_Observable_Observer_Abstract
	 */
	protected function _setMessages(array $messages) {
		$this->_messages = $messages;
		return $this;
	}
	
	/**
	 * @param string $message
	 * @return Promotor_Observable_Observer_Abstract
	 */
	protected function _addMessage($message) {
		$this->_messages[] = $message;
		return $this;
	}
	
	/**
	 * @param array $messages
	 * @return Promotor_Observable_Observer_Abstract
	 */
	protected function _addMessages(array $messages) {
		$this->_messages = array_merge($this->_messages, $messages);
		return $this;
	}
	
	/**
	 * @var array
	 */
	protected $_exception = array();

	/**
	 * @return array
	 */
	public function getExceptions() {
		return $this->_exception;
	}
	
	/**
	 * @param Exception $exception
	 * @return Promotor_Observable_Observer_Abstract
	 */
	protected function _addException(Exception $exception) {
		$this->_exception[] = $exception;
		return $this;
	}
}