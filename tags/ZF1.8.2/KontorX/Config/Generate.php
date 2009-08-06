<?php
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
class KontorX_Config_Generate {
	const INI = 'ini';
	const PHP = 'php';
	const XML = 'xml';
	
	/**
	 * Zwraca obiekt
	 *
	 * @param Zend_Config|array $config
	 * @param string $generatorName
	 * @param Zend_Config|array $options
	 * @return KontorX_Config_Generate_Abstract
	 */
	public static function factory($config, $generatorName, $options = null) {
		switch ($generatorName) {
			case self::INI:
			case self::PHP:
			case self::XML:
				$generatorName = ucfirst($generatorName);
				break;
			default:
				$message = "Generator factory `$generatorName` do not exsists";
				require_once 'KontorX/Config/Generate/Exception.php';
				throw new KontorX_Config_Generate_Exception($message);
		}

		$className = "KontorX_Config_Generate_$generatorName";

		require_once 'Zend/Loader.php';
		Zend_Loader::loadClass($className);

		$class = new $className($config,$options);
		return $class;
	}
}