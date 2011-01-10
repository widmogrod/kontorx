<?php
require_once 'KontorX/DataGrid/Renderer/ExtGrid/Abstract.php';

/**
 * @author gabriel
 *
 */
class KontorX_DataGrid_Renderer_ExtGrid_Array extends KontorX_DataGrid_Renderer_ExtGrid_Abstract 
{
	protected $_readerClass = 'KontorX_Ext_Data_Reader_Array';
	
	public function render() 
	{
		$renderToId = $this->getRenderToId();

		# przygotowanie nazw zmiennuch JS
		$varExtStore  = 'kx_dataGrid_ExtStore_'.$renderToId;
		$varGridPanel = 'kx_dataGrid_ExtGridPanel_'.$renderToId;
		$varJsonData = 'kx_dataGrid_Data_'.$renderToId;

		$grid = $this->getDataGrid();
		
		// setup pagination
		if ($grid->enabledPagination()) 
		{
			$this->_setupPegination($varStore);
		}

		list($columns, $fields) = $this->_getColumnsAndFields();
		
		$js = $this->getJavaScript();
		
		$js->callMethod('Ext.QuickTips.init();');

			$store = $this->getStore(); // Ext.data.Store

				/* @var $reader KontorX_Ext_Data_Reader_Array */
				$reader = $store->getReader(); // Ext.data.ArrayReader
				$reader->setId($this->getId());
				$reader->setFields($fields);
		
				$data = $grid->getAdapter()->fetchData();
				require_once 'Zend/Json.php';
				$data = Zend_Json::encode($data);

		require_once 'KontorX/JavaScript/Expresion.php';
		$js->addVar($varJsonData, new KontorX_JavaScript_Expresion($data)); // kx_dataGrid_Data_ = [{...},{...},...]

		$js->addVar($varExtStore, $store);
		$js->callMethod($varExtStore.'.loadData('.$varJsonData.');');
		
		$reader->setData(new KontorX_JavaScript_Expresion($varJsonData));
		
			$panel = $this->getGridPanel();
			$panel->setColumns($columns);
			$panel->setStore(new KontorX_JavaScript_Expresion($varExtStore));
			$panel->setRenderId($renderToId);

		$js->addVar($varGridPanel, $panel);

		if ($grid->enabledPagination()) {
			$js->callMethod($varExtStore.'.load({params:{start: 0, limit: '.$grid->getItemCountPerPage().'}});');
		} else {
			$js->callMethod($varExtStore.'.render();');
		}

		return $js->toJavaScript();
	}
}