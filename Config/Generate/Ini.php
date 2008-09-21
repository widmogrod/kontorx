<?php
require_once 'KontorX/Config/Generate/Abstract.php';

/**
 * KontorX_Config_Generate_Ini
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
class KontorX_Config_Generate_Ini extends KontorX_Config_Generate_Abstract {

	protected $_separator = '.';

	/**
	 * Zwraca wygenerowany string z array lub @see Zend_Config
	 *
	 * @return string
	 */
	public function generate() {
		// generowanie pliku
		$result  = ";" . self::INFO . "\n";
		$result .= $this->_generate($this->getConfig());
		return $result;
	}

	/**
	 * Start generowania ini-string - tworzenie sekcji
	 * 
	 * @param array $array
	 * @return unknown
	 */
	private function _generate(array $array){
		$result = null;
		foreach ($array as $sectionName => $sectionData) {
			$result .= "[$sectionName]";
			$result .= "\n\t";
			// TODO a co jezeli $sectionData nie jest array ?!
			$result .= $this->_generateHelper_1((array) $sectionData);
			$result .= "\n";
		}
		return $result;
	}

	/**
	 * Pomocnik generowania array to ini-string
	 *
	 * @param array $array
	 * @return string
	 */
	private function _generateHelper_1(array $array){
		$result = null;
		foreach ($array as $key_1 => $value_1){
			if (is_array($value_1)) {
				foreach ($value_1 as $key_2 => $value_2){
					$baseKey = $key_1 . $this->_separator . $key_2;
					$result .= $this->_generateHelper_2($value_2, $baseKey);
					$result .= "\n\t";
				}
			} else {
				$result .= "$key_1 = \"$value_1\"";
				$result .= "\n\t";
			}
		}
		return $result;
	}

	/**
	 * Pomocnik generowania array to ini-string
	 *
	 * @param mixed $value
	 * @param string $baseKey
	 * @return string
	 */
	private function _generateHelper_2($value, $baseKey){
		$result = null;
		if (is_array($value)) {
			foreach ($value as $key => $val){
				$currentBaseKey = $baseKey . $this->_separator . $key;
				$result .= $this->_generateHelper_2($val, $currentBaseKey);
			}
		} else {
			$result .= "$baseKey = \"$value\"";
			$result .= "\n\t";
		}
		return $result;
	}
}