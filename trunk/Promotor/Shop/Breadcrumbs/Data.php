<?php
class Promotor_Shop_Breadcrumbs_Data 
{
	/**
	 * @var Zend_Navigation_Container
	 */
	protected $_breadcrumbs;
	
	/**
	 * @param Zend_Navigation_Container $breadcrumbs
	 */
	public function setBreadcrumbs(Zend_Navigation_Container $breadcrumbs)
	{
		$this->_breadcrumbs = $breadcrumbs;
	}
	
	/**
	 * @return Zend_Navigation_Container
	 */
	public function getBreadcrumbs()
	{
		return $this->_breadcrumbs;
	}
	
	/**
	 * @var Zend_Navigation_Container
	 */
	protected $_suggestion;
	
	/**
	 * @param Zend_Navigation_Container $suggestion
	 */
	public function setSuggestion(Zend_Navigation_Container $suggestion)
	{
		$this->_suggestion = $suggestion;
	}
	
	/**
	 * @return Zend_Navigation_Container
	 */
	public function getSuggestion()
	{
		return $this->_suggestion;
	}
}