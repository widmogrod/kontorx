<?php
abstract class KontorX_Image_Abstract {
	const
		image_quality = 85;		// domyslna jakosc grafiki

	private
		// $_sPath = null,
		// $_sName = null,
		$_iType = null,			// typ pliku
		$_sMime = null,			// mime pliku
		$_iWidth = null,		// szerokosc pliku
		$_iHeight = null,		// wysokosc pliku
		$_rSource = null;		// uchwyt pliku

	/**
	 * konstruktor
	 *
	 * @param 	$sPath		string
	 */
	public function __construct( $sPath = null ){
		if( !is_null( $sPath ))
			$this->load( $sPath );
	}

	/**
	 * zaladuj grafike
	 *
	 * @param 	$sPath		string
	 */
	public function load( $sPath ){
		// sprawdz czy istnieje plik
		if( !file_exists( $sPath )){
			$sError = 'file `'.$sPath.'` does not exists';
			throw new KontorX_Image_Exception( $sError );
		}

		// pobierz informacje o grafice
		$aImage = getimagesize( $sPath );
		$this->_iWidth	= $aImage[0];
		$this->_iHeight	= $aImage[1];
		$this->_iType	= $aImage[2];
		$this->_sMime	= $aImage['mime'];

		$this->_rSource = $this->_imageCreateFrom( $sPath );
	}

	/**
	 * zwroc typ pliku graficznego
	 * 
	 * @return 	integer
	 */
	public function getType(){
		return $this->_iType;
	}

	/**
	 * zwroc mime pliku
	 * 
	 * @return 	string
	 */
	public function getMime(){
		return $this->_sMime;
	}
	
	/**
	 * zmien rozmiar grafiki
	 *
	 * @param 	$iWidth		integer
	 * @param 	$iHeight	integer
	 * @return 	object
	 */
	public function resize( $iWidth, $iHeight ){
		// sprawdz czy zaladowano grafike
		if( !$this->_rSource ){
			$sError = 'image has not been loaded';
			throw new KontorX_Image_Exception( $sError );
		}

		// utworz nowy obrazek
		if( !$rNewImage = $this->_imagecreate( $iWidth, $iHeight )){
			$sError = 'image has not been created';
			throw new KontorX_Image_Exception( $sError );
		}

		// zmien rozmiar obrazka
		$bResult = imageCopyResampled( $rNewImage, $this->_rSource, 0, 0, 0, 0, $iWidth, $iHeight, $this->_iWidth, $this->_iHeight );

		if( !$bResult ){
			$sError = 'image has not been resampled';
			throw new KontorX_Image_Exception( $sError );
		}

		imagedestroy( $this->_rSource );

		$this->_rSource = $rNewImage;
		$this->_iWidth  = $iWidth;
		$this->_iHeight = $iHeight;

		return $this;
	}

	/**
	 * zmien rozmiar grafiki nie wikekszych niz wartosci maksymalnych
	 *
	 * @param 	$iWidth		integer
	 * @param 	$iHeight	integer
	 * @return 	object
	 */
	public function resizeToMax( $iWidth, $iHeight ){
		// nie przekroczono wartosci maksymalnej
		if( $iWidth > $this->_iWidth ){
			$iNewWidth = $this->_iWidth;
			$iNewHeight = $this->_iHeight;
		}
		// wartosc maksymalna zostala przekroczona
		else {
			$iNewWidth = $iWidth;
			$iNewHeight = round( $iWidth / $this->_iWidth * $this->_iHeight );
		}

		// przekroczono wartosc maksymalną
		if( $iHeight < $iNewHeight ){
			$iNewHeight = $iHeight;
			$iNewWidth = round( $iHeight / $this->_iHeight * $this->_iWidth );
		}

		return $this->resize( $iNewWidth, $iNewHeight );
	}

	/**
	 * zmien rozmiar grafiki do szerokosci
	 *
	 * @param 	$iWidth		integer
	 * @return 	bool
	 */
	public function resizeToWidth( $iWidth ){
		$iHeight = round( $iWidth / $this->_iWidth * $this->_iHeight );
		return $this->resize( $iWidth, $iHeight );
	}

	/**
	 * zmien rozmiar grafiki do wysokosci
	 *
	 * @param 	$iHeight	integer
	 * @return 	object
	 */
	public function resizeToHeight( $iHeight ){
		$iWidth = round( $iHeight / $this->_iHeight * $this->_iWidth );
		return $this->resize( $iWidth, $iHeight );
	}

	/**
	 * zmien rozmiar grafiki do maksymalnej szerokosci
	 *
	 * @param 	$iWidth		integer
	 * @return 	bool
	 */
	public function resizeToMaxWidth( $iWidth ){
		// nie przekroczono wartosci maksymalnej
		if( $iWidth > $this->_iWidth ){
			$iWidth = $this->_iWidth;
			$iHeight = $this->_iHeight;
		}
		// wartosc maksymalna zostala przekroczona
		else {
			$iHeight = round( $iWidth / $this->_iWidth * $this->_iHeight );
		}

		return $this->resize( $iWidth, $iHeight );
	}

	/**
	 * zmien rozmiar grafiki do maksymalnej wysokosci
	 *
	 * @param 	$iHeight	integer
	 * @return 	bool
	 */
	public function resizeToMaxHeight( $iHeight ){
		// nie przekroczono wartosci maksymalnej
		if( $iHeight > $this->_iHeight ){
			$iWidth = $this->_iWidth;
			$iHeight = $this->_iHeight;
		}
		// wartosc maksymalna zostala przekroczona
		else {
			$iWidth = round( $iHeight / $this->_iHeight * $this->_iWidth );
		}

		return $this->resize( $iWidth, $iHeight );
	}

