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
     * Wczytanie grafiki
     * @param string $pathname
     */
    public function load($pathname) {
        // sprawdz czy istnieje plik
        if (!file_exists($pathname)) {
            $message = "File '$pathname' does not exists";
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
        // sprawdz czy zaladowano grafike
        if (!$this->_image) {
            $message = 'Image not loaded';
            require_once 'KontorX/Image/Exception.php';
            throw new KontorX_Image_Exception($message);
        }

        // utworz nowy obrazek
        if (($newImage = $this->_imagecreate($width, $height))) {
            $message = 'Image not created';
            require_once 'KontorX/Image/Exception.php';
            throw new KontorX_Image_Exception($message);
        }

        // zmien rozmiar obrazka
        $result = imageCopyResampled($rNewImage, $this->_image, 0, 0, 0, 0, $width, $height, $this->_width, $this->_height);

        if(!$result) {
            $message = 'Image not resampled';
            require_once 'KontorX/Image/Exception.php';
            throw new KontorX_Image_Exception($message);
        }

        imagedestroy($this->_image);

        $this->_image  = $rNewImage;
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
            $iNewWidth = $this->_width;
            $iNewHeight = $this->_height;
        }
        // wartosc maksymalna zostala przekroczona
        else {
            $iNewWidth = $width;
            $iNewHeight = round($width / $this->_width * $this->_height);
        }

        // przekroczono wartosc maksymalną
        if ($height < $iNewHeight) {
            $iNewHeight = $height;
            $iNewWidth = round($height / $this->_height * $this->_width);
        }

        return $this->resize($iNewWidth, $iNewHeight);
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
     * @param integer $offsetWidth
     * @param integer $offsetHeight
     * @param integer $width
     * @param integer $height
     * @return KontorX_Image_Abstract
     */
    public function crop($offsetWidth, $offsetHeight, $width, $height) {
        // sprawdz czy zaladowano grafike
        if(!$this->_image) {
            $message = 'Image not loaded';
            require_once 'KontorX/Image/Exception.php';
            throw new KontorX_Image_Exception($message);
        }

        // utworz nowy obrazek
        if (($newImage = $this->_imagecreate($width, $height))) {
            $message = 'Image not created';
            throw new KontorX_Image_Exception($message);
        }

        // zmien rozmiar obrazka
        $result = imageCopy($rNewImage, $this->_image, 0, 0, $offsetWidth, $offsetHeight, $width, $height);

        if(!$result) {
            $message = 'Image not resampled';
            throw new KontorX_Image_Exception($message);
        }

        imagedestroy($this->_image);

        $this->_image = $rNewImage;
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
     * zapisz grafike do pliku
     *
     * @param string $filename
     * @param integer $type
     * @param integer $quality
     * @return KontorX_Image_Abstract
     */
    public function save($filename, $type = IMAGETYPE_JPEG, $quality = null) {
        return $this->_image($this->_image, $filename, $type, $quality);
    }

    /**
     * @param integer $type
     * @param integer $quality
     * @param bool $capture
     * @return void
     */
    public function display($type = IMAGETYPE_JPEG, $quality = self::IMAGE_QUALITY, $capture = false) {
        // wyslij naglowki
        if ($capture !== true) {
            header('Content-type:' . image_type_to_mime_type($type));
            $this->_image($this->_image, null, $type, $quality);
        } else {
            ob_start();
            $this->_image($this->_image, null, $type, $quality);
            $sReturn = ob_get_contents();
            ob_end_clean();
            return $sReturn;
        }
    }

    /**
     * @param string $pathname
     * @return void
     */
    protected function _imageCreateFrom($pathname) {
        switch($this->_type) {
            case IMAGETYPE_PNG: return imageCreateFromPng($pathname);
            case IMAGETYPE_GIF: return imageCreateFromGif($pathname);
            case IMAGETYPE_JPEG: return imageCreateFromJpeg($pathname);
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
    protected function _image($source, $filename = null, $type = IMAGETYPE_JPEG, $quality = self::IMAGE_QUALITY) {
        // wyslij grafike
        switch($type) {
            case IMAGETYPE_PNG:
                return empty($filename)
                ? imagePng($source)
                : imagePng($source, $filename);

            case IMAGETYPE_GIF:
                return empty($filename)
                ? imageGif($source)
                : imageGif($source, $filename);

            case IMAGETYPE_JPEG:
                $quality = ($quality > 0 && $quality < 100) ? $quality : self::IMAGE_QUALITY ;
                return empty($filename)
                ? imageJpeg($source, null, $quality)
                : imageJpeg($source, $filename, $quality);
        }
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