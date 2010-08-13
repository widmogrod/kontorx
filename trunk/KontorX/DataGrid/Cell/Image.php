<?php
require_once 'KontorX/DataGrid/Cell/ViewHelper.php';

/**
 * @author gabriel
 *
 */
class KontorX_DataGrid_Cell_Image extends KontorX_DataGrid_Cell_ViewHelper {

	/**
	 * @var string
	 */
	protected $_alt;
	
	/**
	 * @return string
	 */
	public function setAlt($alt) {
		return $this->_alt = $alt;
	}

	/**
	 * @var string|array
	 */
	protected $_src;
	
	/**
	 * @param string|array $src
	 * @return void
	 */
	public function setSrc($src) {
		if (is_array($src)) {
			$url = $this->getView()->getHelper('url');

			if (isset($src['route'])) {
				$route = $src['route'];
				unset($src['route']);
				$this->_src = $url->url($src, $route);
			} else {
				$this->_src = $url->url($src);
			}
		} elseif (is_string($src)) {
			$this->_src = $src;
		}
	}

    /**
     * Return a context as a html/text string
     * @return string
     */
    public function render() {
    	$src   = (null === $this->_src)
    		? '' : rtrim($this->_src, '/') . '/';

    	$src .= $this->getValue();

    	$image = sprintf('<img src="%s" alt="%s" />', $src, $this->_alt);
		$image = preg_replace("/{([\wd_\-^}]+)}/ie", "\$this->getData('$1')", $image);
        return $image;
    }
}