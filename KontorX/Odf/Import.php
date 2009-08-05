<?php
class KontorX_Odf_Import {

	public function __construct($fileName) {
		if (!is_readable($fileName) && !is_file($fileName)) {
			$message = "File `$fileName` do not exsists or is not readable";
			require_once 'KontorX/Opf/Exception.php';
			throw new KontorX_Opf_Exception($message);
		}

		// TODO Operacje gzipowania

		// odczytywanie plku
		$document = file_get_contents($fileName);
		
		// parsowanie dokumentu
		$dom = new DOMDocument();
		$dom->loadXML($document);

		$automaticStyles = $dom->getElementsByTagNameNS('urn:oasis:names:tc:opendocument:xmlns:office:1.0','automatic-styles');
		$office 		 = $dom->getElementsByTagNameNS('urn:oasis:names:tc:opendocument:xmlns:office:1.0','*');
		$section 		 = $dom->getElementsByTagNameNS('urn:oasis:names:tc:opendocument:xmlns:text:1.0', 'section');
		$name 		 	 = $dom->getElementsByTagNameNS('urn:oasis:names:tc:opendocument:xmlns:text:1.0', 'name');
		$text 		 	 = $dom->getElementsByTagNameNS('urn:oasis:names:tc:opendocument:xmlns:text:1.0', '*');
		$text_name	 	 = $dom->getElementsByTagName('text');
		
		
//		print '<pre>';
//		if (null !== $automaticStyles) {
//			$this->_debugNodeList($automaticStyles);
//		}
//		print '<hr/>';
//		if (null !== $office) {
//			$this->_debugNodeList($office);
//		}
//		print '<hr/>';
//		if (null !== $section) {
//			$this->_debugNodeList($section);
//		}
//		print '<hr/>';
//		if (null !== $name) {
//			$this->_debugNodeList($name);
//		}
//		print '<hr/>';
		if (null !== $text) {
			foreach ($text as $key => $node) {
				$this->_handleInlineNode($node, $key);
			}
//			$this->_debugNodeList($text);
		}
//		print '<hr/>';
//		if (null !== $text_name) {
//			$this->_debugNodeList($text_name);
//		}
//		print '<hr/>';
	}

	public function __toString() {
		var_dump(array_keys($this->_response));
		ksort($this->_response);
		return implode("\n", $this->_response);
	}
	
	protected function _debugNodeList(DOMNodeList $nodeList) {
		foreach ($nodeList as $node) {
			Zend_Debug::dump("$node->nodeValue : $node->localName : $node->prefix : $node->namespaceURI");
//			Zend_Debug::dump($node->nodeValue,'$node->nodeValue');
//			Zend_Debug::dump($node->nodeName,'$node->nodeName');
//			Zend_Debug::dump($node->nodeType,'$node->nodeType');
//			foreach ($node->attributes as $key => $val) {
//				Zend_Debug::dump("$key => $val->name : $val->value",'$node->attributes');
//			}
//			Zend_Debug::dump($node->ownerDocument,'$node->ownerDocument');
//			Zend_Debug::dump($node->namespaceURI,'$node->namespaceURI');
//			Zend_Debug::dump($node->prefix,'$node->prefix');
//			Zend_Debug::dump($node->localName,'$node->localName');
			
//			Zend_Debug::dump($node->baseURI,'$node->baseURI');
//			Zend_Debug::dump($node->textContent,'$node->textContent');
//			Zend_Debug::dump('---------------------');
		}
	}

	protected $_response = array();
	
	protected $_store = array();
	
	protected $_parentDepth = null;
	protected $_parent = null;
	
	protected function _handleInlineNode(DOMNode $node, $nodeDepth, DOMNode $parent = null, $parentDepth = null) {
		// powinno zostać spawdzone zagnieżdzenie
		if ($node->hasChildNodes()) {
//			$this->_parentDepth = $parentDepth;

			// dzięki temu jeżeli mamy do czynienia
			// z tekstem typu <b>to<u>jest</u> tekst</b>
			// $node->textContent bedzie zawieral to + jest + tekst
			// a nie to jest tekst
			if (null === $parent) {
				foreach ($node->childNodes as $key => $child) {
					$this->_handleInlineNode($child, $key, $node, $nodeDepth);
				}
			}
		} else {
			$this->_handleNode($node, $nodeDepth, $parent, $parentDepth);
		}
	}

	protected function _handleNode(DOMNode $node, $nodeDepth, DOMNode $parent = null, $parentDepth = null) {
		$key = "$nodeDepth:$parentDepth";
		if (null === $parent) {
			$content = $this->_handleNodeCommon($node, $node->textContent);
		} else {
			$content = null;

			$prevParent		 = $this->_parent;
			$prevParentDepth = $this->_parentDepth;
			
			$this->_parent		= $parent;
			$this->_parentDepth = $parentDepth;

			// ten sam poziom zagniezdzenia
			if ($parentDepth === $prevParentDepth) {
				if (!isset($this->_store[$parentDepth])) {
					$this->_store[$parentDepth] = array();
				}
				$this->_store[$parentDepth][] = $this->_handleNodeCommon($node,  $node->textContent);
			}
			// zagnieżdżenie się zmienia -> zamukamy tagi!
			else {
				if (!isset($this->_store[$prevParentDepth])) {
					$content = $this->_handleNodeCommon($parent, $node->textContent);
				} else
				if ($this->_store[$prevParentDepth] === null) {
					$content = $this->_handleNodeCommon($parent, $node->textContent);
				} else {
					$store = implode($this->_store[$prevParentDepth]);

					unset($this->_store[$prevParentDepth]);
					
					$content = $this->_handleNodeCommon($prevParent, $store);
				}
			}
		}
		$this->_response[] = $content;
	}

	protected function _handleNodeCommon(DOMNode $node, $content = null, $singleTag = null, &$tag = null) {
		switch ($node->localName) {
			case 'sequence-decls':
				$tag = 'ol';
				break;

			case 'sequence-decl':
				// display-outline-level
				$tag = 'li';
				break;

			case 'h':
				// style-name : Heading_20_1"
				$tag = 'h1';
				break;

			case 'p':
				// P1
				$tag = 'p';
				break;

			case 'span':
				// T1
				$tag = 'span';
				break;

			case 'list':
				$tag = 'ul';
				// L1
				break;

			case 'list-item':
				$tag = 'li';
				break;

			default:
				return $content;
		}

		return (true === $singleTag)
			? "<$tag>"
			: "<$tag>$content</$tag>";
	}
}