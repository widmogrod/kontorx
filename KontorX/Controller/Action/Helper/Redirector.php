<?php
/**
 * Bardzo przyjemny dodatek, który przekierowuje akcję
 * gdy będzie (when) lub nie będzie (until) spełniony warunek
 * 
 * @author $Author$
 * @version $Id$
 */
class KontorX_Controller_Action_Helper_Redirector extends Zend_Controller_Action_Helper_Redirector
{
	public function gotoReferer(array $options = array())
	{
		if (isset($_SERVER['HTTP_REFERER']))
		{
			$url = $_SERVER['HTTP_REFERER'];
		} else {
			$url = '/';
		}
		
		$this->gotoUrl($url, $options);
	}
	
	public function gotoRefererUntil($condition, array $options = array())
	{
		$this->gotoRefererWhen(!$condition, $options);
	}
	
	public function gotoRefererWhen($condition, array $options = array())
	{
		if ($condition) {
			$this->gotoReferer($options);
		}
	}
	
	public function gotoUrlUntil($condition, $url, array $options = array())
	{
		$this->gotoUrlWhen($condition, $url, $options);
	}
	
	public function gotoUrlWhen($condition, $url, array $options = array())
	{
		if ($condition) {
			$this->gotoUrl($url, $options);
		}
	}
	
	public function gotoRouteUntil($condition, array $urlOptions = array(), $name = null, $reset = false, $encode = true)
	{
		$this->gotoRouterWhen(!$condition, $urlOptions, $name, $reset, $encode);
	}
	
	public function gotoRouteWhen($condition, array $urlOptions = array(), $name = null, $reset = false, $encode = true)
	{
		if ($condition) {
			$this->gotoRoute($urlOptions, $name, $reset, $encode);
		}
	}
	
	public function gotoSimpleUntil($condition, $action, $controller = null, $module = null, array $params = array())
	{
		$this->gotoSimpleWhen(!$condition, $action, $controller, $module, $params);
	}
	
	public function gotoSimpleWhen($condition, $action, $controller = null, $module = null, array $params = array())
	{
		if ($condition) {
			$this->gotoSimple($action, $controller, $module, $params);
		}
	}
}