<?php
/**
 * Przydatne funkcje
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

	/**
	 * Zwraca nazwę przeglądarki
	 *
	 * @return string
	 */
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

	/**
	 * TODO Czyszczenie katalogu z całej jego zawartości
	 *
	 * 
	 * @param string $path
	 * @throw Exception
	 */
	public static function recursiveDelete($path) {
		// zmienna przechuje ścieżki katalogów do usunięcia
		$directories = array($path);

		$iterator = new RecursiveDirectoryIterator($path);
		$iterator = new RecursiveIteratorIterator($iterator);

		while ($iterator->valid()) {
			$file = $iterator->current();
			Zend_Debug::dump($file->getPathname());
			if ($file->isFile()) {
				if (!unlink($file->getPathname())) {
					$message = "Can't unlink file";
					throw new Exception($message);
				}
			} else
			if (!$file->isDot()){
				// zachowujemy katalog, by go usunąć puźniej
				// ponieważ nie wiemy czy jest pusty czy też nie
				// a pusty katalog nie może być usunięty!
				array_unshift($directories, $file->getPathname());
			}
			$iterator->next();
		}

		// kasujemy zaległe katalogi
		foreach ($directories as $path) {
			if (!rmdir($path)) {
				$message = "Can't unlink directory";
				throw new Exception($message);
			}
		}
	}
}