<?php
/**
 * Promotor_Controller_Plugin_PDF
 * 
 * Klasa odpowiada za generowanie plikow PDF zamiast
 * generowania HTML.
 * 
 * Wykorzystywanajest biblioteka KontorX_Pdf
 * 
 * @author $Author$
 * @version $Id$
 */
class Promotor_Controller_Plugin_PDF extends Zend_Controller_Plugin_Abstract
{
	/**
	 * Nazwa parametru, który jest poszukiwany przez obiekt {@see Zend_Controller_Request_Abstract} 
	 * Ustawienie parametru na 1 (true) włącza możliwość generowania PDF.
	 * 
	 * @var string
	 */
	const ENABLED_PARAM_KEY = '__generatePDF';
	
	/**
	 * @var bool
	 */
	protected $_responseToPdf = false;

	/**
	 * Ustaw flagę że można generować PDF
	 * @param bool $flag
	 */
	public function setResponseToPdf($flag = true)
	{
		$this->_responseToPdf = (bool) $flag;
	}
	
	/**
	 * @var string
	 */
	protected $_filename;

	/**
	 * Ustaw nazwę pliku
	 * @param string $filename
	 */
	public function setFilename($filename)
	{
		$this->_filename = $filename;
	}
	
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
        $enabled = $request->getParam(self::ENABLED_PARAM_KEY, $this->_responseToPdf);
        if (!$enabled)
        	return;

        $html = $response->getBody();
        $response->clearBody();

        $filename = $request->getParam('filename','plik');
        $filename .= '.pdf'; 

        $options = array(
        	'tempDir' => TMP_PATHNAME,
        	'filename' => $filename,
        	'scriptName' => 'wkhtmltopdf-amd64'
       	);
        
		$pdf = KontorX_Pdf::factory('Wkpdf', $options);
		$pdf->setHtml($html);
		
		if (null !== $this->_filename)
			$pdf->setFilename($this->_filename);
		
		$pdf->output();
	}
}