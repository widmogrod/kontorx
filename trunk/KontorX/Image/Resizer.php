<?php
class KontorX_Image_Resizer {

    const RESIZE_MAX = 'MAX';
    const RESIZE_MIN = 'MIN';
    const RESIZE_CROP = 'CROP';
    const RESIZE_DEFAULT = 'DEFAULT';

    /**
     * @var array
     */
    protected $_avalibleTypes = array(
        self::RESIZE_MAX,
        self::RESIZE_MIN, 
        self::RESIZE_CROP, 
        self::RESIZE_DEFAULT
    );

    /**
     * @param mixed $options
     * @return void
     */
    public function  __construct($options = null) {
        if (is_array($options)) {
            $this->setOptions($options);
        } elseif ($options instanceof Zend_Config) {
            $this->setConfig($options);
        }
    }

    /**
     * @var Zend_Config
     */
    protected $_config = null;

    /**
     * @param Zend_Config $config
     * @return void
     */
    public function setConfig(Zend_Config $config) {
        $this->setOptions($config->toArray());
    }

    /**
     * @param array $options
     * @return void
     */
    public function setOptions(array $options) {
        foreach ($options as $name => $value) {
            $method = 'set'.ucfirst($name);
            if (method_exists($this, $method)) {
                $this->$method($value);
            }
        }
    }

    /**
     * @var integer
     */
    protected $_offsetWidth = 0;

    /**
     * @param integer $width
     * @return void
     */
    public function setOffsetWidth($width) {
        $this->_offsetWidth = $width;
    }

    /**
     * @return integer
     */
    public function getOffsetWidth() {
        return $this->_offsetWidth;
    }

    /**
     * @var integer
     */
    protected $_offsetHeight = 0;

    /**
     * @param integer $height
     * @return void
     */
    public function setOffsetHeight($height) {
        $this->_offsetHeight = $height;
    }

    /**
     * @return integer
     */
    public function getOffsetHeight() {
        return $this->_offsetHeight;
    }

    /**
     * @var integer
     */
    protected $_width = null;

    /**
     * @param integer $width
     * @return void
     */
    public function setWidth($width) {
        $this->_width = (int) $width;
    }

    /**
     * @return integer
     */
    public function getWidth() {
        return $this->_width;
    }

    /**
     * @var integer
     */
    protected $_height = null;

    /**
     * @param integer $height
     * @return void
     */
    public function setHeight($height) {
        $this->_height = (int) $height;
    }

    /**
     * @return integer
     */
    public function getHeight() {
        return $this->_height;
    }
    
    /**
     * Lista dostępnych filtrów
     * @var arary
     */
    protected $_availibleFilters = array(
    	'grayscale',
    	'emboss',
    	'negate',
    	'smooth',
    	'brightness',
    	'edgedetect'
    );
    
    /**
     * Wykorzystywany filtr 
     * @var string
     */
    protected $_filters;
    
    public function setFilters($filters) 
    {
    	$this->clearFilters();
    	foreach ($filters as $filter)
    		$this->addFilter($filter);
    }
    
	public function getFilters() 
	{
		return $this->_filters;
	}
    
	public function clearFilters() {
		$this->_filters = array();
	}
	
	public function setFilter($filter) 
    {
    	$this->addFilter($filter);
    }
    
	public function addFilter($filter) 
    {
    	$filter = strtolower($filter);
    	if (in_array($filter, $this->_availibleFilters))
    		$this->_filters[] = $filter;
    }

    /**
     * @var string
     */
    protected $_type = null;

    /**
     * @param string $type
     * @return void
     */
    public function setType($type) {
        $this->_type = strtoupper((string) $type);
    }

    /**
     * @return string
     */
    public function getType() {
        if (null === $this->_type
                || !in_array($this->_type, $this->_avalibleTypes)) {
            $this->_type = self::RESIZE_DEFAULT;
        }
        return $this->_type;
    }
    
    /**
     * @var array
     */
    protected $_chains = array();
    
    /**
     * @param array $chains
     * @return void
     */
    public function setChains(array $chains) {
    	$this->_chains = $chains;
    }

    /**
     * @return array
     */
    public function getChains() {
    	return $this->_chains;
    }

    /**
     * @return bool
     */
    public function hasChains() {
    	return (bool) count($this->_chains);
    }

    /**
     * @return void
     */
    public function clearChains() {
    	$this->_chains = array();
    }
    
    /**
     * @var string
     */
    protected $_multiType = null;

    /**
     * @param string $type
     * @return void
     */
    public function setMultiType($type) {
        $this->_multiType = (string) $type;
    }

    /**
     * @return string
     */
    public function getMultiType() {
        return $this->_multiType;
    }

    /**
     * @var array
     */
    protected $_multiTypesOptions = array();

    /**
     * @param array $multiTypes
     * @return void
     */
    public function setMultiTypesOptions(array $multiTypes) {
        $this->_multiTypesOptions = array();
        $this->addMultiTypesOptions($multiTypes);
    }

