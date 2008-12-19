<?php
require_once 'Zend/Db/Table/Row/Abstract.php';

/**
 * KontorX_Db_Table_Row_FileUpload_Abstract
 * 
 * TODO Dodać możliwość określenia czy plik ma być uploadowany czy też nie
 * TODO Informacje o bledzie raczej nie przez throw, bo blokuje dalsze wykonywanie akcji
 * TODO Pobieranie informacji o bledzie jezeli jest przez jakies uslugi ..
 *
 * @category	KontorX
 * @package		KontorX_Db
 * @subpackage	Table
 * @version		0.1.5
 */
abstract class KontorX_Db_Table_Row_FileUpload_Abstract extends Zend_Db_Table_Row_Abstract {
	
	/**
	 * Nazwa pola w tablicy _FILES
	 *
	 * @var string
	 */
	protected $_filesKeyName = 'image';

	/**
	 * Pole w tabeli, w ktorym przechowywujemy uploadowaną nazwe pliku
	 *
	 * @var string
	 */
	protected $_fieldFilename = null;

	/**
	 * Przechowuje komunikaty bledu
	 *
	 * @var array
	 */
	protected $_messages = array();
	
	/**
	 * Scieżka katalogu do uploadu
	 *
	 * @var string
	 */
	protected static $_uploadPath = null;

	/**
	 * Overwrite
	 */
	public function init(){
		if (null === $this->_fieldFilename) {
			require_once 'KontorX/Db/Table/Row/FileUpload/Exception.php';
			throw new KontorX_Db_Table_Row_FileUpload_Exception('Field name to store file upload name is not set');
		}
		if (!array_key_exists($this->_fieldFilename, $this->_data)) {
            require_once 'KontorX/Db/Table/Row/FileUpload/Exception.php';
            throw new KontorX_Db_Table_Row_FileUpload_Exception("Specified column \"$this->_fieldFilename\" is not in the row");
        }

        if (!is_dir(self::$_uploadPath) || null === self::$_uploadPath) {
        	require_once 'KontorX/Db/Table/Row/FileUpload/Exception.php';
            throw new KontorX_Db_Table_Row_FileUpload_Exception("Upload path \"".self::$_uploadPath."\" do not exsists");
        }
	}
	
	/**
	 * Ustawienie katalogu do uploadowania plikow
	 *
	 * @param string $path
	 */
	public static function setImagePath($path) {
//		if (!is_dir($path)) {
//			require_once 'KontorX/Db/Table/Row/FileUpload/Exception.php';
//            throw new KontorX_Db_Table_Row_FileUpload_Exception("Upload path \"".self::$_uploadPath."\" do not exsists");
//		}

		self::$_uploadPath = $path;
	}

	public static function setUploadPath($path) {
		self::setImagePath($path);
	}
	
	public function addMessage($message) {
		$this->_messages[] = (string) $message;
	}

	public function getMessages() {
		return $this->_messages;
	}

	public function hasMessages() {
		return !empty($this->_messages);
	}

	/**
	 * Zwraca nazwe katalogu do ktorego uploadujemy pliki
	 *
	 */
	public static function getUploadPath() {
		return self::$_uploadPath;
	}
	
	/**
	 * Zwraca nazwe katalogu do ktorego uploadujemy pliki
	 *
	 */
	protected function _getUploadPath() {
		return self::$_uploadPath;
	}

	/**
	 * Zwraca wartosc z _FILES o kluczu zdefiniowanym w @see $this->_filesKeyName
	 *
	 * @return array
	 */
	protected function _getFilesArray() {
		return (array) @$_FILES[$this->_filesKeyName];
	}

	/**
	 * Zapewnia uploadowanie pliku
	 *
	 */
	protected function _insert() {
		$path = $this->_getUploadPath();
		$files = $this->_getFilesArray();

		require_once 'KontorX/File/Upload.php';
		$upload = new KontorX_File_Upload($files);

		if (!$upload->isUploaded()) {
			$message = "Plik nie został uploadowany";
			$this->addMessage($message);
		} else
		if (!$upload->move($path, true)) {
			$message = "Plik nie został przeniesiony";
			$this->addMessage($message);
		} else {
			$this->{$this->_fieldFilename} = $upload->getGenerateUniqFileName();
		}
		$upload->clean();
	}

	/**
	 * Zapewnia uploadowanie pliku
	 *
	 */
	protected function _update() {
		$path = $this->_getUploadPath();
		$files = $this->_getFilesArray();

		require_once 'KontorX/File/Upload.php';
		$upload = new KontorX_File_Upload($files);

		if (!$upload->isUploaded()) {
			$message = "Plik nie został uploadowany";
			$this->addMessage($message);
		} else
		if (!$upload->move($path, true)) {
			$message = "Plik nie został przeniesiony";
			$this->addMessage($message);
		} else {
			// skauj stary plik
			$path = $this->_getUploadPath();
			$file = $this->{$this->_fieldFilename};
			if (@unlink("$path/$file") === false){
				// nieudane kasowanie pliku
				$message = "Nie udało się usunąć grafiki wraz z edycją rekordu";
				$this->addMessage($message);
			}
			// dodaj informacje o nowym pliku
			$this->{$this->_fieldFilename} = $upload->getGenerateUniqFileName();
		}
		$upload->clean();
	}
	
	/**
	 * Dba o usuniecie pliku po skasowaniu rekordu
	 *
	 */
	protected function _postDelete() {
		$path = $this->_getUploadPath();
		$file = $this->{$this->_fieldFilename};
                $filename = "$path/$file";

                if (!file_exists($filename)) {
                    $message = "Plik do usunięcia nie istnieje";
                    $this->addMessage($message);
                    return;
                }
            
		if (@unlink($filename) === false){
			$message = "Nie udało się usunąć grafiki wraz z edycją rekordu";
			require_once 'KontorX/Db/Table/Row/FileUpload/Exception.php';
			throw new KontorX_Db_Table_Row_FileUpload_Exception($message);
		}
	}
}