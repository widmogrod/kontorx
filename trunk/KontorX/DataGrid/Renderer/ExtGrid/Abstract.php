<?php
require_once 'KontorX/DataGrid/Renderer/Abstract.php';

/**
 * @author gabriel
 *
 */
abstract class KontorX_DataGrid_Renderer_ExtGrid_Abstract 
		extends KontorX_DataGrid_Renderer_Abstract {

	/**
	 * @param Zend_Config|array $options
	 * @return void
	 */
	public function __construct($options = array()) {
		if ($options instanceof Zend_Config) {
			$this->setOptions($options->toArray());
		} elseif (is_array($options)) {
			$this->setOptions($options);
		}
	}
	
	/**
	 * @param array $options
	 * @return KontorX_DataGrid_Renderer_ExtGrid
	 */
	public function setOptions(array $options) {
		foreach ($options as $key => $value) {
			$method = 'set' . ucfirst($key);
			if (method_exists($this, $method)) {
				$this->$method($value);
			}
		}
		return $this;
	}
	
	/**
	 * @var string
	 */
	protected $_id = 'id';

	/**
	 * @param string $id
	 * @return KontorX_DataGrid_Renderer_ExtGrid_Abstract
	 */
	public function setId($id) {
		$this->_id = (string) $id;
		return $this;
	}
	
	/**
	 * @return string
	 */
	public function getId() {
		return $this->_id;
	}
	
	/**
	 * @var KontorX_Ext_Grid_GridPanel
	 */
	protected $_gridPanel;
	
	/**
	 * @param KontorX_Ext_Grid_GridPanel $gridPanel
	 * @return KontorX_DataGrid_Renderer_ExtGrid_Abstract
	 */
	public function setGridPanel(KontorX_Ext_Grid_GridPanel $gridPanel) {
		$this->_gridPanel = $gridPanel;
		return $this;
	}

	/**
	 * @return KontorX_Ext_Grid_GridPanel
	 */
	public function getGridPanel() {
		if (null === $this->_gridPanel) {
			require_once 'KontorX/Ext/Grid/GridPanel.php';
			$this->_gridPanel = new KontorX_Ext_Grid_GridPanel();
		}
		return $this->_gridPanel;
	}

	/**
	 * @var string {@see KontorX_Ext_Data_Reader_Interface}
	 */
	protected $_readerClass;
	
	/**
	 * @var KontorX_Ext_Data_Store
	 */
	protected $_store;
	
	/**
	 * @param KontorX_Ext_Data_Store_Interface $store
	 * @return KontorX_DataGrid_Renderer_ExtGrid_Abstract
	 */
	public function setStore(KontorX_Ext_Data_Store_Interface $store) {
		$this->_store = $store;
	}

	/**
	 * @return KontorX_Ext_Data_Store_Interface
	 */
	public function getStore() {
		if (null === $this->_store) {
			require_once 'KontorX/Ext/Data/Store.php';
			$this->_store = new KontorX_Ext_Data_Store();
			$this->_store->setReader($this->_readerClass);
		}
		return $this->_store;
	}
	
	/**
	 * @return array array($columns, $fileds)
	 */
	public function _getColumnsAndFields() {
		$grid = $this->getDataGrid();
		
		$readerFields = array();
		$panelColumns = array();

		$i = -1;
		$columns = $grid->getColumns();
		foreach ($columns as $column) {
			/* @var $column KontorX_DataGrid_Column_Interface */
			$columnName = $column->getColumnName();
			$panelColumns[++$i] = array(
				'header' => isset($column->name) ? $column->name : $columnName,
				'sortable' => ($column instanceof KontorX_DataGrid_Column_Order)
			);
			$readerFields[$i] = array(
				'name' => isset($column->name) ? $column->name : $columnName,
				'mapping' => $columnName
			);
			
			$cell = $column->getCell();
			$type = $cell->getClassName();
			$type = strtolower($type);

			switch($type) {
				case 'float':
					$readerFields[$i]['type'] = 'float';
					break;
				case 'yesno':
					$readerFields[$i]['type'] = 'boolean';
					break;

				case 'date':
					/* @var $cell KontorX_DataGrid_Cell_Date */
					$readerFields[$i]['type'] = 'date';
					$readerFields[$i]['dateFormat'] = 'y-m-d';
					break;
			}
		}

		return array($panelColumns, $readerFields);
	}
}