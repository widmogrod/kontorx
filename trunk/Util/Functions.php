<?php
/**
 * Enter description here...
 * 
 */
class KontorX_Util_Functions {

	/**
	 * Zwraca IP uzytkownika
	 *
	 * @return string
	 */
	public static function getIP() {
		$ip = getenv('HTTP_X_FORWARDED_FOR');
		return !$ip	? getenv('REMOTE_ADDR')	: $ip;
	}

	public static function getBrowser() {
		$userAgent = getenv('HTTP_USER_AGENT');
		switch (true) {
			case strpos($userAgent, 'Netscape'): 	return 'Netscape';
			case strpos($userAgent, 'Firefox'): 	return 'Firefox';
			case strpos($userAgent, 'Gecko'): 		return 'Gecko';
			case strpos($userAgent, 'MSIE'): 		return 'Internet Explorer';
			case strpos($userAgent, 'Opera'): 		return 'Opera';
			default:
				return 'Others browsers';
		}
	}
}