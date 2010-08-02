<?php
/**
 * Dekorator dekoruje tylko elementy {@see KontorX_Form_Element_Link}
 * 
 * @version $Id$
 * @author $Author$
 */
class KontorX_Form_Decorator_Link extends Zend_Form_Decorator_Abstract {
	public function render($content)
	{
		/* @var $element KontorX_Form_Element_Link */
		$element = $this->getElement();
		if (!($element instanceof KontorX_Form_Element_Link)) {
			return $content;
		}

		$view = $element->getView();
		if (!($view instanceof Zend_View_Interface)) {
			return $content;
		}
		
		$name 		= $element->getLabel();
		$urlOptions = $element->getUrlOptions();
		$routeName  = $element->getRouteName();
		$reset		= $element->getReset();
		$encode		= $element->getEncode();

		/* @var $url Zend_View_Helper_Url */
		$url = $view->getHelper('Url');
		$uri = $url->url($urlOptions, $routeName, $reset, $encode);

		$markup = '<a href="%s">%s</a>';
		$markup = sprintf($markup, $uri, $name);
                  
		switch ($this->getPlacement()) {
			case self::PREPEND:
				return $markup . $this->getSeparator() . $content;

			case self::APPEND:
			default:
				return $content . $this->getSeparator() . $markup;
        }
	}
}