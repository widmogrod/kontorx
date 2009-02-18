<?php

/**
 * Description of MagicQuotes
 *
 * @author gabriel
 */
class KontorX_Filter_MagicQuotes implements Zend_Filter_Interface{
    public function filter($value) {
        return get_magic_quotes_gpc() ? stripslashes($value) : $value;
    }
}
