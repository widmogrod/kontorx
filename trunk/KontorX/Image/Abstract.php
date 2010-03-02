<?php
abstract class KontorX_Image_Abstract {
    // domyslna jakosc grafiki
    const IMAGE_QUALITY = 85;

    /**
     * Typ pliku
     * @var integer
     */
    protected $_type = null;

    /**
     * Typ mime pliku
     * @var string
     */
    protected $_mine = null;

    /**
     * Szerokosc pliku
     * @var integer
     */
    protected $_width = null;

    /**
     * Wysokosc pliku
     * @var integer
     */
    protected $_height = null;

    /**
     * Uchwyt pliku
     * @var resource
     */
    protected $_image = null;

    /**
     * @var string
     */
    protected $_pathname = null;

    /**
     * @param string $pathname
     */
    public function __construct($pathname) {
        $this->_pathname = $pathname;
        $this->load($pathname);
    }

    /**
     * @return string
     */
    public function getPathname() {
    	return $this->_pathname;
    }
    
    /**
     * Wczytanie grafiki
     * @param string $pathname
     */
    public function load($pathname) {
        // sprawdz czy istnieje plik
        if (!file_exists($pathname) && is_readable($pathname)) {
            $message = "File '$pathname' does not exists or is not readable";
            require_once 'KontorX/Image/Exception.php';
            throw new KontorX_Image_Exception($message);
        }

        // pobierz informacje o grafice
        $data = getimagesize($pathname);
        $this->_width	= $data[0];
        $this->_height	= $data[1];
        $this->_type	= $data[2];
        $this->_mine	= $data['mime'];

        $this->_image = $this->_imageCreateFrom($pathname);
    }

    /**
     * @return resource
     * @throws KontorX_Image_Exception
     */
    protected function _getImage() {
   		// sprawdz czy zaladowano grafike
        if (!is_resource($this->_image)) {
            $message = 'Image not loaded';
            require_once 'KontorX/Image/Exception.php';
            throw new KontorX_Image_Exception($message);
        }

    	return $this->_image;
    }
    
    /**
     * Zwraca typ pliku graficznego
     * @return 	integer
     */
    public function getType() {
        return $this->_type;
    }

    /**
     * Zwraca MIME pliku
     * @return 	string
     */
    public function getMime() {
        return $this->_mine;
    }

    /**
     * @param integer $width
     * @param integer $height
     * @return KontorX_Image_Abstract
     */
    public function resize($width, $height) {
        // utworz nowy obrazek
        if (!is_resource($newImage = $this->_imagecreate($width, $height))) {
            $message = 'Image not created';
            require_once 'KontorX/Image/Exception.php';
            throw new KontorX_Image_Exception($message);
        }

        // zmien rozmiar obrazka
        $result = imageCopyResampled($newImage, $this->_image, 0, 0, 0, 0, $width, $height, $this->_width, $this->_height);
        
        if(!$result) {
            $message = 'Image not resampled';
            require_once 'KontorX/Image/Exception.php';
            throw new KontorX_Image_Exception($message);
        }

        imagedestroy($this->_image);

        $this->_image  = $newImage;
        $this->_width  = $width;
        $this->_height = $height;

        return $this;
    }

    /**
     * @param integer $width
     * @param integer $height
     * @return KontorX_Image_Abstract
     */
    public function resizeToMax($width, $height) {
        // nie przekroczono wartosci maksymalnej
        if ($width > $this->_width) {
            $newWidht = $this->_width;
            $newHeight = $this->_height;
        }
        // wartosc maksymalna zostala przekroczona
        else {
            $newWidht = $width;
            $newHeight = round($width / $this->_width * $this->_height);
        }

        // przekroczono wartosc maksymalną
        if ($height < $newHeight) {
            $newHeight = $height;
            $newWidht = round($height / $this->_height * $this->_width);
        }

        return $this->resize($newWidht, $newHeight);
    }

    /**
     * @param integer $width
     * @return KontorX_Image_Abstract
     */
    public function resizeToWidth($width) {
        $height = round($width / $this->_width * $this->_height);
        return $this->resize($width, $height);
    }

    /**
     * @param integer $height
     * @return KontorX_Image_Abstract
     */
    public function resizeToHeight($height) {
        $width = round($height / $this->_height * $this->_width);
        return $this->resize($width, $height);
    }

    /**
     * @param integer $width
     * @return KontorX_Image_Abstract
     */
    public function resizeToMaxWidth($width) {
        // nie przekroczono wartosci maksymalnej
        if ($width > $this->_width) {
            $width = $this->_width;
            $height = $this->_height;
        }
        // wartosc maksymalna zostala przekroczona
        else {
            $height = round($width / $this->_width * $this->_height);
        }

        return $this->resize($width, $height);
    }

