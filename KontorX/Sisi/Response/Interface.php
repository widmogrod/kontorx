<?php
/**
 * @author $Author$
 * @version $Id$
 */
interface KontorX_Sisi_Response_Interface
{
        /**
         * @param string $message
         * @param string $type
         */
        public function addMessage($message, $type = null);
        
        /**
         * @return array
         */
        public function getMessages();
        
        /**
         * @param mixed $data
         */
        public function setData($data);
        
        /**
         * @return mixed
         */
        public function getData();
        
        /**
         * @param bool $flag
         */
        public function setStatus($flag);
        
        /**
         * @return bool
         */
        public function getStatus();
        
        /**
         * @return string
         */
        public function send();
}
