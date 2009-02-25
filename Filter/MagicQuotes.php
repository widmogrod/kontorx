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
                // rekursywne filtrowanie
                $value = array_map(array($this,'filter'), $value);
            } else {
                $value = stripslashes($value);
            }
        }

        return $value;
    }
}