    /**
     * @param integer $height
     * @return KontorX_Image_Abstract
     */
    public function resizeToMaxHeight($height) {
        // nie przekroczono wartosci maksymalnej
        if ($height > $this->_height) {
            $width = $this->_width;
            $height = $this->_height;
        }
        // wartosc maksymalna zostala przekroczona
        else {
            $width = round($height / $this->_height * $this->_width);
        }

        return $this->resize($width, $height);
    }
    
	/**
     * @param integer $width
     * @param integer $height
     * @return KontorX_Image_Abstract
     */
    public function resizeToMin($width, $height) {
        // nie przekroczono wartosci minimalnej
        if ($width < $this->_width) {
            $newWidht = $this->_width;
            $newHeight = $this->_height;
        }
        // wartosc minimalna zostala przekroczona
        else {
            $newWidht = $width;
            $newHeight = round($width / $this->_width * $this->_height);
        }

        // przekroczono wartosc minimalną
        if ($height > $newHeight) {
            $newHeight = $height;
            $newWidht = round($height / $this->_height * $this->_width);
        }

        return $this->resize($newWidht, $newHeight);
    }

	/**
     * @param integer $width
     * @return KontorX_Image_Abstract
     */
    public function resizeToMinWidth($width) {
        // nie przekroczono wartosci minimalnej
        if ($width < $this->_width) {
            $width = $this->_width;
            $height = $this->_height;
        }
        // wartosc minimalna zostala przekroczona
        else {
            $height = round($width / $this->_width * $this->_height);
        }

        return $this->resize($width, $height);
    }

    /**
     * @param integer $height
     * @return KontorX_Image_Abstract
     */
    public function resizeToMinHeight($height) {
        // nie przekroczono wartosci minimalnej
        if ($height < $this->_height) {
            $width = $this->_width;
            $height = $this->_height;
        }
        // wartosc minimalna zostala przekroczona
        else {
            $width = round($height / $this->_height * $this->_width);
        }

        return $this->resize($width, $height);
    }
    
    /**
     * @param integer $offsetWidth
     * @param integer $offsetHeight
     * @param integer $width
     * @param integer $height
     * @return KontorX_Image_Abstract
     */
    public function crop($offsetWidth, $offsetHeight, $width, $height) {
        // utworz nowy obrazek
        if (!is_resource($newImage = $this->_imagecreate($width, $height))) {
            $message = 'Image not created';
            throw new KontorX_Image_Exception($message);
        }


        // if offset is % then calculate offset width
    	if (substr($offsetWidth,-1) == '%') {
    		$offsetWidth = substr($offsetWidth, 0, -1) / 100;
    		$offsetWidth = ($offsetWidth * $this->_width) - ($offsetWidth * $width) ;
        }
        // if offset is % then calculate offset height
    	if (substr($offsetHeight,-1) == '%') {
    		$offsetHeight = substr($offsetHeight, 0, -1) / 100;
    		$offsetHeight = ($offsetHeight * $this->_height) - ($offsetHeight * $height) ;
        }
        
        // zmien rozmiar obrazka
        $result = imageCopy($newImage, $this->_image, 0, 0, $offsetWidth, $offsetHeight, $width, $height);

        if(!$result) {
            $message = 'Image not resampled';
            throw new KontorX_Image_Exception($message);
        }

        imagedestroy($this->_image);

        $this->_image = $newImage;
        $this->_width  = $width;
        $this->_height = $height;

        return $this;
    }

    /**
     * @return KontorX_Image_Abstract
     */
    public function grayscale() {
        imagefilter($this->_image, IMG_FILTER_GRAYSCALE);
        return $this;
    }

    /**
     * @return KontorX_Image_Abstract
     */
    public function brightness($iLevel) {
        imagefilter($this->_image, IMG_FILTER_BRIGHTNESS, $iLevel);
        return $this;
    }

    /**
     * @return KontorX_Image_Abstract
     */
    public function emboss() {
        imagefilter($this->_image, IMG_FILTER_EMBOSS);
        return $this;
    }

    /**
     * @return KontorX_Image_Abstract
     */
    public function negate() {
        imagefilter($this->_image, IMG_FILTER_NEGATE);
        return $this;
    }

    /**
     * @return KontorX_Image_Abstract
     */
    public function smooth() {
        imagefilter($this->_image, IMG_FILTER_SMOOTH, $iLevel);
        return $this;
    }

