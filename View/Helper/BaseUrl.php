<?php
/**
 * Helper widoku, umożliwia generowanie strokotry drzewiastej,
 * wykorzystywany jest dla "ExtJs"
 * 
 * @category 	KontorX
 * @package 	KontorX_View_Helper
 * @version 	0.1.2
 * @license		GNU GPL
 * @author 		Marcin `widmogror` Habryn, widmogrod@gmail.com
 * 
 * @todo		Dodać opisy
 */
class KontorX_View_Helper_BaseUrl {
	protected static $_url = array(); 

	/**
	 * Enter description here...
	 *
	 * @param boolean $scriptName
	 * @return string
	 */
	public static function baseUrl($scriptName = false){
		// TODO Dodac rozpoznawanie czy https
		switch($scriptName){
			case true:
				$phpSelf = getenv('PHP_SELF');
				$scriptName = ($phpSelf === false OR $phpSelf == '' OR $phpSelf == '/' )
					? getenv('SCRIPT_NAME')
					: $phpSelf;
				break;

			case false:
				$scriptName = dirname(getenv('SCRIPT_NAME'));
				$iScriptName = strlen($scriptName);
				$scriptName = $scriptName{$iScriptName-1} == '/'
					? substr($scriptName,0,-1)
					: $scriptName;
				$scriptName .= '/';
				break;
		}

		return 'http://' . getenv('SERVER_NAME') . $scriptName;
	}

	/**
	 * Enter description here...
	 *
	 * @param bool $scriptName
	 * @return string
	 */
	public static function getUrl($scriptName = false) {
		if (array_key_exists((int) $scriptName, self::$_url)) {
			return self::$_url[(int) $scriptName];
		}

		switch($scriptName){
			case true:
				$url = ($scriptName = getenv('SCRIPT_NAME')) === false ? getenv('REQUEST_URI') : $scriptName;
				break;
			case false:
				if (($pathInfo = getenv('PATH_INFO')) !== false){
					$url = $pathInfo;
				} else {
					$requestUri = getenv('REQUEST_URI');
					$scriptName = getenv('SCRIPT_NAME');
					$dirnameScriptName = dirname($scriptName);

					$url = ( $scriptName == '/' OR $dirnameScriptName == '/')
						? $requestUri
						: str_replace(array($scriptName, $dirnameScriptName),'',$requestUri);
				}
				
				$filename = basename(getenv('SCRIPT_FILENAME'));
				$url = str_replace($filename,'',$url);
				
				if(($scriptName = getenv('QUERY_STRING')) != '') {
					$url .= '?'.$scriptName;
				}
				break;
		}

		return self::$_url[(int) $scriptName] = urldecode($url);
	}

	public static function getDomain() {
		return 'http://' . getenv('SERVER_NAME') . '/';
	}
	
	/**
	 * Enter description here...
	 *
	 * @param string $string
	 * @param bool $scriptName
	 * @return bool
	 */
	public static function inUrl($string, $true = null, $false = null, $scriptName = false, array $noIn = array()) {
		$url = self::getUrl($scriptName);
		$inUrl = strstr($url, $string);

		$return = $inUrl === false
			? (null === $false ? false : $false)
			: (null === $true ? true : $true);

		if (!$inUrl) {
			return $return;
		}

		foreach ($noIn as $value) {
			if(strstr($url, $value) !== false) {
				return null === $false ? false : $false;
			}
		}
		return $return;
	}

	/**
	 * Enter description here...
	 *
	 * @return string
	 */
	public static function getActivePageUrl() {
		return self::getDomain() . self::getUrl();
	}
}
?>