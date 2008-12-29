<?php
/**
 * Umilenie uploadowania pliku na serwer ;]
 *
 * @category 	KontorX
 * @package 	KontorX_File
 * @version 	0.1.6
 * @license		GNU GPL
 * @author 		Marcin `widmogror` Habryn, widmogrod@gmail.com
 */
class KontorX_File_Upload {

	/**
	 * Nazwa pliku w folderze tymczasowym
	 *
	 * @var string
	 */
	private $_fileTempName = null;

	/**
	 * Nazwa uloadowanego pliku
	 *
	 * @var string
	 */
	private $_fileName = null;

	/**
	 * Czy generować unikalną nazwe pliku
	 *
	 * @var string
	 */
	private $_generateUniqFileName = false;

	/**
	 * Typ uploadowanego pliku
	 *
	 * @var string
	 */
	private $_fileType = null;

	/**
	 * Rozmiar uploadowanego pliku
	 *
	 * @var double
	 */
	private $_fileSize = null;

	/**
	 * Błędy podczas uploadowania pliku
	 *
	 * @var array
	 */
	private $_errors = array();

	/**
	 * Czy jest uploadowany
	 *
	 * @var boolean
	 */
	private $_uploaded = false;

	/**
	 * Enter description here...
	 *
	 * @var string
	 */
	private $_dir = null;

	/**
	 * Konstruktor, w parametrze $fiels przekazuje się tablise $_FILES
	 *
	 * @param array $fiels
	 */
	public function __construct(array $fiels) {
		$this->_fileTempName = @$fiels['tmp_name'];
		$this->_fileName = @$fiels['name'];
		$this->_fileSize = @$fiels['size'];
		$this->_fileType = @$fiels['type'];
		$this->_errors   = @$fiels['error'];

		if(is_uploaded_file($this->_fileTempName)){
			$this->_uploaded = true;
		}
	}

	/**
	 * Czy plik został uploadowany
	 *
	 * @return boolean
	 */
	public function isUploaded() {
		return $this->_uploaded;
	}

	/**
	 * Przenoszenie pliku
	 *
	 * @param string $filePath
	 * @param string $generateUniqName
	 * @param boolean $filterName
	 * @return boolean
	 */
	public function move($filePath, $generateUniqName = false, $filterName = true) {
		if($generateUniqName === true) {
			// Generujemy unikalną nazwę dla pliku
			// Jeżeli $filePath wskazuje na istniejący plik,
			// pobiera jego nazwę wprzeciwnym wypadku pobiera nazwe pliku 
			// przekazanej w konstroktorze tablicy $_FILES
			$fileName = is_file($filePath)
			? basename($filePath)
			: $this->_fileName;

			if (true === $filterName) {
				$extension = end(explode('.', $fileName));
				$name	   = implode('.', (array) $a);

				require_once 'KontorX/Filter/Word/Rewrite.php';
				$filter = new KontorX_Filter_Word_Rewrite();
				$name = $filter->filter($name);

				$fileName = "$name.$extension";
			}

			$this->_fileName = $this->_generateUniqFileName = md5($fileName . time() . microtime(true)) . '_' . $fileName;
			$filePath = $filePath . '/' . $this->_generateUniqFileName;
		}

		return move_uploaded_file($this->_fileTempName, $filePath);
	}

	/**
	 * Usuwa plik z katalogu tymczasowego
	 *
	 * @return boolean
	 */
	public function clean() {
		return file_exists($this->_fileTempName) ? unlink($this->_fileTempName) : false ;
	}

	/**
	 * Zwraca nazwe pliku, zależnie od parametru $withdExtension z/bez rozszerzeniem
	 *
	 * @param boolean $withdExtension
	 * @return string
	 */
	public function getName($withdExtension = true) {
		return (bool) $withdExtension
		? $this->_fileName
		: substr($this->_fileName, 0, strpos($this->_fileName, '.'));
	}

	/**
	 * Zwraca $_FILES['tmp_name']
	 *
	 * @return string
	 */
	public function getFileTempName() {
		return $this->_fileTempName;
	}

	/**
	 * Zwraca nazwe wygenerowanej unikalnej nazwy dla, uploadowanego pliku
	 *
	 * @return 		string
	 */
	public function getGenerateUniqFileName() {
		return $this->_generateUniqFileName;
	}

	/**
	 * Zwraca rozszerzenie uploadowanego pliku
	 *
	 * @return string
	 */
	public function getExtension() {
		return substr(strrchr($this->getName(), '.'), 1);
	}

	/**
	 * Zwraca typ mime uploadowanego pliku
	 *
	 * @return string
	 */
	public function getMime() {
		return $this->_fileType;
	}

	/**
	 * Zwraca rozmiar uploadowanego pliku
	 *
	 * @return double
	 */
	public function getSize() {
		return $this->_fileSize;
	}

	/**
	 * Zwraca wystąpione błędy, podczas uploadowania pliku
	 *
	 * @param boolean $bToString
	 * @return array|string
	 */
	public function getErrors($toString = false) {
		switch ($this->_errors) {
			case 0: $msg = 'There is no error, the file uploaded with success.'; break;
			case 1: $msg = 'The uploaded file exceeds the upload_max_filesize directive in php.ini.'; break;
			case 2: $msg = 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.'; break;
			case 3: $msg = 'The uploaded file was only partially uploaded.'; break;
			case 4: $msg = 'No file was uploaded.'; break;
			case 6: $msg = 'Missing a temporary folder. Introduced in PHP 4.3.10 and PHP 5.0.3.'; break;
			case 7: $msg = 'Failed to write file to disk. Introduced in PHP 5.1.0.'; break;
			case 8: $msg = 'File upload stopped by extension. Introduced in PHP 5.2.0.'; break;
		}
		return $toString === true ? $msg : $this->_errors;
	}
}
?>