    /**
     * @return KontorX_Image_Abstract
     */
    public function colorize($iRed = 0, $iGreen = 0, $iBlue = 0) {
        imagefilter($this->_image, IMG_FILTER_COLORIZE, $iRed, $iGreen, $iBlue);
        return $this;
    }

    /**
     * @return KontorX_Image_Abstract
     */
    public function edgeDetect() {
        imagefilter($this->_image, IMG_FILTER_EDGEDETECT);
        return $this;
    }

    /**
     * @return KontorX_Image_Abstract
     */
    public function rotate($angle, $backgroundColor = 0, $ignoreTransparent = 0)
    {
    	$angle = (float) $angle;
    	$backgroundColor = (int) $backgroundColor;
    	$ignoreTransparent = (int) $ignoreTransparent;

    	if (!($image = imagerotate($this->_image, $angle, $backgroundColor, $ignoreTransparent)))
    	{
    		require_once 'KontorX/Image/Exception.php';
    		throw new KontorX_Image_Exception('imagerotate return false');
    	}

    	$this->_image = $image;
    	unset($image);

    	return $this;
    }
    
    /**
     * @param string $filename
     * @param integer $type
     * @param integer $quality
     * @return void
     */
    public function save($filename = null, $type = IMAGETYPE_JPEG, $quality = null) {
        if (null === $filename) {
            $filename = $this->_pathname;
        }
        $this->_image($this->_image, $filename, $type, $quality);
    }

    /**
     * @param integer $type
     * @param integer $quality
     * @param bool $capture
     * @return mixed
     */
    public function display($type = null, $quality = self::IMAGE_QUALITY, $capture = false) {
    	if (null === $type) {
    		$type = $this->_type;
    	}

        // wyslij naglowki
        if ($capture !== true) {
            header('Content-type:' . image_type_to_mime_type($type));
            $this->_image($this->_image, null, $type, $quality);
        } else {
            return $this->toString();
        }
    }

    /**
     * @return string
     */
    public function toString() {
        ob_start();
        $this->_image($this->_image, null, $this->_type);
        $sReturn = ob_get_clean();
        return $sReturn;
    }

    /**
     * @return string
     */
    public function __toString() {
        return $this->toString();
    }

    /**
     * @param string $pathname
     * @return resource
     */
    protected function _imageCreateFrom($pathname) {
        switch($this->_type) {
            case IMAGETYPE_PNG: return imageCreateFromPng($pathname);
            case IMAGETYPE_GIF: return imageCreateFromGif($pathname);
            case IMAGETYPE_JPEG: return imageCreateFromJpeg($pathname);
            default:
                $message = "Image type '".image_type_to_mime_type($this->_type)."' is not suported";
                require_once 'KontorX/Image/Exception.php';
                throw new KontorX_Image_Exception($message);
        }
    }

    /**
     * @param integer $width
     * @param integer $height
     * @return resource
     */
    protected function _imagecreate($width, $height) {
        return function_exists('imagecreatetruecolor')
            ? imageCreateTrueColor($width, $height)
            : imageCreate($width, $height);
    }

    /**
     * @param string $source
     * @param string $filename
     * @param integer $quality
     * @return bool
     */
    protected function _image($source, $filename = null, $type = null, $quality = null) {
        $result = null;
        switch($type) {
            case IMAGETYPE_PNG:
                $result = (null === $filename)
                    ? imagePng($source)
                    : imagePng($source, $filename);
                break;
            case IMAGETYPE_GIF:
                $result = (null === $filename)
                    ? imageGif($source)
                    : imageGif($source, $filename);
                break;
            default:
            case IMAGETYPE_JPEG:
                if (!is_integer($quality)) {
                    $quality = self::IMAGE_QUALITY;
                }

                $result = imageJpeg($source, $filename, $quality);
                break;
        }
        return $result;
    }

    /**
     * @param string $mime
     * @return string
     */
    static public function getImageTypeFromMimeType($mime) {
        switch(strtolower($mime)) {
            case 'image/gif': 	return IMAGETYPE_GIF;
            case 'image/jpeg': 	return IMAGETYPE_JPEG;
            case 'image/png': 	return IMAGETYPE_PNG;
            case 'image/psd': 	return IMAGETYPE_PSD;
            case 'image/bmp': 	return IMAGETYPE_BMP;
            case 'image/tiff': 	return IMAGETYPE_TIFF_II;
            case 'image/jp2': 	return IMAGETYPE_JP2;
            case 'image/iff': 	return IMAGETYPE_IFF;
            case 'image/xbm': 	return IMAGETYPE_XBM;
            case 'image/vnd.wap.wbmp': return IMAGETYPE_WBMP;
            case 'application/octet-stream': return IMAGETYPE_JPC;
            case 'application/x-shockwave-flash': return IMAGETYPE_SWF;
        }

        return null;
    }
}

