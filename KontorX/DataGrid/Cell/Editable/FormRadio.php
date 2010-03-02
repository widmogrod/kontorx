<?php
require_once 'KontorX/DataGrid/Cell/Editable/Abstract.php';
class KontorX_DataGrid_Cell_Editable_FormRadio extends KontorX_DataGrid_Cell_Editable_Abstract {
	
	public function render() {
		// Tworzenie prefisku, klucza głównego
		$primaryVal = $this->getPrimaryKey();
		$primaryKey = implode(self::SEPARATOR, array_intersect_key($this->getData(), array_flip($primaryVal)));

		$columnName = $this->getColumnName();
		$prefix = $this->getPrefix();
		$valueName = $this->getAttrib('valueName', $columnName);

		$name = sprintf('%s[%s]', $prefix, $columnName);
		$value = $primaryKey;

		// radio jest rozpatrywana na zasadzie bool
		$checked = '';
		if ($this->getValue())
		{
			$checked = 'checked="checked"';			
		}
		
		// ustawienie attrybutu 'class' dla elementu widoku.
		$class = (string) $this->getAttrib('class');
		$class = $this->_prepareClassAttr($class);

		return sprintf('<input type="radio" name="%s" value="%d" class="%s" %s/>', $name, $value, $class, $checked);
	}
}