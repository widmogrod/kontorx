<?php
/**
 * Dekorator dekoruje tylko elementy {@see KontorX_Form_Element_NIP}
 * 
 * @version $Id$
 * @author $Author$
 */
class KontorX_Form_Decorator_NIP extends Zend_Form_Decorator_Abstract {
	public function render($content)
	{
		/* @var KontorX_Form_Element_NIP */
		$element = $this->getElement();
		if (!($element instanceof KontorX_Form_Element_NIP)) {
			return $content;
		}

		$view = $element->getView();
		if (!($view instanceof Zend_View_Interface)) {
			return $content;
		}
		
		$name = $element->getName();
		/* @var $formText Zend_View_Helper_FormText */
		$formText = $view->getHelper('FormText');
		$NIPParts = $element->getNIPParts();

		$longParams = array(
			'size'      => 3,
            'maxlength' => 3,
		);
		$shortParams = array(
			'size'      => 2,
            'maxlength' => 2,
		);

		$markup = $formText->formText($name . '[0]', $NIPParts[0], $longParams)   . ' - ' .
                  $formText->formText($name . '[1]', $NIPParts[1], $longParams) . ' - ' .
                  $formText->formText($name . '[2]', $NIPParts[2], $shortParams) . ' - ' .
                  $formText->formText($name . '[3]', $NIPParts[3], $shortParams);
                  
		switch ($this->getPlacement()) {
			case self::PREPEND:
				return $markup . $this->getSeparator() . $content;

			case self::APPEND:
			default:
				return $content . $this->getSeparator() . $markup;
        }
	}
}