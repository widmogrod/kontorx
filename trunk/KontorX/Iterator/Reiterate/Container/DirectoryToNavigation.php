<?php
require_once 'KontorX/Iterator/Reiterate/Container.php';

require_once 'Zend/Config.php';
require_once 'Zend/Navigation/Page.php';

/**
 * Create recursive ArrayObject for @see Zend_Navigation
 * implements data structure @see http://framework.zend.com/manual/en/zend.navigation.containers.html
 * 
 * @author gabriel
 */
class KontorX_Iterator_Reiterate_Container_DirectoryToNavigation
	extends Zend_Navigation_Page implements KontorX_Iterator_Reiterate_Container 
{

	/**
	 * @var string
	 */
	protected $_href;
	
	/**
	 * @var string
	 */
	protected $_label;
	
	/**
	 * @var string
	 */
	protected $_basePath;
	
	/**
	 * @var string
	 */
	protected $_baseUrl;
	
	/**
	 * @var string
	 */
	protected $_path;
	
	/**
	 * @var SplFileInfo
	 */
	protected $_fileInfo;

	/**
	 * @param KontorX_Iterator_Reiterate_Container $children
	 * @param integer $depth
	 */
	public function addChildren(KontorX_Iterator_Reiterate_Container $children, $depth) {
		
		if ($depth < 1) {
			// dodaj rekord główny
			if ($this->getParent())
				$this->getParent()->addPage($children);
			else
				$this->addPage($children);
		} else {
			// dodaj dziecko
			$this->addPage($children);
		}
	}

	/**
	 * Przygotowanie struktury plików dla
	 * @param $data SplFileInfo
	 * @return KontorX_Iterator_Reiterate_Container_DirectoryToNavigation
	 */
	public function getInstance($data = null) {
		$instance = new self();

		if ($data instanceof SplFileInfo) {
			$instance->setLabel($data->getFilename());
			$instance->setTitle($data->getFilename());
			$instance->setPath($data->getPathname());
			$instance->setSplFileInfo($data);
			
			// droben "dziedziczenie" atrybutów
			$instance->setBasePath($this->getBasePath());
			$instance->setBaseUrl($this->getBaseUrl());
		}
		
		return $instance;
	}
	
	/**
	 * @param string $basePath
	 */
	public function setBasePath($basePath) {
		$this->_basePath = (string) $basePath;
	}
	
	/**
	 * @return string
	 */
	public function getBasePath() {
		return $this->_basePath;
	}
	
	/**
	 * @return string
	 */
	public function setBaseUrl($baseUrl) {
		$this->_baseUrl = (string) $baseUrl;
	}
	
	/**
	 * @return string
	 */
	public function getBaseUrl() {
		return $this->_baseUrl;
	}

	/**
	 * @param string $path
	 */
	public function setPath($path) {
		$this->_path = (string)$path;
	}
	
	/**
	 * @param SplFileInfo $info
	 */
	public function setSplFileInfo(SplFileInfo $info) {
		$this->_fileInfo = $info;
	}
	
	public function getHref() {
		// tylko pliki moga być linkowane
		if ($this->_fileInfo->isDir())
			return;

		// przygotowywanie ściezli od pliku
		return $this->_baseUrl . '/' . 
				ltrim(str_replace($this->_basePath, '', $this->_path), '/');
	}
}