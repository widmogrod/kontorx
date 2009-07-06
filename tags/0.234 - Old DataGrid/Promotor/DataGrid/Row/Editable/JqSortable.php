<?php
class KontorX_DataGrid_Row_Editable_JqSortable extends KontorX_DataGrid_Row_Editable_FormText {

	/**
	 * @todo sorting
	 */
	protected function _init() {
		$view = $this->getView();
		$view->inlineScript()->appendScript(
			sprintf('$(".%s").parents("tr").sortable({connectWith:"tr",axis:"y",fit: true,revert:true});', $this->_getClassAttr())
		);
	}
}