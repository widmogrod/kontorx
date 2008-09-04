<?php
require_once 'KontorX/Config/Generate/Abstract.php';

/**
 * KontorX_Config_Generate_Php
 * 
 * @category 	KontorX_Config
 * @package 	KontorX_Config_Generate
 * @version 	0.1.1
 * @license		GNU GPL
 * @author 		Marcin `widmogror` Habryn, widmogrod@gmail.com
 */
class KontorX_Config_Generate_Php extends KontorX_Config_Generate_Abstract {

	/**
	 * Zwraca wygenerowany string z array lub @see Zend_Config
	 *
	 * @return string
	 */
	public function generate() {
		// generowanie pliku
		$result  = "<?php \n";
		$result .= "/* " . self::INFO . " */ \n";
		$result .= "return " . var_export($this->getConfig(), true) . ";";
		return $result;
	}
}