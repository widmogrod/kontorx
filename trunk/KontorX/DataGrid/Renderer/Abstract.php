<?php
require_once 'KontorX/DataGrid/Renderer/Interface.php';

/**
 * @author gabriel
 *
 */
abstract class KontorX_DataGrid_Renderer_Abstract implements KontorX_DataGrid_Renderer_Interface {

	/**
	 * @var KontorX_DataGrid
	 */
	protected $_dataGrid;
	
	/**
	 * @param KontorX_DataGrid $dataGrid
	 * @return KontorX_DataGrid_Renderer_Abstract
	 */
	public function setDataGrid(KontorX_DataGrid $dataGrid) {
		$this->_dataGrid = $dataGrid;
		return $this;
	}

	/**
	 * @return KontorX_DataGrid
	 */
	public function getDataGrid() {
		return $this->_dataGrid;
	}
	
	/**
	 * @return string
	 */
	public function __toString() {
		try {
			return $this->render();
		} catch (Exception $e) {
			$message = get_class($e) . ' :: ' . $e->getMessage();
			trigger_error($message, E_USER_WARNING);
		}
		return '';
	}
}