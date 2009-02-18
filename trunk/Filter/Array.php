<?php
require_once 'Zend/Filter/Interface.php';
class KontorX_Filter_Array implements Zend_Filter_Interface {
    public function filter($value) {
        if (!is_array($value)) {
            return array();
        }

        switch(true) {
            default:
                $value = array_filter($value);
                break;
        }

        return (array) $value;
    }
}
