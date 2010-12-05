<?php
/**
 * Klasa sprawdzająca pozycję strony w Google.pl
 * Miłym dodatkiem jest sprawdzanie pozycji z różnych adresów proxy.
 *
 * @author Gabriel Habryn, widmogrod.info
 * @version $Id$
 */
class KontorX_Util_Google 
{
	private
		$sUri = null,
		$iCount = 100,
		$sWord = null,
		$aProxies = array(),
		$aProxiesFaild = array(),
		$sProxiesFile = null;
		
	public function __construct($sUrl)
	{
		$this->sUri = $sUrl;
	}
	
	public function addProxy($proxy)
	{
		$this->aProxies[] = $proxy;
	}

	public function setProxy($proxy)
	{
		$this->aProxies = array($proxy);
		$this->aProxiesFaild = array();
	}
	
	protected $_shuffle = true;
	
	public function setShuffle($flag = true)
	{
		$this->_shuffle = (bool) $flag;
	}
	
	public function isShuffle()
	{
		return $this->_shuffle;
	}
	
	public function setProxies(array $proxies)
	{
		$this->aProxies = $proxies;
		$this->aProxiesFaild = array();
	}

	public function setProxiesFile($file)
	{
		$this->sProxiesFile = $file;

		$proxies = array();
		$proxiesFails = array();

		$handle = fopen($this->sProxiesFile, 'r');
		while ($userinfo = fscanf($handle, "%s\t%d\n")) {
		    list ($proxies[], $proxiesFails[]) = $userinfo;
		}
		fclose($handle);

		$proxies = array_unique($proxies);
		$this->setProxies($proxies);
		
		$this->aProxiesFaild = $proxiesFails;
	}
	
	public function saveProxiesInfoToFile()
	{

		if (null === $this->sProxiesFile)
			return;

		$handle = fopen($this->sProxiesFile, 'w');
		foreach($this->aProxies as $key => $proxie)
		{
			$fails = array_key_exists($key, $this->aProxiesFaild)
				? $this->aProxiesFaild[$key] : 0;

			fwrite($handle, sprintf("%s\t%d\n", $proxie, $fails));
		}

		fclose($handle);
	}

	public function position($sWord, $iCount = null)
	{
		$this->sWord = $sWord;
		$this->iCount = is_integer($iCount)
			? $iCount : 100;

		if (count($this->aProxies))
		{
			if ($this->isShuffle()) {
				shuffle($this->aProxies);
			}
			
			// while ($sProxy = array_pop($this->aProxies)) 
			foreach($this->aProxies as $key => $sProxy)
			{
				$sData = $this->getData($sProxy);
				if (false !== $sData) {
					$this->saveProxiesInfoToFile();
					break;
				} else {
					if (!array_key_exists($key, $this->aProxiesFaild)) {
						$this->aProxiesFaild[$key] = 0;
					}

					++$this->aProxiesFaild[$key];					
				}
			}
			if (false === $sData) {
				$sData = $this->getData();
			}
		} else {
			$sData = $this->getData();
		}

		if (!$sData)
			return false;

		// lapiemy linki
		// TODO Dodać lapanie w wynikach pola z pdf etc.
		$aResults = array();
		preg_match_all('@<li class=g><h3 class="r"><a href="([^"]+)" class=l@i', $sData, $aResults );

		// generowanie url'a z www i bez ..
		$aInfo = pathinfo($this->sUri);
		$sUri = strpos( $this->sUri, '://www') === false
			? $aInfo['dirname'] . 'www.' . $aInfo['basename']
			: $aInfo['dirname'] . '//' . substr( strrchr( $aInfo['basename'],'www.'), 2 );

		// lementy pod kluczem 1 zawieraja tylko linki do strony
		$aResults = (array) @$aResults[1];
		
		if (count($aResults) == 0) {
			return false;
		}

		$aReturn = array($this->sUri => false, $sUri=>false);

		// szuka pozycje w google.pl
		foreach($aResults as $iKey => $sRowUrl)
		{
			$sString = strip_tags($sRowUrl);
			if(strpos($sString, $this->sUri) !== false)
				$aReturn[$this->sUri] = $iKey+1;

			if(strpos($sString, $sUri) !== false)
				$aReturn[$sUri] = $iKey+1;
		}

		// zwroc najwieksza pozycje
		arsort($aReturn);
		return (int) array_shift( $aReturn );
	}
	
	public function getData($sProxy = null)
	{
		// laczenie z Google.pl
		$rCurl = curl_init();

		curl_setopt($rCurl, CURLOPT_HEADER, 0);
		curl_setopt($rCurl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($rCurl, CURLOPT_VERBOSE, 0);
		curl_setopt($rCurl, CURLOPT_REFERER, 'www.google.pl');
		curl_setopt($rCurl, CURLOPT_URL, sprintf( 'http://www.google.pl/search?hl=pl&q=%s&num='.$this->iCount, urlencode($this->sWord)));

		if (null !== $sProxy) {
			curl_setopt($rCurl, CURLOPT_PROXY, $sProxy);
		}

		$sData = curl_exec($rCurl);
		
		if (0 != curl_errno($rCurl)) 
		{
			curl_close($rCurl);
			return false;			
		}

		// Jeżeli jest odpowiedź jest mniejsza niż 5000 znaków
		// oznacza to że że odpowiedź z google nie zawiera wyników wyszukiwania
		// tylko mechanizm ochronny googla przed nadmiernym pingowaniem
		if (strlen($sData) < 5000)
			return false;

		curl_close($rCurl);
		
		return $sData;
	}
}