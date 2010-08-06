<?php
/**
 * KontorX_Controller_Plugin_DomPDF
 * 
 * Klasa odpowiada za generowanie plikow PDF zamiast
 * generowania HTML.
 * 
 * Wykorzystywanajest biblioteka DomPDF (HTML to PDF converter)
 * @link http://code.google.com/p/dompdf
 * 
 * @author $Author$
 * @version $Id$
 */
class KontorX_Controller_Plugin_DomPDF extends Zend_Controller_Plugin_Abstract
{
	/**
	 * Nazwa parametru, który jest poszukiwany przez obiekt {@see Zend_Controller_Request_Abstract} 
	 * Ustawienie parametru na 1 (true) włącza możliwość generowania PDF.
	 * 
	 * @var string
	 */
	const ENABLED_PARAM_KEY = '__generatePDF';

	public function postDispatch(Zend_Controller_Request_Abstract $request)
    {
    	$response = $this->getResponse();

        // Return early if forward detected
        if (!$request->isDispatched()
            || $response->isRedirect())
        {
            return;
        }

        /**
         * Czy jest włączone generowanie plików PDF
         * TODO: Zastanowić się czy nie przełożyć włączania
         * generowania PDF do pomocnika akcji
         */
        $enabled = $request->getParam(self::ENABLED_PARAM_KEY);
        if (!$enabled)
        	return;

        $html = $response->getBody();
        $response->clearBody();
        
        $filename = $request->getParam('filename','plik');
        $filename .= '.pdf'; 

        /**
         * Prekonfigurowanie DomPDF 
         */
        defined('DOMPDF_ENABLE_REMOTE') 
			or define('DOMPDF_ENABLE_REMOTE', true);
		
		defined('DOMPDF_ENABLE_PHP') 
			or define('DOMPDF_ENABLE_PHP', false);

        // Włącz debugowanie
        if ($request->getParam('__debug') == '1') {
			defined('DEBUGPNG') 
				or define('DEBUGPNG',1);
				
			defined('DEBUGKEEPTEMP') 
				or define('DEBUGKEEPTEMP',1);
				
			defined('DEBUGCSS') 
				or define('DEBUGCSS',1);
        }

        /**
		 * DomPDF - HTML to PDF converter
		 * @link http://code.google.com/p/dompdf
		 */
		require_once("dompdf/dompdf_config.inc.php");

		$dompdf = new DOMPDF();
		$dompdf->set_paper('A4');
		$dompdf->load_html($html);
		$dompdf->render();
		$dompdf->stream($filename);
	}
}