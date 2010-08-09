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
	public function __construct(array $options = null)
	{
		if (is_array($options))
			$this->setOptions($options);
	}

	protected $_scriptName;
	
	public function setScriptName($scriptName)
	{
		$this->_scriptName = $scriptName;
	}
	
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
				$message = 'WKPDF couldn\'t determine CPU ("'.`grep -i vendor_id /proc/cpuinfo`.'").';
				require_once 'KontorX/Pdf/Exception.php';
				throw new KontorX_Pdf_Exception($message);
			}
		}

		return $this->_scriptName;
	}

	protected $_tempDir;
	
	public function setTempDir($tempDir)
	{
		$this->_tempDir = $tempDir;
	}
	
	public function getTempDir()
	{
		if (null === $this->_tempDir)
			$this->_tempDir = sys_get_temp_dir();

		return $this->_tempDir;
	}
	
	protected $_inputFilepath;
	
	public function setInputFilepath($inputFilepath)
	{
		$this->_inputFilepath = $inputFilepath;
	}
	
	public function getInputFilepath()
	{
		if (null === $this->_inputFilepath) {
			$this->_inputFilepath = tempnam($this->getTempDir(), get_class($this));
			$this->_inputFilepath .= '.html';
		}

		return $this->_inputFilepath;
	}
	
	protected $_outputFilepath;
	
	public function setOutputFilepath($outputFilepath)
	{
		$this->_outputFilepath = $outputFilepath;
	}
	
	public function getOutputFilepath()
	{
		if (null === $this->_outputFilepath)
		$this->_outputFilepath = $this->getInputFilepath() . '.pdf';

		return $this->_outputFilepath;
	}
	
	public function setHtml($html)
	{
		// zapisz html do pliku
		$inputFile = $this->getInputFilepath();
		$outputFile = $this->getOutputFilepath();
		
		touch($inputFile);
		touch($outputFile);
		
		chmod($inputFile, 0666);
		chmod($outputFile, 0666);
		
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