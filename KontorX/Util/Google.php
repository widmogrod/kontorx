<?php
/**
 * 
 */
class KontorX_Util_Google {
	private
		$sUri = null;

	public function __construct( $sUrl ){
		$this->sUri = $sUrl;
	}

	public function position( $sWord, $iCount = null ){
		$iCount = is_integer( $iCount )
			? $iCount : 100;

		// laczenie z google
		$rCurl = curl_init();

		curl_setopt($rCurl, CURLOPT_HEADER, 0);
		curl_setopt($rCurl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($rCurl, CURLOPT_VERBOSE, 1);
		curl_setopt($rCurl, CURLOPT_REFERER, 'www.google.pl');
		curl_setopt($rCurl, CURLOPT_URL, sprintf( 'http://www.google.pl/search?hl=pl&q=%s&num='.$iCount, urlencode( $sWord )));

		$sData = curl_exec($rCurl);

		curl_close($rCurl);

		// lapiemy linki
		// TODO Dodać lapanie w wynikach pola z pdf etc.
		$aResults = array();
		preg_match_all('@<li class=g><h3 class=r><a href="([^"]+)" class=l@i', $sData, $aResults );

		// generowanie url'a z www i bez ..
		$aInfo = pathinfo($this->sUri);
		$sUri = strpos( $this->sUri, '://www') === false
			? $aInfo['dirname'] . 'www.' . $aInfo['basename']
			: $aInfo['dirname'] . '//' . substr( strrchr( $aInfo['basename'],'www.'), 2 );

		// lementy pod kluczem 1 zawieraja tylko linki do strony
		$aResults = (array) @$aResults[1];

		$aReturn = array($this->sUri => false, $sUri=>false);

		// szuka pozycje w google.pl
		foreach( $aResults as $iKey => $sRowUrl ){
			$sString = strip_tags( $sRowUrl );
			if( strpos( $sString, $this->sUri ) !== false)
				$aReturn[$this->sUri] = $iKey+1;

			if(  strpos( $sString, $sUri ) !== false )
				$aReturn[$sUri] = $iKey+1;
		}

		// zwroc najwieksza pozycje
		arsort($aReturn);
		return (int) array_shift( $aReturn );
	}
}