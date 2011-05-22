<?php
interface KontorX_Model_Interface_Messanger
{
    public function getMessages();
	public function setMessages(array $messages);
	public function addMessage($message, $type = null);
	public function cleanMessages();
}