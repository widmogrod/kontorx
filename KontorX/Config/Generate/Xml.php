<?php
require_once 'KontorX/Config/Generate/Abstract.php';

/**
 * KontorX_Config_Generate_Xml
 * 
 * @category 	KontorX_Config
 * @package 	KontorX_Config_Generate
 * @version 	0.1.2
 * @license		GNU GPL
 * @author 		Marcin `widmogror` Habryn, widmogrod@gmail.com
 * 
 * TODO dodac opcje
 *  - czy zapis z sekcjami
 *  - czy wprowadzic rozszerzenie [sekcja : rozszerzenie]
 * TODO optymalizacja klasa jest za wolna!!
 */
class KontorX_Config_Generate_Xml extends KontorX_Config_Generate_Abstract {

	protected $_rootnode = 'configdata';

	/**
	 * Zwraca wygenerowany string z array lub @see Zend_Config
	 *
	 * @return string
	 */
	public function generate() {
		$rootnode = "<$this->_rootnode></$this->_rootnode>";
		$xml = $this->_generate($this->getConfig(), new SimpleXMLElement($rootnode));

		// generowanie pliku
		$result  = "<!--" . self::INFO . "-->";
		$result .= $xml->asXML();
		return $result;
	}

	/**
	 * Enter description here...
	 * 
	 * //TODO Zeby dzialalo!
	 * 
	 * @param array $array
	 * @param SimpleXMLElement $xml
	 * @return SimpleXMLElement
	 */
	private function _generate(array $array, SimpleXMLElement $xml){
		foreach ($array as $key_1 => $val_1) {
			// zapis atrybutow
			if (isset($val_1['@attributes']) && is_array($val_1['@attributes'])) {
				$xmlNew = $xml->addChild($key_1);
				foreach ($val_1['@attributes'] as $key_2 => $val_2) {
					$xmlNew->addAttribute($key_2, $val_2);
				}
			} else
			if (is_array($val_1)) {
				$xml = $this->_generate($val_1, $xml);
			} else {
				$xml->addChild($key_1, $val_1);
			}
		}
		return $xml;
	}
}