    /**
     * @return array
     */
    public function getMultiTypesOptions() {
    	return $this->_multiTypesOptions;
    }
    
    /**
     * @param string $type
     * @return array
     */
    public function getMultiTypesOption($type) {
        return isset($this->_multiTypesOptions[$type])
        	? $this->_multiTypesOptions[$type]
        	: array();
    }

    /**
     * @param array $multiTypes
     * @return void
     */
    public function addMultiTypesOptions($multiTypes) {
    	$this->_multiTypesOptions = array_merge($this->_multiTypesOptions, $multiTypes);
    }

    /**
     * @param string $type
     * @return void
     */
    protected function _setupMultiTypesOptions($type = null) {
        if (null === $type) {
            if (null === ($type = $this->getMultiType())) {
                return;
            }
        }
        $this->setOptions($this->getMultiTypesOption($type));
    }

    /**
     * @var string
     */
    protected $_dirname = null;

    /**
     * @param string $dirname
     * @return void
     */
    public function setDirname($dirname) {
        $this->_dirname = (string) $dirname;
    }

    /**
     * @return string
     */
    public function getDirname() {
        return $this->_dirname;
    }

    /**
     * @var string
     */
    protected $_filename = null;

    /**
     * @param string $filename
     * @return void
     */
    public function setFilename($filename) {
        $this->_filename = (string) $filename;
    }

    /**
     * @return string
     */
    public function getFilename() {
        return $this->_filename;
    }

    /**
     * @return string
     */
    public function getPathname() {
        return $this->getDirname() . DIRECTORY_SEPARATOR . $this->getFilename();
    }

    /**
     * @var KontorX_Image
     */
    protected $_image = null;

    /**
     * @return KontorX_Image
     */
    protected function _getImage() {
        if (null === $this->_image) {
            $pathname = $this->getPathname();

            require_once 'KontorX/Image.php';
            $this->_image = new KontorX_Image($pathname);
        }
        return $this->_image;
    }

    /**
     * @return KontorX_Image
     */
    public function resize($multiType = null) {
        $this->_setupMultiTypesOptions($multiType);

        $this->_resize();

        if ($this->hasChains()) 
        {
        	foreach ($this->getChains() as $chain) 
        	{
        		$this->setOptions($chain);
        		
        		$this->_resize();
        		$this->_filter();
        	}
        	$this->clearChains();
        }

        return $this->_getImage();
    }
    
    protected function _filter() 
    {
    	$image = $this->_getImage();
    	
    	foreach ($this->getFilters() as $filter) 
    	{
    		if (method_exists($image, $filter))
    			$image->$filter();
    	}

    	$this->clearFilters();
    }

    protected function _resize() {
    	$image = $this->_getImage();

        switch ($this->getType()) {
            default:
            case self::RESIZE_DEFAULT:
                if (is_int($this->getWidth()) && is_int($this->getHeight())) {
                    $image->resize($this->getWidth(), $this->getHeight());
                } else
                if (is_int($this->getWidth())) {
                    $image->resizeToWidth($this->getWidth());
                } else
                if (is_int($this->getHeight())) {
                    $image->resizeToHeight($this->getHeight());
                } else {
                    $message = "Width or height or both are required for resizing";
                    require_once 'KontorX/Image/Exception.php';
                    throw new KontorX_Image_Exception($message);
                }
                break;
            case self::RESIZE_MAX:
                if (is_int($this->getWidth()) && is_int($this->getHeight())) {
                    $image->resizeToMax($this->getWidth(), $this->getHeight());
                } else
                if (is_int($this->getWidth())) {
                    $image->resizeToMaxWidth($this->getWidth());
                } else
                if (is_int($this->getHeight())) {
                    $image->resizeToMaxHeight($this->getHeight());
                } else {
                    $message = "Width or height or both are required for resizing to max";
                    require_once 'KontorX/Image/Exception.php';
                    throw new KontorX_Image_Exception($message);
                }
                break;
            case self::RESIZE_MIN:
                if (is_int($this->getWidth()) && is_int($this->getHeight())) {
                    $image->resizeToMin($this->getWidth(), $this->getHeight());
                } else
                if (is_int($this->getWidth())) {
                    $image->resizeToMinWidth($this->getWidth());
                } else
                if (is_int($this->getHeight())) {
                    $image->resizeToMinHeight($this->getHeight());
                } else {
                    $message = "Width or height or both are required for resizing to min";
                    require_once 'KontorX/Image/Exception.php';
                    throw new KontorX_Image_Exception($message);
                }
                break;
            case self::RESIZE_CROP:
                if (is_int($this->getWidth()) && is_int($this->getHeight())) {
                    $image->crop($this->getOffsetWidth(), $this->getOffsetHeight(), $this->getWidth(), $this->getHeight());
                } else {
                    $message = "Width and height is required for crop";
                    require_once 'KontorX/Image/Exception.php';
                    throw new KontorX_Image_Exception($message);
                }
                break;
        }

        return $image;
    }
}