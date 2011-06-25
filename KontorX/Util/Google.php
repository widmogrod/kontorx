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
	const TYPE_XPATH = 'TYPE_XPATH';
	const TYPE_DEFAULT = 'TYPE_DEFAULT';
	
	
	protected $_siteUri = null;
	protected $aProxies = array();
	protected $aProxiesFaild = array();
	protected $sProxiesFile = null;
		
	public function __construct($siteUri, $googleDomain = null)
	{
		$this->setSiteUri($siteUri);

		if (null !== $googleDomain) {
		    $this->setGoogleDomain($googleDomain);
		}
	}
	
	public function setSiteUri($siteUri)
	{
	    $siteUri = trim($siteUri);
	    if (empty($siteUri))
	    {
	        require_once 'KontorX/Util/Google/Exception.php';
	        $message = 'Site url can\'t by empty';
	        throw new KontorX_Util_Google_Exception($message);
	    }

	    $this->_siteUri = $siteUri;
	}
	
	public function getSiteUri()
	{
	    return $this->_siteUri;
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
	
	protected $_useProxies = true;
	
	public function setUseProxies($flag = true)
	{
		$this->_useProxies = (bool) $flag;
	}
	
	protected $_timeout = 10;
	
	public function setTimeout($timeout)
	{
		$this->_timeout = abs((int) $timeout);
	}
	
	public function getTimeout()
	{
		return $this->_timeout;
	}
	
	protected $_type = self::TYPE_XPATH;
	
	public function setType($type)
	{
		switch ($type)
		{
			case self::TYPE_DEFAULT:
			case self::TYPE_XPATH:
				$this->_type = $type;
				break;

			default:
			    require_once 'KontorX/Util/Google/Exception.php';
    	        $message = 'Undefined type "%s"';
    	        $message = sprintf($message, $type);
    	        throw new KontorX_Util_Google_Exception($message);
		}
	}

	public function getType()
	{
		return $this->_type;
	}

	public function position($keyword = null, $perPage = null)
	{
	    if (null !== $keyword) {
	        $this->setKeyword($keyword);
	    }
	    
	    if (null !== $perPage) {
	        $this->setPerPage($perPage);
	    }
	    
	    $this->clearPositions();

		if (count($this->aProxies) && true === $this->_useProxies)
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
		} else {
			$sData = $this->getData();
		}

		if (!$sData)
			return false;

		$searchHostname = parse_url($this->getSiteUri(), PHP_URL_HOST);
		if (!$searchHostname) {
		    $searchHostname = pathinfo($this->getSiteUri(), PATHINFO_BASENAME);
		}
		
    	switch($this->getType())
    	{
    		case self::TYPE_XPATH:

	    		require_once 'Zend/Dom/Query.php';
		    	$query = new Zend_Dom_Query($sData);
		
		    	$elements = $query->queryXpath('//li[contains(normalize-space(@class), \'g\')]'.
		    								   '//h3[contains(normalize-space(@class), \'r\')]'.
		    								   '//a[contains(normalize-space(@class), \'l\')]');
		
		    	if (!count($elements)) {
		    		return false;
		    	}

		    	$position = false;
		    	foreach($elements as $key => /* @var $element DOMElement */ $element)
		    	{
		    		$href = $element->getAttribute('href');

		    		$href = strip_tags($href);
					if (strpos($href, $searchHostname) !== false) 
					{
					    $position = $key+1;

					    /*
					     * Wędrówka od elementu a > h3
					     * Następnie sprawdzenie czy istnieje poniżej element div 
					     */
					    $divElement = $element->parentNode->nextSibling;
					    if ($divElement instanceof DOMElement) 
					    {
					        /*
					         * Sprawdzenie czy poniżej elemenu div jest tabela
					         * jeżeli tak to jest to pozycja MapGoogle
					         * Zapisać pozycję i poszukiwanie pozycji dla wyszukiwania organicznego 
					         */

					        /* @var $tableElement DOMElement */
					        $tableElement = $divElement->nextSibling;
					        
					        if ($tableElement instanceof DOMElement 
					            && strtolower($tableElement->nodeName) == 'table')
					        {
					            if (null === $this->_localPosition) {
					                $this->_localPosition = $position;
					            }
                                continue;
					        }
					    }
					    
					    if (null === $this->_orgnicPosition) {
					        $this->_orgnicPosition = $position;
					    }
					}
		    	}
		    	
    			break;

    		default:
    		case self::TYPE_DEFAULT:
    			// lapiemy linki
				$matches = array();
				preg_match_all('@<li class=g><h3 class="r"><a href="([^"]+)" class=l@i', $sData, $matches);
		
				// lementy pod kluczem 1 zawieraja tylko linki do strony
				$matches = (array) @$matches[1];
				if (count($matches) == 0) {
					return false;
				}
		
				// szuka pozycje w Google
				foreach($matches as $key => $href)
				{
				    $href = strip_tags($href);
				    if (strpos($href, $searchHostname) !== false)  
				    {
				        if (null === $this->_orgnicPosition) 
				        {
				            $position = $key+1;
					        return $this->_orgnicPosition = $position;
					    }
				    }
				}
				
    			break;
    	}
    	
    	$this->_localPosition = (null === $this->_localPosition) ? false : $this->_localPosition;
    	$this->_orgnicPosition = (null === $this->_orgnicPosition) ? false : $this->_orgnicPosition;
    	
    	return $this->getOrganicPosition();;
	}
	
	protected $_googleDomain;
	
	/**
	 * @todo validate
	 * @param string $googleDomain
	 */
	public function setGoogleDomain($googleDomain)
	{
	    $googleDomain = parse_url($googleDomain, PHP_URL_HOST);
	    $this->_googleDomain = $googleDomain;
	}
	
    public function getGoogleDomain()
	{
	    if (null === $this->_googleDomain) 
	    {
	        $this->_googleDomain = 'www.google.pl';
	    }
	    return $this->_googleDomain;
	}
	
	
	protected $_keyword;
	
	public function setKeyword($keyword)
	{
	    $this->_keyword = $keyword;
	}
	
	public function getKeyword($throwException = true)
	{
	    if (empty($this->_keyword) && $throwException) 
	    {
	        require_once 'KontorX/Util/Google/Exception.php';
	        $message = 'Keyword is required. Use method $this->setKeyword() to set keyword.';
	        throw new KontorX_Util_Google_Exception($message);
	    }
	    
	    return $this->_keyword;
	}
	
	/**
	 * @var number
	 */
	protected $_onPage = 100;
	
	/**
	 * @param integer $onPage
	 * @throws KontorX_Util_Google_Exception
	 */
	public function setOnPage($onPage)
	{
	    if ($onPage < 10 || $onPage > 100)
	    {
	        require_once 'KontorX/Util/Google/Exception.php';
	        $message = 'Google can display on page from 10 to 100 search results.';
	        throw new KontorX_Util_Google_Exception($message);
	    }
	    
	    $this->_onPage = (int) $onPage;
	}
	
	/**
	 * @return number
	 */
	public function getOnPage()
	{
	    return $this->_onPage;
	}
	
	/**
	 * @var number
	 */
	protected $_perPage = 1;
	
	/**
	 * @param number $perPage
	 * @throws KontorX_Util_Google_Exception
	 */
	public function setPerPage($perPage)
	{
	    if ($perPage < 0) 
	    {
	        require_once 'KontorX/Util/Google/Exception.php';
	        $message = 'Per page value should be grater than 0';
	        throw new KontorX_Util_Google_Exception($message);
	    }

	    $this->_perPage = (int) $perPage;
	}
	
	/**
	 * @return number
	 */
	public function getPerPage()
	{
	    return $this->_perPage;
	}
	
	/**
	 * @var number
	 */
	protected $_orgnicPosition;
	
	/**
	 * @return number
	 */
	public function getOrganicPosition()
	{
	    if (null === $this->_orgnicPosition) {
	        $this->position();
	    }
	    return $this->_orgnicPosition;
	}
	
	/**
	 * @var number
	 */
	protected $_localPosition;

	/**
	 * @return number
	 */
	public function getLocalPosition()
	{
	    if (null === $this->_localPosition) {
	        $this->position();
	    }

	    return $this->_localPosition;
	}

	public function clearPositions()
	{
	    $this->_localPosition = null;
	    $this->_orgnicPosition = null;
	}
	
	/**
	 * @var array
	 */
	protected $_data = array();

	/**
	 * @param unknown_type $proxy
	 * @return boolean|mixed
	 */
	public function getData($proxy = null)
	{
	    $cacheKey = $this->_cacheKey($proxy);
	    if (isset($this->_data[$cacheKey])) {
	        return $this->_data[$cacheKey];
	    }

		// laczenie z Google.
		$curl = curl_init();

		curl_setopt($curl, CURLOPT_HEADER, 0);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_VERBOSE, 0);
		curl_setopt($curl, CURLOPT_REFERER, $this->getGoogleDomain());
		curl_setopt($curl, CURLOPT_URL, sprintf( 'http://%s/search?hl=pl&q=%s&num=%d', $this->getGoogleDomain(),urlencode($this->getKeyword()), $this->getOnPage()));
		curl_setopt($curl, CURLOPT_TIMEOUT, $this->getTimeout());

		if (null !== $proxy) {
			curl_setopt($curl, CURLOPT_PROXY, $proxy);
		}

		$result = curl_exec($curl);
		
		if (0 != curl_errno($curl)) 
		{
			curl_close($curl);
			return false;			
		}

		// Jeżeli jest odpowiedź jest mniejsza niż 5000 znaków
		// oznacza to że że odpowiedź z google nie zawiera wyników wyszukiwania
		// tylko mechanizm ochronny googla przed nadmiernym pingowaniem
		if (strlen($result) < 5000)
			return false;

		curl_close($curl);
		
		return $this->_data[$cacheKey] = $result;
	}
	
	/**
	 * Do przyśpieszenia testowania
	 * @param string $data
	 * @param string $proxy
	 */
	public function setData($data, $proxy = null)
	{
	    $cacheKey = $this->_cacheKey($proxy);
	    $this->_data[$cacheKey] = (string) $data;
	}

	protected function _cacheKey($proxy = null)
	{
	    return $proxy . $this->getGoogleDomain() . $this->getKeyword() . $this->getOnPage();
	}
}