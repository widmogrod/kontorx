<?php
/**
 * Validate_File
 * 
 * @category 	File
 * @package 	KontorX_Validate
 * @version 	0.1.1
 * @license		GNU GPL
 * @author 		Marcin `widmogror` Habryn, widmogrod@gmail.com
 */
class KontorX_Validate_File extends Zend_Validate_Abstract {
	const MSG_ALLOW_MIME = 'msgAllowMime';
    const MSG_MIN_SIZE = 'msgMinSize';
    const MSG_MAX_SIZE = 'msgMaxSize';
    const MSG_EXTENSION = 'msgExtension';
    const MSG_NOT_UPLOADED = 'notNotUploaded';

    protected $_files = array();
    
    protected $_mime = null;
    protected $_minSize = null;
    protected $_maxSize = null;
    protected $_extension = null;

    protected $_messageVariables = array(
        'mime' => '_mime',
    	'minSize' => '_minSize',
        'maxSize' => '_maxSize',
    	'extension' => '_extension'
    );

    protected $_messageTemplates = array(
        self::MSG_ALLOW_MIME => "file has wrong MIME type, allow MIME types are '%mime%'",
        self::MSG_MIN_SIZE => "filesize must be at least '%minSize%'",
        self::MSG_MAX_SIZE => "filesize must be no more than '%maxSize%'",
        self::MSG_EXTENSION => "file has wrong extension, allow extensions are '%extension%'",
        self::MSG_NOT_UPLOADED => "file is not uploaded"
    );
    
    public function __construct(array $files = null) {
    	if (null === $files) {
    		$files = $_FILES;
    	}
    	$this->_files = $files;
    }

    public function setMime($mime) {
    	$this->_mime = (string) $mime;
    }

	public function getMime() {
		return $this->_mime;
	}

	public function setMinSize($size) {
		$this->_minSize = (int) $size;
	}

	public function getMinSize() {
		return $this->_minSize;
	}
	
	public function setMaxSize($size) {
		$this->_maxSize = (int) $size;
	}

	public function getMaxSize() {
		return $this->_maxSize;
	}
	
	public function setExtension($extension) {
		$this->_extension = (string) $extension;
	}

	public function getExtension() {
		return $this->_extension;
	}

    public function isValid($value) {
    	$isValid = true;

		$this->_setValue((string) $value);

    	if (is_array($value)) {
    		$fileArray = $value;
    	} else
   		if (array_key_exists($value, $this->_files)) {
			$fileArray = $this->_files[$value];
    	} else {
    		$this->_error(self::MSG_NOT_UPLOADED);
    		return false;
    	}

    	$file = new KontorX_Request_Files($fileArray);

    	if (!$file->isUploaded()) {
    		$this->_error(self::MSG_NOT_UPLOADED);
    		return false;
    	}

    	$extension = $this->getExtension();
    	if (null !== $extension) {
			$extension = strtolower($extension);
			$fileExtension = strtolower($file->getExtension());
    		if (!in_array($fileExtension, explode(':',$extension))){
    			$isValid = false;
    			$this->_error(self::MSG_EXTENSION);
    		}
    	}
    	
    	$mime = $this->getMime();
    	if (null !== $mime) {
    		$mime = strtolower($mime);
    		$fileMime = $file->getMime();
    		if (!in_array($fileMime, explode(':',$mime))) {
    			$isValid = false;
    			$this->_error(self::MSG_ALLOW_MIME);
    		}
    	}
    	
    	$minSize = $this->getMinSize();
    	if (null !== $minSize) {
    		if ((int) $file->getSize() < $minSize) {
    			$isValid = false;
    			$this->_error(self::MSG_MIN_SIZE);
    		}
    	}
    	
    	$maxSize = $this->getMaxSize();
    	if (null !== $maxSize) {
    		if ((int) $file->getSize() > $maxSize) {
    			$isValid = false;
    			$this->_error(self::MSG_MAX_SIZE);
    		}
    	}

        return $isValid;
    }
}
?>