<?php
require_once 'KontorX/DataGrid/Cell/ViewHelper.php';

/**
 * @author gabriel
 *
 */
class Promotor_DataGrid_Cell_ImageEditor extends KontorX_DataGrid_Cell_ViewHelper {

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
	 * @var string|array
	 */
	protected $_srcFull;
	
	/**
	 * @param string|array $src
	 * @return void
	 */
	public function setSrcFull($src) {
		if (is_array($src)) {
			$url = $this->getView()->getHelper('url');

			if (isset($src['route'])) {
				$route = $src['route'];
				unset($src['route']);
				$this->_srcFull = $url->url($src, $route);
			} else {
				$this->_srcFull = $url->url($src);
			}
		} elseif (is_string($src)) {
			$this->_srcFull = $src;
		}
	}

    /**
     * Return a context as a html/text string
     * @return string
     */
    public function render() {
    	$src   	 = rtrim($this->_src, '/') . '/' . $this->getValue();
    	$srcFull = rtrim($this->_srcFull, '/') . '/' . $this->getValue();
    	$image = sprintf('<img src="%s" alt="%s" />', $src, $this->_alt);
		$image = preg_replace("/{([\wd_\-^}]+)}/ie", "\$this->getData('$1')", $image);

		$return = sprintf('<a target="_blank" href="/tools/image/index?image=%s" title="Klinkij by zedytować zdięcie">%s</a>', $srcFull, $image);
        return $return;
    }
}