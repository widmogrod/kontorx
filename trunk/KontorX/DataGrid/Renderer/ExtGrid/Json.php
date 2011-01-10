<?php
require_once 'KontorX/DataGrid/Renderer/ExtGrid/Abstract.php';

require_once 'KontorX/JavaScript/Expresion.php';

/**
 * @author gabriel
 *
 */
class KontorX_DataGrid_Renderer_ExtGrid_Json extends KontorX_DataGrid_Renderer_ExtGrid_Abstract 
{
	protected $_readerClass = 'KontorX_Ext_Data_Reader_Json';
	
	public function render($renderToId = null) 
	{
		$renderToId = $this->getRenderToId();
		
		# przygotowanie nazw zmiennuch JS
		$varExtStore  = 'kx_dataGrid_ExtStore_'.$renderToId;
		$varGridPanel = 'kx_dataGrid_ExtGridPanel_'.$renderToId;
		$varJsonData = 'kx_dataGrid_Data_'.$renderToId;

		$grid = $this->getDataGrid();

		// setup pagination
		if ($grid->enabledPagination()) {
			$this->_setupPegination($varExtStore);
		}
		
		list($columns, $fields) = $this->_getColumnsAndFields();

		$js = $this->getJavaScript();

			$data = $grid->getAdapter()->fetchData();
		
			$store = $this->getStore();
			$store->setUrl($this->_url);

				/* @var $reader KontorX_Ext_Data_Reader_Json */
				$reader = $store->getReader();
				$reader->setId($this->getId());
				$reader->setFields($fields);

		$js->addVar($varExtStore, $store);
		
			$panel = $this->getGridPanel();
			$panel->setColumns($columns);
			$panel->setStore(new KontorX_JavaScript_Expresion($varExtStore));
			$panel->setRenderId($renderToId);

		$js->addVar($varGridPanel, $panel);

		if ($grid->enabledPagination()) {
			$js->callMethod($varExtStore.'.load({params:{start: 0, limit: '.$grid->getItemCountPerPage().'}});');
		} else {
			$js->callMethod($varExtStore.'.load();');
		}

		return $js->toJavaScript();
	}
	
	/**
	 * @var string
	 */
	protected $_url;

	/**
	 * @param string $url
	 * @return KontorX_Ext_Data_Store
	 */
	public function setUrl($url) {
		$this->_url = (string) $url;
		return $this;
	}
}