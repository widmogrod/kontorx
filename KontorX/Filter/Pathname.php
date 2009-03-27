<?php
/**
 * @see Zend_Filter_Interface
 */
require_once 'Zend/Filter/Interface.php';

/**
 * Description of Pathname
 *
 * @author gabriel
 */
class KontorX_Filter_Pathname implements Zend_Filter_Interface {
    public function filter($value) {
        $search = array('//','\\\\');
        $replace = DIRECTORY_SEPARATOR;
        return str_replace($search, $replace, (string) $value);
    }
}