	public function crop( $iX, $iY, $iWidth, $iHeight ){
		// sprawdz czy zaladowano grafike
		if( !$this->_rSource ){
			$sError = 'image has not been loaded';
			throw new KontorX_Image_Exception( $sError );
		}

		// utworz nowy obrazek
		if( !$rNewImage = $this->_imagecreate( $iWidth, $iHeight )){
			$sError = 'image has nit been created';
			throw new KontorX_Image_Exception( $sError );
		}

		// zmien rozmiar obrazka
		$bResult = imageCopy( $rNewImage, $this->_rSource, 0, 0, $iX, $iY, $iWidth, $iHeight );

		if( !$bResult ){
			$sError = 'image has not been resampled';
			throw new KontorX_Image_Exception( $sError );
		}

		imagedestroy( $this->_rSource );

		$this->_rSource = $rNewImage;
		$this->_iWidth  = $iWidth;
		$this->_iHeight = $iHeight;

		return $this;
	}

	/**
	 * Filtry na grafike
	 */

	public function grayscale(){
		imagefilter( $this->_rSource, IMG_FILTER_GRAYSCALE );
		return $this;
	}

	public function brightness( $iLevel ){
		imagefilter( $this->_rSource, IMG_FILTER_BRIGHTNESS, $iLevel );
		return $this;
	}

	public function emboss(){
		imagefilter( $this->_rSource, IMG_FILTER_EMBOSS );
		return $this;
	}

	public function negate(){
		imagefilter( $this->_rSource, IMG_FILTER_NEGATE );
		return $this;
	}

	public function smooth(){
		imagefilter( $this->_rSource, IMG_FILTER_SMOOTH, $iLevel );
		return $this;
	}

	public function colorize( $iRed = 0, $iGreen = 0, $iBlue = 0 ){
		imagefilter( $this->_rSource, IMG_FILTER_COLORIZE, $iRed, $iGreen, $iBlue );
		return $this;
	}

	public function edgeDetect(){
		imagefilter( $this->_rSource, IMG_FILTER_EDGEDETECT );
		return $this;
	}

	/**
	 * zapisz grafike do pliku
	 *
	 * @param 	$sFile		string
	 * @param 	$iType		integer
	 * @param 	$iQuality	integer
	 * @return 	object
	 */
	public function save( $sFile, $iType = IMAGETYPE_JPEG, $iQuality = null ){
		return $this->_image( $this->_rSource, $sFile, $iType, $iQuality );
	}

	/**
	 * wyswietl grafike
	 *
	 * @param 	$iType 		integer
	 * @param 	$iQuality	integer
	 */
	public function display( $iType = IMAGETYPE_JPEG, $iQuality = self::image_quality, $bReturn = false ){
		// wyslij naglowki
		if((bool) !$bReturn ){
			header( 'Content-type:' . image_type_to_mime_type( $iType ));
			$this->_image( $this->_rSource, null, $iType, $iQuality );
		} else {
			ob_start();
			$this->_image( $this->_rSource, null, $iType, $iQuality );
			$sReturn = ob_get_contents();
			ob_end_clean();
			return $sReturn;
		}
	}

	/**
	 * @param 	$sPath		string
	 */
	protected function _imageCreateFrom( $sPath ){
		switch( $this->_iType ){
			case IMAGETYPE_PNG: return imageCreateFromPng( $sPath );
			case IMAGETYPE_GIF: return imageCreateFromGif( $sPath );
			case IMAGETYPE_JPEG: return imageCreateFromJpeg( $sPath );
		}
	}

	/**
	 * @param 	$iWidth		integer
	 * @param 	$iHeight	integer
	 * @return 	resource
	 */
	protected function _imagecreate( $iWidth, $iHeight ){
		return function_exists('imagecreatetruecolor')
			? imageCreateTrueColor( $iWidth, $iHeight )
			: imageCreate( $iWidth, $iHeight );
	}

	/**
	 * @param  	$rSource 	string
	 * @param 	$sFilename	string
	 * @param 	$iQuality	integer
	 * @return 	bool
	 */
	protected function _image( $rSource, $sFilename = null, $iType = IMAGETYPE_JPEG, $iQuality = self::image_quality ){
		// wyslij grafike
		switch( $iType ){
			case IMAGETYPE_PNG:
				return empty( $sFilename )
					? imagePng( $rSource )
					: imagePng( $rSource, $sFilename );

			case IMAGETYPE_GIF:
				return empty( $sFilename )
					? imageGif( $rSource )
					: imageGif( $rSource, $sFilename );

			case IMAGETYPE_JPEG:
				$iQuality = ( $iQuality > 0 && $iQuality < 100 ) ? $iQuality : self::image_quality ;
				return empty( $sFilename )
					? imageJpeg( $rSource, null, $iQuality )
					: imageJpeg( $rSource, $sFilename, $iQuality );

			default: return false;
		}
	}

	/**
	 * zwraca numer typu pliku na podstawie nazwy mime
	 *
	 * @param 	$sMime		string
	 * @return 	mixed
	 */
	static public function getImageTypeFromMimeType( $sMime ){
		switch( strtolower( $sMime )){
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
			//case 'application/octet-stream': return IMAGETYPE_JPX;
			case 'application/octet-stream': return IMAGETYPE_JPC;
			case 'application/x-shockwave-flash': return IMAGETYPE_SWF;
		}

		return false;
	}
}