<?php

/**
 * Description of MagicQuotes
 *
 * @author gabriel
 */
class KontorX_Filter_MagicQuotes implements Zend_Filter_Interface{
    public function filter($value) {
        if  (get_magic_quotes_gpc()) {
            if (is_array($value)) {
                $value = array_map('stripslashes', $value);
            } else {
                $value = $stripslashes($value);
            }
        }

        return $value;
    }
}
