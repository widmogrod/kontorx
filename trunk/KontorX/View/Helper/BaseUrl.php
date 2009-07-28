<?php
require_once 'Zend/View/Helper/Abstract.php';

/**
 * @author Gabriel
 */
class KontorX_View_Helper_BaseUrl extends Zend_View_Helper_Abstract {

	/**
	 * @var string
	 */
	protected $_baseUrl;

    /**
     * @param boolean $scriptName
     * @return KontorX_View_Helper_BaseUrl
     */
    public function baseUrl() {
    	return $this;
    }

    /**
     * @return string
     */
    public function __toString() {
    	return $this->getBaseUrl();
    }
    
    /**
     * @return KontorX_View_Helper_BaseUrl
     */
    public function setBaseUrl($baseUrl) {
    	$this->_baseUrl = (string) $baseUrl;
    }

    /**
     * @return string
     */
    public function getBaseUrl() {
    	if (null === $this->_baseUrl) {
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
	
			$this->_baseUrl = $protocol . '://' . $host;
    	}
    	return $this->_baseUrl;
    }

    /**
     * @return KontorX_View_Helper_BaseUrl
     */
    public function direct() {
    	return $this;
    }
}