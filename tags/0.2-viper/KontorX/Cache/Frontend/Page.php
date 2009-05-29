<?php
/**
 * @see Zend_Cache_Frontend_Page
 */
require_once 'Zend/Cache/Frontend/Page.php';

class KontorX_Cache_Frontend_Page extends Zend_Cache_Frontend_Page {
    /**
     * Start the cache
     *
     * @param  string  $id       (optional) A cache id (if you set a value here, maybe you have to use Output frontend instead)
     * @param  boolean $doNotDie For unit testing only !
     * @return boolean True if the cache is hit (false else)
     */
    public function start($id = false, $doNotDie = false)
    {
        $this->_cancel = false;
        $lastMatchingId = null;
        $lastMatchingRegexp = null;
        foreach ($this->_specificOptions['regexps'] as $regexp => $conf) {
            $matched = array();
            if (preg_match("`$regexp`", $_SERVER['REQUEST_URI'], $matched)) {
                $lastMatchingId = isset($matched['id']) ? $matched['id'] : false;
                $lastMatchingRegexp = $regexp;
            }
        }
        $this->_activeOptions = $this->_specificOptions['default_options'];
        if (!is_null($lastMatchingRegexp)) {
            $conf = $this->_specificOptions['regexps'][$lastMatchingRegexp];
            foreach ($conf as $key=>$value) {
                $this->_activeOptions[$key] = $value;
            }
        }
        if (!($this->_activeOptions['cache'])) {
            return false;
        }
        if (!$id) {
            if (false !== $lastMatchingId) {
                $id = $lastMatchingId;
            } else {
                $id = $this->_makeId($lastMatchingData);
            }
            if (!$id) {
                return false;
            }
        }
        $array = $this->load($id);
        if ($array !== false) {
            $data = $array['data'];
            $headers = $array['headers'];
            if ($this->_specificOptions['debug_header']) {
                echo 'DEBUG HEADER : This is a cached page !';
            }
            if (!headers_sent()) {
                foreach ($headers as $key=>$headerCouple) {
                    $name = $headerCouple[0];
                    $value = $headerCouple[1];
                    header("$name: $value");
                }
            }
            echo $data;
            if ($doNotDie) {
                return true;
            }
            die();
        }
        ob_start(array($this, '_flush'));
        ob_implicit_flush(false);
        return false;
    }
}