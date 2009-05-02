<?php
/**
 * @see Zend_Filter_Interface
 */
require_once 'Zend/Filter/Interface.php';

/**
 * @name KontorX_Filter_File_UniqName
 */
class KontorX_Filter_File_UniqName implements Zend_Filter_Interface {

    public function filter($value) {
        $dirname = dirname($value);
        $basename = basename($value);
        $pathname = $dirname . DIRECTORY_SEPARATOR . $this->_uniqFilename($basename);

        if (file_exists($pathname)) {
            require_once 'Zend/Filter/Exception.php';
            throw new Zend_Filter_Exception(sprintf("File '%s' could not be renamed. It already exists.", $pathname));
        }

        if (!rename($value, $pathname)) {
            require_once 'Zend/Filter/Exception.php';
            throw new Zend_Filter_Exception(sprintf("File '%s' could not be renamed. An error occured while processing the file.", $value));
        }

        return $pathname;
    }

    /**
     * @param string $filename
     * @return string
     */
    protected function _uniqFilename($filename) {
        return uniqid() . '_' . $filename;
    }
}