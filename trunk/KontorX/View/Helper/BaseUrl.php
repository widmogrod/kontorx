<?php
require_once 'Zend/View/Helper/Abstract.php';

/**
 * @author Gabriel
 */
class KontorX_View_Helper_BaseUrl extends Zend_View_Helper_Abstract {

    /**
     * @param boolean $scriptName
     * @return string
     */
    public static function baseUrl() {
    	if (isset($_SERVER['SERVER_NAME'])) {
    		$host = $_SERVER['SERVER_NAME'];
    	} elseif (isset($_SERVER['HTTP_HOST'])) {
    		$host = $_SERVER['HTTP_HOST'];
    	} else {
    		// no host no play .. ??
    		return '';
    	}

		$protocol = isset($_SERVER['SERVER_PROTOCOL']);
		if (false !== ($strpos = strpos($protocol,'/'))) {
			$protocol = substr($protocol, 0, strpos($protocol,'/'));
		} else {
			$protocol = 'http';
		}

		return $protocol . '://' . $host;
    }
}