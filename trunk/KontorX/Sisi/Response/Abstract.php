<?php
require_once 'KontorX/Sisi/Response/Interface.php';

/**
 * @author $Author$
 * @version $Id$
 */
abstract class KontorX_Sisi_Response_Abstract implements KontorX_Sisi_Response_Interface
{
        /**
         * @var array
         */
        protected $_messages = array();
        
        /**
         * @var mixed
         */
        protected $_data;
        
        /**
         * @var bool
         */
        protected $_status;
        
        /**
         * @param string $message
         * @param string $type
         */
        public function addMessage($message, $type = null) {
                $this->_messages[] = (string) $message;
        }

        /**
         * @return array
         */
        public function getMessages() {
                return $this->_messages;
        }
        
        /**
         * @param mixed $data
         */
        public function setData($data) {
                $this->_data = $data;
        }
        
        /**
         * @return mixed
         */
        public function getData() {
                return $this->_data;
        }

        /**
         * @param bool $flag
         */
        public function setStatus($flag) {
                $this->_status = (bool) $flag;
        }
        
        /**
         * @return bool
         */
        public function getStatus() {
                return $this->_status;
        }
}
