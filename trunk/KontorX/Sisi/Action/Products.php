<?php
require_once 'KontorX/Sisi/Action/Interface.php';

class Prducts 
{
	const GLOB_TYPES = '/*/';

	/**
	 * @var string
	 */
	protected $_productPathname = null;
	
	/**
	 * @var array
	 */
	protected $_config = null;
	 
	 /**
	  * @var array
	  */
	 protected $_types = null;

	/**
	 * 
	 */
	public function __construct($productPathname) {
		if (!is_dir($productPathname))
			throw new Exception(sprintf('Katalog z produktami nie istnieje "%s"', $productPathname));
		
		$this->_productPathname = rtrim((string)$productPathname,'/');
	}

	/**
	 * Przygotuj wzorzec dla GLOB
	 * @return array
	 */
	protected function _getGlobPattern($pattern) {
		return $this->_productPathname . '/' . ltrim($pattern, '/');
	}
	
	/**
	 * Pobierz konfigurację produktów
	 * @return array
	 */
	public function getConfig() {
		if (null === $this->_config) {
			$configFile = $this->_productPathname . '/config.php';
			if (!is_file($configFile))
				throw new Exception('Katalog konfiguracyjny produktów nie istnieje');

			$this->_config = include $configFile;

			if (!is_array($this->_config))
				throw new Exception('Nieprawidłowa klnfiguracja produktu!');
		}
		
		return $this->_config;
	}

	/**
	 * Pobierz typy produktu
	 * @return array
	 */
	public function getTypes() {
		if (null === $this->_types) {
			$this->_types = array();
			
			$pattern = $this->_getGlobPattern(self::GLOB_TYPES);
			foreach(glob($pattern, ) as $dir) {
				$this->_types[] = array(
					'id'   => $dir,
					'name' => basename($dir),
					'path' => $dir
				)
			}
		}

		return $this->_types;
	}
}

class KontorX_Sisi_Action_Products implements KontorX_Sisi_Action_Interface
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

    	$product = $sisi->getParam('product');
    	
    	if (strlen($product) > 2) {
    		// wyświetl produkt
    	} else {
    		// wyświetl listę produktów
    	}
    }
}
