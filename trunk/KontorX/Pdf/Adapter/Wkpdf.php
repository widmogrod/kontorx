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
	public function render() 
	{
		$scriptName = $this->getScriptName();
		$options	= array('--load-error-handling ignore');
		$options	= implode(' ', $options);
		$input 		= $this->getInputFilepath();
		$output 	= $this->getOutputFilepath();
		
		$cmd = sprintf('%s %s %s %s', $scriptName, $options, $input, $output);
		
		list($stdout, $stderr, $return) = $this->_pipeExec($cmd);
	}

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