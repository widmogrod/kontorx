<?php
require_once 'KontorX/DataGrid/Renderer/ExtGrid/Abstract.php';

/**
 * @author gabriel
 *
 */
class KontorX_DataGrid_Renderer_ExtGrid_Array extends KontorX_DataGrid_Renderer_ExtGrid_Abstract {

	protected $_readerClass = 'KontorX_Ext_Data_Reader_Array';
	
	public function render($renderToId = null) {
		$varStore 	  = 'kx_dataGrid_ExtStore_'.$renderToId;
		$varGridPanel = 'kx_dataGrid_ExtGridPanel_'.$renderToId;

		$grid = $this->getDataGrid();
		
		// setup pagination
		if ($grid->enabledPagination()) {
			$this->_setupPegination($varStore);
		}
		
		list($columns, $fields) = $this->_getColumnsAndFields();
		
		$js = $this->getJavaScript();
		
		
		$js->callMethod('Ext.QuickTips.init();');

			$store = $this->getStore();

				/* @var $reader KontorX_Ext_Data_Reader_Array */
				$reader = $store->getReader();
				$reader->setId($this->getId());
				$reader->setFields($fields);

		
				$data = $grid->getAdapter()->fetchData();
				$data = Zend_Json::encode($data);

		$js->addVar('store', new KontorX_JavaScript_Expresion($data));
		
		$js->addVar($varStore, $store);
		$js->callMethod($varStore.'.loadData(store);');
		
		$reader->setData(new KontorX_JavaScript_Expresion('store'));
		
			$panel = $this->getGridPanel();
			$panel->setColumns($columns);
			$panel->setStore(new KontorX_JavaScript_Expresion($varStore));
			$panel->setRenderId($renderToId);

		$js->addVar($varGridPanel, $panel);

		if ($grid->enabledPagination()) {
			$js->callMethod($varStore.'.load({params:{start: 0, limit: '.$grid->getItemCountPerPage().'}});');
		} else {
			$js->callMethod($varStore.'.load();');
		}

		return $js->toJavaScript();
	}
}