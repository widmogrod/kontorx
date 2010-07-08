<?php
require_once 'KontorX/Sisi/Action/Interface.php';

class Pages
{
	protected $_activePageId;
	
	protected $_pages;
	
	protected $_pagePathname;
	
	/**
	 * @param string $pagePathname
	 */
	public function __construct($pagePathname) {
		if (!is_dir($pagePathname))
			throw new Exception(sprintf('Katalog z stronami nie istnieje "%s"', $pagePathname));
		
		$this->_pagePathname = rtrim((string)$pagePathname,'/');
	}

	public function setActiveId($id) {
		$this->_activePageId = basename((string) $id);
	}
	
	public function getActiveId() {
		return $this->_activePageId;
	}
	
	public function getPage($pageId = null) {
		if (null !== $pageId)
			$pageId = $this->getActivePageId();

		$pageId = basename($pageId);

		$path = $this->_pagePathname . '/' . $pageId;
		if (!is_file($path)) {
			return;
		}

		ob_start();
		include $path;
		return ob_get_clean();
	}
}

class KontorX_Sisi_Action_Page implements KontorX_Sisi_Action_Interface
{
	/**
     * @param KontorX_Sisi $sisi
     * @return void
     */
    public function run(KontorX_Sisi $sisi) {
    	$response = $sisi->getResponse();
    	if ($response instanceof KontorX_Sisi_Response_Html) {
    		$response->setScriptName('index');
    	}

    	$pages = new Pages(PAGE_PATHNAME);
    	$pages->setActiveId($sisi->getParam('id','index'));
    	$page = $pages->getPage();

    	$result = array(
    		'page' => $page,
    	);

    	$response->setData($result);
    }
}
