<?php
require_once 'KontorX/Sisi/Response/Abstract.php';

/**
 * @author $Author$
 * @version $Id$
 */
class KontorX_Sisi_Response_Json extends KontorX_Sisi_Response_Abstract
{
        public function __construct() {
                if (!function_exists('json_encode'))
                        throw new Exception ('PHP extension "json" is not enable');
        }
        /**
         * @return string
         */
        public function send() {
                $data = array(
                        'messages' => $this->getMessages(),
                        'response' => $this->getData()
                );
                
                return json_encode($data);
        }
}
