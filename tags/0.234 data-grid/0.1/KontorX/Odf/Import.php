<?php
/**
 * @author gabriel
 *
 */
class KontorX_Odf_Import {

	/** Typy plików zapisywanych */
	const PICTURE = 'PICTURE';
	
	/** Obsługiwane typy ODF */
	const TYPE_ODT = 'ODT';

	/**
	 * @var array
	 */
	protected $_templateType = array(
		self::TYPE_ODT => 'xsl/odt.xsl'
	);
	
	/**
	 * @param string $file
	 * @return void
	 * @throws KontorX_Opf_Exception
	 */
	public function __construct($file) {
		if (!class_exists('ZipArchive')) {
			require_once 'KontorX/Odf/Exception.php';
			throw new KontorX_Odf_Exception('php extension "ZipArchive" do not exsists');
		}

		$zip = new ZipArchive();
		if (!$zip->open($file)) {
			require_once 'KontorX/Odf/Exception.php';
			throw new KontorX_Odf_Exception(sprintf('cannot open file "%s"', $file));
		}
		
		$type = pathinfo($file, PATHINFO_EXTENSION);
		$this->setType($type);
		
		if (false === ($stream = $zip->getStream('content.xml'))) {
			require_once 'KontorX/Odf/Exception.php';
			throw new KontorX_Odf_Exception('file "content.xml" do not exsists, probably not "ODF" file format');
		}

		// odczytaj xml do transformacji
		$stat = $zip->statName('content.xml');
		$content = $this->_readContent($stream, $stat);
		$this->_setContentXml($content);

	    // szukaj grafik
		for ($i = 0; $i < $zip->numFiles; $i++) {
			$stat = $zip->statIndex($i);
			// jest grafika
			if ((false !== strstr($stat['name'], 'Pictures/'))) {
				$stream = $zip->getStream($stat['name']);
				$content = $this->_readContent($stream, $stat);
				$this->_addFileContent(str_replace('Pictures/','',$stat['name']), $content, self::PICTURE);
			}
		}
	}
	
	/**
	 * @param string $type
	 * @return string
	 */
	public function getTemplatePath($type) {
		if (!isset($this->_templateType[$type])) {
			require_once 'KontorX/Odf/Exception.php';
			throw new KontorX_Odf_Exception(sprintf('import template type "%s" is not supported', $type));
		}
		
		return dirname(__FILE__) . DIRECTORY_SEPARATOR . $this->_templateType[$type];
	}
	
	/**
	 * @return string
	 */
	public function import() {
		$type = $this->getType();
		$template = $this->getTemplatePath($type);

		$xls = new DOMDocument();
		$xls->load($template);

		$xslt = new XSLTProcessor();
		$xslt->importStylesheet($xls);
		
		$contentXml = $this->getContentXml();
		$contentXml = $this->_prepareContentXml($contentXml);
		
		$xml = new DOMDocument();
		$xml->loadXML($contentXml);

		return html_entity_decode($xslt->transformToXML($xml));
	}

	/**
	 * @param string $xml
	 * @return string
	 */
	protected function _prepareContentXml($xml) {
		return preg_replace('#<draw:image xlink:href="Pictures/([a-z .A-Z_0-9]*)" (.*?)/>#es', "\$this->_makeImage('$1')", $xml);
	}

	/**
	 * @var string
	 */
	protected $_imagePath = null;
	
	/**
	 * @param string $path
	 * @return void
	 */
	public function setImagePath($path) {
		$this->_imagePath = (string) trim($path, DIRECTORY_SEPARATOR);
	}

	/**
	 * @return unknown_type
	 */
	public function getImagePath() {
		return $this->_imagePath;
	}

	/**
	 * @param string $img
	 * @return string
	 */
	protected function _makeImage($img) {
		return sprintf('&lt;img src="/%s/%s" border="0" /&gt;', $this->_imagePath, $img);
	}

	/**
	 * @var string
	 */
	protected $_type = null;
	
	/**
	 * @param string $type
	 * @return void
	 */
	public function setType($type) {
		$this->_type = strtoupper((string) $type);
	}

	/**
	 * @return string
	 */
	public function getType() {
		return $this->_type;
	}
	
	/**
	 * @var string
	 */
	protected $_contentXml = null;
	
	/**
	 * @param string $xml
	 * @return void
	 */
	protected function _setContentXml($xml) {
		$this->_contentXml = (string) $xml;
	}

	/**
	 * @return string
	 */
	public function getContentXml() {
		return $this->_contentXml;
	}
	
	/**
	 * @var array
	 */
	protected $_files = array(
		self::PICTURE => array()
	);
	
	/**
	 * @param string $name
	 * @param string $content
	 * @param string $type
	 * @return void
	 */
	protected function _addFileContent($name, $content, $type) {
		if (!isset($this->_files[$type])) {
			require_once 'KontorX/Odf/Exception.php';
			throw new KontorX_Odf_Exception(sprintf('file content type "%s" is not suported',$type));
		}
		$this->_files[$type][] = array($name, $content);
	}

	/**
	 * @param string $type
	 * @return array
	 */
	public function getFilesContent($type) {
		return isset($this->_files[$type]) ? $this->_files[$type] : null;
	}
	
	/**
	 * @param resource $stream
	 * @param array $stat
	 * @return string|false
	 */
	protected function _readContent($stream, array $stat) {
		if (!is_resource($stream)) {
			return false;
		}

		$contentData = null;
		while (!feof($stream)) {
	        $contentData .= fread($stream, $stat['size']);
	    }
	    return $contentData;
	}
}