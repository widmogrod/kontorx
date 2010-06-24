<?php
require_once 'KontorX/DataGrid/Column/Abstract.php';

/**
 * This column type required jQuery (http://www.jquery.com/)
 * Requirements:
 * - JQuery
 * @author gabriel
 * 
 */
class KontorX_DataGrid_Column_ChechboxManager extends KontorX_DataGrid_Column_Abstract {
	
	public function render() {
		return '<input type="checkbox" onclick="jQuery(\'input:checkbox\').attr(\'checked\', this.checked);"/>';
	}
}