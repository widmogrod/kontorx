<?php
require_once 'KontorX/DataGrid/Renderer/ExtGrid/Abstract.php';

/**
 * @author gabriel
 *
 */
class KontorX_DataGrid_Renderer_ExtGrid_Array extends KontorX_DataGrid_Renderer_ExtGrid_Abstract {

	protected $_readerClass = 'KontorX_Ext_Data_Reader_Array';
	
	public function render($renderToId = null) {
		$grid = $this->getDataGrid();
		
		list($columns, $fields) = $this->_getColumnsAndFields();
		
		require_once 'KontorX/JavaScript.php';
		$js = new KontorX_JavaScript();

			$store = $this->getStore();

				/* @var $reader KontorX_Ext_Data_Reader_Array */
				$reader = $store->getReader();
				$reader->setId($this->getId());
				$reader->setFields($fields);

		$varStore = 'kx_dataGrid_ExtStore_'.$renderToId;
		$js->addVar($varStore, $store);

				$data = $grid->getAdapter()->fetchData();
				$data = Zend_Json::encode($data);

		$js->addVar('store', new KontorX_JavaScript_Expresion($data));
		$js->callMethod($varStore.'.loadData(store);');

			$panel = $this->getGridPanel();
			$panel->setColumns($columns);
			$panel->setStore(new KontorX_JavaScript_Expresion($varStore));
			$panel->setRenderId($renderToId);

		$varGridPanel = 'kx_dataGrid_ExtGridPanel_'.$renderToId;
		$js->addVar($varGridPanel, $panel);
		$js->callMethod($varStore.'.load();');

		return $js->toJavaScript();
	}
}