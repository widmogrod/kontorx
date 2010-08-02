<?php
require_once 'Zend/View/Helper/Abstract.php';
class KontorX_View_Helper_FormLink extends Zend_View_Helper_Abstract
{
	public function formLink($name, $value = null, $attribs = null,
		$options = null, $listsep = "<br />\n")
	{
		if (isset($attribs['id'])) {
			unset($attribs['id']);
		}

		if (isset($attribs['options'])) {
			$options = $attribs['options'];
			unset($attribs['options']);
		}
		
		if (isset($attribs['listsep'])) {
			$listsep = $attribs['listsep'];
			unset($attribs['listsep']);
		}

		$routeName = null;
		if (isset($attribs['routeName'])) {
			$routeName = $attribs['routeName'];
			unset($attribs['routeName']);
		}
			
		$reset = false;
		if (isset($attribs['reset'])) {
			$reset = $attribs['reset'];
			unset($attribs['reset']);
		}

		$encode = false;
		if (isset($attribs['encode'])) {
			$encode = $attribs['encode'];
			unset($attribs['encode']);
		}

		$mapKeyToUrlParam = $name;
		if (isset($attribs['mapKeyToUrlParam'])) {
			$mapKeyToUrlParam = $attribs['mapKeyToUrlParam'];
			unset($attribs['mapKeyToUrlParam']);
		}
		
		if (isset($attribs['urlOptions'])) {
			$urlOptions = $attribs['urlOptions'];
			unset($attribs['urlOptions']);
		} else {
			$urlOptions = $attribs;
		}

		/* @var $url Zend_View_Helper_Url */
		$url = $this->view->getHelper('Url');
		
		$result = array();
		foreach ($options as $id => $linkName) 
		{
			$urlOptions[$mapKeyToUrlParam] = $id;
			$uri = $url->url($urlOptions, $routeName, $reset, $encode);
			$link = sprintf('<a href="%s">%s</a>', $uri, $linkName);

			$result[] = $link;
		}

		return implode($listsep, $result);
	}
}