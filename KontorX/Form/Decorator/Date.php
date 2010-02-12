<?php
/**
 * Dekorator dekoruje tylko elementy {@see KontorX_Form_Element_Date}
 * 
 * @version $Id$
 * @author $Author$
 */
class KontorX_Form_Decorator_Date extends Zend_Form_Decorator_Abstract {
	public function render($content) 
	{
		/* @var KontorX_Form_Element_Date */
		$element = $this->getElement();
		if (!($element instanceof KontorX_Form_Element_Date)) {
			return $content;
		}

		$view = $element->getView();
		if (!($view instanceof Zend_View_Interface)) {
			return $content;
		}
		
		$name = $element->getName();
		/* @var $formText Zend_View_Helper_FormText */
		$formText = $view->getHelper('FormText');
		$dateParts = $element->getDateParts();

		$longParams = array(
			'size'      => 4,
            'maxlength' => 4,
		);
		$shortParams = array(
			'size'      => 2,
            'maxlength' => 2,
		);

		// YYYY - MM - DD
		$markup = $formText->formText($name . '[0]', $dateParts[0], $longParams)   . ' - ' .
                  $formText->formText($name . '[1]', $dateParts[1], $shortParams) . ' - ' .
                  $formText->formText($name . '[2]', $dateParts[2], $shortParams);

        $markup = sprintf('<span class="kx_inline">%s</span>', $markup);
                  
		switch ($this->getPlacement()) {
			case self::PREPEND:
				return $markup . $this->getSeparator() . $content;

			case self::APPEND:
			default:
				return $content . $this->getSeparator() . $markup;
        }
	}
}