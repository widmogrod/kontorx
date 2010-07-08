<?php
require_once 'KontorX/Sisi/Action/Interface.php';

class Products 
{
	const GLOB_CATEGORIES = '/*/*';
	const GLOB_PRODUCTS = '/*.{jpg,JPG,png,PNG,gif,GIF,jpeg,JPEG}';

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
	 protected $_categories = null;

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
	 * @param string $pattern
	 * @return string
	 */
	protected function _getGlobPattern($pattern) {
		return $this->_productPathname . '/' . ltrim($pattern, '/');
	}

	/**
	 * Przygotuj relatywną ścieżke
	 * - np. dla linków do produktów
	 * @param string $absolutePath
	 * @return string
	 */
	protected function _relativePath($absolutePath) {
		$path = str_replace($this->_productPathname, '', $absolutePath);
		$path = ltrim($path, '/');
		return $path;
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
	 * Pobierz kategore produktu
	 * @return array
	 */
	public function getCategories() {
		if (null === $this->_categories) {
			$this->_categories = array();
			$config = $this->getConfig();
			$groups = $config['groups'];
			
			$pattern = $this->_getGlobPattern(self::GLOB_CATEGORIES);
			foreach(glob($pattern, GLOB_ONLYDIR) as $dir) {
				$name = basename($dir);
				$groupName = basename(dirname($dir));

				$this->_categories[$name] = array(
					'id'   => $name,
					'name' => $name,
					'path' => $dir,
					'group' => $groupName,
					'prefix' => @$groups[$groupName],
					'products' => $this->getProductsInfoFromPath($dir, $groupName)
				);
			}
		}

		return $this->_categories;
	}

	public function getCategory($name) {
		$categories = $this->getCategories();
		
		return (array_key_exists($name, $categories))
			? $categories[$name]
			: array();
	}

	public function getProductsInfoFromPath($path, $groupName) {
		$info = array();
		foreach(glob($path . self::GLOB_PRODUCTS, GLOB_BRACE) as $file) {
			$id = basename($file);
			
			// usunięcie rozszeżenia
			$name = explode('.', $id);
			array_pop($name);
			$name = implode('.', $name);
			
			$info[] = array(
				'id' => $id,
				'name' => $name,
				'path' => PRODUCTS_PATH . '/' . $this->_relativePath(dirname($file)) // sciezka do pliku
			);
		}
		return $info;
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
    		$response->setScriptName('asite');
    	}

		$products = new Products(PRODUCTS_PATHNAME);
		$categories = $products->getCategories();

    	$category = $sisi->getParam('category');
    	if (!strlen($category)) {
    		$category = current($categories);
    		$category = $category['id'];
    	}
    	
    	$category = $products->getCategory($category);
    	
    	$result = array(
    		'categories' => $categories,
    		'category' => $category
    	);

    	$response->setData($result);
    }
}
