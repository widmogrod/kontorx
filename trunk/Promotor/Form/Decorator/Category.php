<?php
class Promotor_Form_Decorator_Category extends Zend_Form_Decorator_Abstract
{
	public function render($content)
	{
		/* @var $element Promotor_Form_Element_Category */
		$element = $this->getElement();
		if (!($element instanceof Promotor_Form_Element_Category)) 
		{
			return $content;
		}

		$view = $element->getView();
		if (!($view instanceof Zend_View_Interface)) 
		{
			return $content;
		}
		
		$name 			 = $element->getName();
		$mainCategory 	 = $element->getMainCategory();
		$chosenCategories = (array) $element->getChosenCategories();
		
		$prefix = $name;
		if (null !== ($belongsTo = $element->getBelongsTo())) {
			$prefix = $belongsTo.'['.$name.']';
		}

		$checkbox = '<input type="checkbox" name="'.$prefix.'['.Promotor_Form_Element_Category::CHOSEN_CATEGORY_NS.'][]" value="%s" %s/>';
		$radio    = '<input type="radio"    name="'.$prefix.'['.Promotor_Form_Element_Category::MAIN_CATEGORY_NS.']" value="%s" %s />';

		/* @var $rowset KontorX_Db_Table_Tree_Rowset_Abstract */
		$rowset = $element->getCategoryRowset();

		$iterator = new RecursiveIteratorIterator($rowset, RecursiveIteratorIterator::SELF_FIRST);
		$iterator->rewind();

		$result = '<div class="tree" id="form_element_category">';
		while ($iterator->valid()) 
		{
			$depth = $iterator->getDepth();

			/* @var $row KontorX_Db_Table_Tree_Row_Abstract */
			$row = $iterator->current();

			// sprawdź czy rekord główny jest zaznaczony 
			$checkedRadio = ($row->id == $mainCategory)
				? 'checked="checked"' : '';

			// sprawdź czy rekord "wolny dzielnicy jest zaznaczony"
			$checkedCheckbox = (in_array($row->id, $chosenCategories))
				? 'checked="checked"' : '';

			// buduj linie
			$line  = '<div>';
			$line .= str_repeat('<span>&nbsp;</span>', $depth*10);
			$line .= sprintf($radio,    $row->id, $checkedRadio);
			$line .= sprintf($checkbox, $row->id, $checkedCheckbox);
			$line .= sprintf('<span>%s</span>', $row->name);
			$line .= '</div>';

			$result .= $line;

			$iterator->next();
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