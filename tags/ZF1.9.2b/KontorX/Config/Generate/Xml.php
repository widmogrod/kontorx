<?php
require_once 'KontorX/Config/Generate/Abstract.php';

/**
 * KontorX_Config_Generate_Xml
 * 
 * @category 	KontorX_Config
 * @package 	KontorX_Config_Generate
 * @license		GNU GPL
 * @author 		Gabriel gabriel@widmogrod.info
 * 
 * TODO dodac opcje
 *  - czy zapis z sekcjami
 *  - czy wprowadzic rozszerzenie [sekcja : rozszerzenie]
 */
class KontorX_Config_Generate_Xml extends KontorX_Config_Generate_Abstract {

	const NUMERIC_KEY_PREFIX = 'kontorx_numeric_key_';

	/**
	 * Zwraca wygenerowany string z array lub @see Zend_Config
	 *
	 * @return string
	 */
	public function generate() {
		$xml = new SimpleXMLElement('<kontorx-config/>');
		$this->_generate($this->getConfig(), $xml);

		// generowanie pliku
		$dom = dom_import_simplexml($xml)->ownerDocument;
        $dom->formatOutput = true;
        
		return $dom->saveXML();
	}

	/**
	 * @param array $array
	 * @param SimpleXMLElement $xml
	 * @return SimpleXMLElement
	 */
	private function _generate(array $array, SimpleXMLElement $xml){
		foreach ($array as $key => $value) {
			// Klucze numeryczne są niedozwolone
			if (is_numeric($key)) {
				$key = self::NUMERIC_KEY_PREFIX . $key;
			}
			
			// @todo Zastanowić się czy nie parsować klucza jako alfa-numerycznego?

			if (is_array($value)) {
				$child = $xml->addChild($key);
				$this->_generate($value, $child);
			} else {
				$xml->addChild($key, (string) $value);
			}
		}
	}
}