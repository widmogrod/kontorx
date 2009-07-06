<?php
/**
 * @see Zend_Filter_Interface
 */
require_once 'Zend/Filter/Interface.php';

/**
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
