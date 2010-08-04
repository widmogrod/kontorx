<?php
/**
 * Przygotowywane są etykiety produktów do wyswietlenia
 * 
 * @author $Author$
 * @version $Id$
 */
class Promotor_View_Helper_ShopTags extends Zend_View_Helper_Abstract
{
	/**
	 * @var Shop_Model_ProductTag
	 */
	protected $_model;
	
	public function getModel() 
	{
		if (null === $this->_model) {
			$this->_model = new Shop_Model_ProductTag();
		}

		return $this->_model;
	}

	protected $_tags;
	
	public function getTags() 
	{
		if (null === $this->_tags)
			$this->_tags = $this->getModel()->findAllForTag();
			
		return $this->_tags;
	}
	
	/**
	 * Przygotowywane są etykiety produktów do wyswietlenia
	 * 
	 * @return Zend_Tag_Cloud
	 */
	public function shopTags() 
	{
		$tags = $this->getTags();

		$cloud = new Zend_Tag_Cloud();
		$cloud->setTags($tags);
		$cloud->setCloudDecorator('htmlCloud');
		$cloud->setTagDecorator('htmlTag');
		
		return $cloud;
	}

	/**
	 * @return Zend_Tag_Cloud
	 */
	public function render() {
		return $this->shopTags()->render();
	}

	/**
	 * @return string
	 */
	public function __toString() {
		$result = '';

		try {
			$result = $this->render();
		} catch (Zend_Tag_Exception $e) {
			trigger_error($e->getMessage(), E_USER_ERROR);
		}

		return (string) $result;
	}
}