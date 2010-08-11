<?php
abstract class KontorX_Pdf_Adapter_Abstract
{
	/**
	 * Renderowanie dokumentu HTML do PDF
	 * @return void
	 */
	abstract public function render();

	/**
	 * @var string
	 */
	protected $_html;
	
	/**
	 * Ustaw HTML
	 * @param striing $html
	 */
	public function setHtml($html)
	{
		$this->_html = $html;
	}
	
	/**
	 * Pobierz HTML
	 * @return string
	 */
	public function getHtml()
	{
		return $this->_html;
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
}