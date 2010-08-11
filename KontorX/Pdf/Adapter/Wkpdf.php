<?php
require_once 'KontorX/Pdf/Adapter/Abstract.php';

/**
 * Adapter for @see http://code.google.com/p/wkhtmltopdf
 * 
 * Based on:
 * @author Christian Sciberras
 * @see http://code.google.com/p/wkhtmltopdf/
 * @copyright 2010 Christian Sciberras / Covac Software.
 * 
 * @author $Author$
 * @version $Id$
 */
class KontorX_Pdf_Adapter_Wkpdf extends KontorX_Pdf_Adapter_Abstract 
{
	/**
	 * Ścieżka lub nazwa do pliku wykonującego `wkhtmltopdf`
	 * @var string
	 */
	protected $_scriptName;
	
	/**
	 * Można zdefiniować własną nazwę/ścieżkę do pliku `wkhtmltopdf`
	 * @param string $scriptName
	 */
	public function setScriptName($scriptName)
	{
		$this->_scriptName = $scriptName;
	}
	
	/**
	 * Zwraca nazwę pliku wykonywanego jeżeli nie została ona zdefiniowana przez uzytkownika.
	 * Sprawdzana jest architektóra sprzętowa i na jej podstawie wybierana jest nazwa biblioteki.
	 * 
	 * @throws KontorX_Pdf_Exception
	 * @return string
	 */
	public function getScriptName()
	{
		if (null === $this->_scriptName) {
			$this->_scriptName = 'wkhtmltopdf';

			if (`grep -i amd /proc/cpuinfo` != '' ||
				`uname -m | grep x86_64` != '') 
			{
				$this->_scriptName .= '-amd64';
			} elseif (`grep -i intel /proc/cpuinfo` != '') {
				$this->_scriptName .= '-i386';
			} else {
				$message = 'Wkpdf couldn\'t determine CPU.';
				require_once 'KontorX/Pdf/Exception.php';
				throw new KontorX_Pdf_Exception($message);
			}
		}

		return $this->_scriptName;
	}

	/**
	 * Ścieżka do katalogu z plikami tymczasowymi
	 * @var string
	 */
	protected $_tempDir;
	
	/**
	 * Ścieżka do katalogu w którym mają być przechowywane tymczasowe pliki
	 * potrzebne do wygenerowania dokumentu PDF
	 * 
	 * @param string $tempDir
	 */
	public function setTempDir($tempDir)
	{
		$this->_tempDir = $tempDir;
	}
	
	/**
	 * Zwaca ścieżkę do katalogu przechowywującego pliki tymczasowe
	 * 
	 * Jeżeli nie zostanie zdefiniowany katalog TEMP to jest ustawiany
	 * jako domyślny systemowy katalog TEMP 
	 * 
	 * @return string
	 */
	public function getTempDir()
	{
		if (null === $this->_tempDir)
			$this->_tempDir = sys_get_temp_dir();

		return $this->_tempDir;
	}

	/**
	 * @var string
	 */
	protected $_inputFilepath;
	
	/**
	 * Ustaw ścieżkę do pliku, który jest dokumentem HTML,
	 * który ma zostać uzyty do wygenerowania dokumentu PDF
	 * 
	 * @param string $inputFilepath
	 */
	public function setInputFilepath($inputFilepath)
	{
		$this->_inputFilepath = $inputFilepath;
	}
	
	/**
	 * Zwraca ścieżkę do pliku, który ma przechowywać HTML
	 *
	 * Jeżeli nie zostanie podana nazwa pliku, 
	 * zostanie wygenerowana nazwa pliku.
	 * 
	 * @return string
	 */
	public function getInputFilepath()
	{
		if (null === $this->_inputFilepath) {
			$this->_inputFilepath = tempnam($this->getTempDir(), get_class($this));
			$this->_inputFilepath .= '.html';
		}

		return $this->_inputFilepath;
	}
	
	/**
	 * @var string
	 */
	protected $_outputFilepath;
	
	/**
	 * Ścieżka do pliku gdzie ma być zapisany wygenerowany dokument PDF
	 * 
	 * @param string $outputFilepath
	 */
	public function setOutputFilepath($outputFilepath)
	{
		$this->_outputFilepath = $outputFilepath;
	}
	
	/**
	 * Zwróć ścieżkę do pliku, który ma przechowywać dokument PDF
	 * 
	 * Jeżeli nazwa pliku nie została wcześniej podana 
	 * to zostabie wygenerowana nazwa automatycznie
	 *  
	 * @return string
	 */
	public function getOutputFilepath()
	{
		if (null === $this->_outputFilepath) {
			$this->_outputFilepath = $this->getInputFilepath();
			$this->_outputFilepath .= '.pdf';
		}

		return $this->_outputFilepath;
	}
	
	/**
	 * Rozszeżenie metody nadklasy @see KontorX_Pdf_Adapter_Abstract::setHtml()
	 * o specyficzne dla tego adaptera zachowanie podczas podania kodu HTML
	 * do przetworzenia na dokument PDF
	 */
	public function setHtml($html)
	{
		
		$inputFile = $this->getInputFilepath();
		$outputFile = $this->getOutputFilepath();
		
		touch($inputFile);
		touch($outputFile);
		
		chmod($inputFile, 0666);
		chmod($outputFile, 0666);
		
		// zapisz HTML do pliku
		file_put_contents($inputFile, $html);
		
		parent::setHtml($html);
	}

	public function output() 
	{
		$scriptName = $this->getScriptName();
		$options	= array('--load-error-handling ignore');
		$options	= implode(' ', $options);
		$input 		= $this->getInputFilepath();
		$output 	= $this->getOutputFilepath();
		
		$cmd = sprintf('%s %s %s %s', $scriptName, $options, $input, $output);
		
		list($stdout, $stderr, $return) = $this->_pipeExec($cmd);
		
		if(headers_sent()) {
			$message = 'WKPDF download headers were already sent.';
			require_once 'KontorX/Pdf/Exception.php';
			throw new KontorX_Pdf_Exception($message);
		}

		$filename = $this->getFilename();
		
		
		header('Content-Description: File Transfer');
		header('Cache-Control: public, must-revalidate, max-age=0'); // HTTP/1.1
		header('Pragma: public');
		header('Expires: Sat, 26 Jul 1997 05:00:00 GMT'); // Date in the past
		header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
		// force download dialog
		header('Content-Type: application/force-download');
		header('Content-Type: application/octet-stream', false);
		header('Content-Type: application/download', false);
		header('Content-Type: application/pdf', false);
		// use the Content-Disposition header to supply a recommended filename
		header('Content-Disposition: attachment; filename="'.basename($filename).'";');
		header('Content-Transfer-Encoding: binary');
		header('Content-Length: '.filesize($output));

		readfile($output);

		unlink($input);
		unlink($output);
	}

	/**
	 * Execute a command and open file pointers for input/output
	 *  
	 * @param string $cmd
	 * @param string $input
	 * @return multitype:string
	 */
	private static function _pipeExec($cmd, $input = '')
	{
		$proc = proc_open($cmd, array(array('pipe','r'),array('pipe','w'),array('pipe','w')), $pipes);

		fwrite($pipes[0], $input);
		fclose($pipes[0]);

		$stdout = stream_get_contents($pipes[1]);
		fclose($pipes[1]);
		
		$stderr = stream_get_contents($pipes[2]);
		fclose($pipes[2]);
		
		$return = proc_close($proc);

		return array(
			$stdout,
			$stderr,
			$return,
			'stdout' => $stdout,
			'stderr' => $stderr,
			'return' => $return
		);
	}
}