<?php
/**
 * Dekorator dekoruje tylko elementy {@see KontorX_Form_Element_Payments}
 * 
 * @version $Id$
 * @author $Author$
 */
class KontorX_Form_Decorator_Payments extends Zend_Form_Decorator_Abstract {
	public function render($content)
	{
		/* @var $element KontorX_Form_Element_Payments */
		$element = $this->getElement();
		if (!($element instanceof KontorX_Form_Element_Payments)) {
			return $content;
		}

		$view = $element->getView();
		if (!($view instanceof Zend_View_Interface)) {
			return $content;
		}

		if (null === ($paymentTypes = $element->getPaymentsTypes())) {
			return $content;
		}

		$result  = '<h1 class="kx_title">'.$element->getLabel().'</h1>';
		$result .= '<div class="kx_payment_types">';
		foreach ($paymentTypes as $options)
		{
			$id  = 'paytype-' . $options->type;
			$attribs = array(
				'id' => $id
			);

			$result .= '<label class="kx_item">';
			$result .= sprintf('<input type="radio" name="%s" value="%s"/>', $element->getName(), $options->type);
			$result .= sprintf('<img src="%s" alt="%s"/>', $options->img, $options->name);
			$result .= '<span class="kx_name">' . $options->name . '</span>';
			$result .= '</label>';
		}

		$result .= '</div>';
		
		switch ($this->getPlacement()) {
			case self::PREPEND:
				return $result . $this->getSeparator() . $content;

			case self::APPEND:
			default:
				return $content . $this->getSeparator() . $result;
        }
	}
}