if (!function_exists('imagerotate'))
{
	/**
	 * @link http://php.mainseek.com/manual/en/function.imagerotate.php#93151
	 * 
	 * @param unknown_type $srcImg
	 * @param unknown_type $angle
	 * @param unknown_type $bgcolor
	 * @param unknown_type $ignore_transparent
	 * @return number|number|unknown|resource
	 */
	function imagerotate($srcImg, $angle, $bgcolor, $ignore_transparent = 0)
	{
	    function rotateX($x, $y, $theta)
	    {
	        return $x * cos($theta) - $y * sin($theta);
	    }

	    function rotateY($x, $y, $theta)
	    {
	        return $x * sin($theta) + $y * cos($theta);
	    }
	
	    $srcw = imagesx($srcImg);
	    $srch = imagesy($srcImg);
	
	    //Normalize angle
	    $angle %= 360;
	    //Set rotate to clockwise
	    $angle = -$angle;
	
	    if($angle == 0) {
	        if ($ignore_transparent == 0) {
	            imagesavealpha($srcImg, true);
	        }
	        return $srcImg;
	    }
	
	    // Convert the angle to radians
	    $theta = deg2rad ($angle);
	
	    //Standart case of rotate
	    if ( (abs($angle) == 90) || (abs($angle) == 270) ) {
	        $width = $srch;
	        $height = $srcw;
	        if ( ($angle == 90) || ($angle == -270) ) {
	            $minX = 0;
	            $maxX = $width;
	            $minY = -$height+1;
	            $maxY = 1;
	        } else if ( ($angle == -90) || ($angle == 270) ) {
	            $minX = -$width+1;
	            $maxX = 1;
	            $minY = 0;
	            $maxY = $height;
	        }
	    } else if (abs($angle) === 180) {
	        $width = $srcw;
	        $height = $srch;
	        $minX = -$width+1;
	        $maxX = 1;
	        $minY = -$height+1;
	        $maxY = 1;
	    } else {
	        // Calculate the width of the destination image.
	        $temp = array (rotateX(0, 0, 0-$theta),
	        rotateX($srcw, 0, 0-$theta),
	        rotateX(0, $srch, 0-$theta),
	        rotateX($srcw, $srch, 0-$theta)
	        );
	        $minX = floor(min($temp));
	        $maxX = ceil(max($temp));
	        $width = $maxX - $minX;
	
	        // Calculate the height of the destination image.
	        $temp = array (rotateY(0, 0, 0-$theta),
	        rotateY($srcw, 0, 0-$theta),
	        rotateY(0, $srch, 0-$theta),
	        rotateY($srcw, $srch, 0-$theta)
	        );
	        $minY = floor(min($temp));
	        $maxY = ceil(max($temp));
	        $height = $maxY - $minY;
	    }
	
	    $destimg = imagecreatetruecolor($width, $height);
	    
	    /**
    	 * Dodanie dodatkowej obsługi:
    	 * 
    	 * 1. sprawdź czy ignorować przeźroczystość
    	 *    (nie będą ignorowane jeżeli ustawiono wartość 0)
    	 * 2. sprawdz czy wersja PHP jest większa niż 5.1
    	 *    bo PHP < od 5.1 nie obsługuje $ignoreTransparent
    	 */
		if ($ignore_transparent == 0) {
	        $temp = imagecolorallocatealpha($destimg, 255,255, 255, 127);
	        imagefill($destimg, 0, 0, $temp);
	        //if set the default color or white or magic pink then use transparent color
	        if ( ($bgcolor == 0) || ($bgcolor == 16777215) || ($bgcolor == 16711935) )  {
	            $bgcolor = $temp;
	        }
	        imagesavealpha($destimg, true);
	    }
	
	    // sets all pixels in the new image
	    for($x=$minX; $x<$maxX; $x++) {
	        for($y=$minY; $y<$maxY; $y++) {
	            // fetch corresponding pixel from the source image
	            $srcX = round(rotateX($x, $y, $theta));
	            $srcY = round(rotateY($x, $y, $theta));
	            if($srcX >= 0 && $srcX < $srcw && $srcY >= 0 && $srcY < $srch) {
	                $color = imagecolorat($srcImg, $srcX, $srcY );
	            } else {
	                $color = $bgcolor;
	            }
	            imagesetpixel($destimg, $x-$minX, $y-$minY, $color);
	        }
	    }
	    return $destimg;
	}
}