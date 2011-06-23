<?php
class KontorX_Controller_Action_Helper_FlashMessenger 
	extends Zend_Controller_Action_Helper_FlashMessenger
	implements KontorX_Model_Interface_Visitor
{
	// stałe określające rodzaj wiadomości
	const OK = "OK";
	const INFO = "INFO";
	const ERROR = "ERROR";
	const WARNING = "WARNING";
	
	public function addMessage($message, $type = null)
    {
        if (self::$_messageAdded === false) {
            self::$_session->setExpirationHops(1, null, true);
        }

        if (!is_array(self::$_session->{$this->_namespace})) {
            self::$_session->{$this->_namespace} = array(
				self::OK => array(),
            	self::INFO => array(),
            	self::ERROR => array(),
            	self::WARNING => array(),
            );
        }

		switch($type)
		{
			case self::OK:
			case self::INFO:
			case self::ERROR:
			case self::WARNING:
				break;
			default:
				$type = self::INFO;
		}
-
        self::$_session->{$this->_namespace}[$type][] = $message;

        return $this;
    }

    public function visit($object)
	{
		switch(true)
		{
			case $object instanceof KontorX_Model_Interface_Messanger:
				foreach($object->getMessages() as $data)
				{
					extract($data);
					$this->addMessage($message, $type);
				}
				break;
				
			default:
				if (method_exists($object, 'getMessages')) 
				{
					foreach($object->getMessages() as $message)
					{
						$this->addMessage($message);
					}
				}
		}
	}
}