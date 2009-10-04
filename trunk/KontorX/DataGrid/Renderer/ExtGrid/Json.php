<?php
require_once 'KontorX/DataGrid/Renderer/ExtGrid/Abstract.php';

/**
 * @author gabriel
 *
 */
class KontorX_DataGrid_Renderer_ExtGrid_Json extends KontorX_DataGrid_Renderer_ExtGrid_Abstract {

	protected $_readerClass = 'KontorX_Ext_Data_Reader_Json';
	
	public function render($renderToId = null) {
		$grid = $this->getDataGrid();

		list($columns, $fields) = $this->_getColumnsAndFields();

		require_once 'KontorX/JavaScript.php';
		$js = new KontorX_JavaScript();

			$data = $grid->getAdapter()->fetchData();
		
			$store = $this->getStore();
			$store->setUrl($this->_url);

				/* @var $reader KontorX_Ext_Data_Reader_Json */
				$reader = $store->getReader();
				$reader->setId($this->getId());
				$reader->setFields($fields);

		$varStore = 'kx_dataGrid_ExtStore_'.$renderToId;
		$js->addVar($varStore, $store);
		
			$panel = $this->getGridPanel();
			$panel->setColumns($columns);
			$panel->setStore(new KontorX_JavaScript_Expresion($varStore));
			$panel->setRenderId($renderToId);

		$varGridPanel = 'kx_dataGrid_ExtGridPanel_'.$renderToId;
		$js->addVar($varGridPanel, $panel);

		$js->callMethod($varStore.'.load();');

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