<?php
class KontorX_Form_Decorator_DataGrid extends Zend_Form_Decorator_Abstract
{
	public function render($content)
	{
		/* @var $element KontorX_Form_Element_DataGrid */
		$element = $this->getElement();
		if (!($element instanceof KontorX_Form_Element_DataGrid)) {
			return $content;
		}

		$view = $element->getView();
		if (!($view instanceof Zend_View_Interface)) {
			return $content;
		}

		$dataGrid = $element->getDataGrid();
		$render = $dataGrid->getRenderer()->render();
                  
		switch ($this->getPlacement())
		{
			case self::PREPEND:
				return $render . $this->getSeparator() . $content;

			case self::APPEND:
			default:
				return $content . $this->getSeparator() . $render;
        }
	}
}