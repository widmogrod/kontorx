<?php
/**
 * @name KontorX_File_Write
 * @author gabriel
 */
class KontorX_File_Write {
    const CHMOD_DIR = 'DIR';
    const CHMOD_FILE = 'FILE';

    /**
     * @param Zend_Config|mixed $options
     */
    public function __construct($options = null) {
        if (is_bool($options)) {
            $this->setForce($options);
        } elseif (is_string($options)) {
            $this->setBasedir($options);
        } elseif (is_array($options)) {
            $this->setOptions($options);
        } elseif ($options instanceof Zend_Config) {
            $this->setConfig($options);
        }
    }

    /**
     * @param Zend_Config $config
     * @return KontorX_File_Write
     */
    public function setConfig(Zend_Config $config) {
        $this->setOptions($config->toArray());
    }

    /**
     * @param array $options
     * @return KontorX_File_Write
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
     * @var bool
     */
    private $_force = null;

    /**
     * @param bool $flag
     * @return KontorX_File_Write
     */
    public function setForce($flag = true) {
        $this->_force = (bool) $flag;
        return $this;
    }

    /**
     * @return bool
     */
    public function isForced() {
        return ($this->_force === true) ? true : false;
    }
    
    /**
     * @var string
     */
    private $_basedir = null;

    /**
     * @param string $basedir
     * @return KontorX_File_Write
     */
    public function setBasedir($basedir) {
        if (!is_dir($basedir)) {
            $message = "Basedit '$basedir' is not dir";
            require_once 'KontorX/File/Exception.php';
            throw new KontorX_File_Exception($message);
        }

        $this->_basedir = (string) $basedir;
        return $this;
    }

    /**
     * @return string
     */
    public function getBasedir() {
        return $this->_basedir;
    }

    /**
     * @var string
     */
    private $_pathname = null;

    /**
     * @param string $basedir
     * @return KontorX_File_Write
     */
    public function setPathname($pathname) {
        $this->_pathname = (string) $pathname;
        return $this;
    }

    /**
     * @return string
     */
    public function getPathname() {
        return $this->_pathname;
    }

    /**
     * @var array
     */
    private $_chmod = array(
        self::CHMOD_DIR => 0777,
        self::CHMOD_FILE => 0666
    );

    /**
     * @param array $mods
     * @return KontorX_File_Write
     */
    public function setChmods(array $mods) {
        foreach ($mods as $type => $mode) {
            $this->setChmod($mode, strtoupper($type));
        }
        return $this;
    }

    /**
     * @param numeric $mode
     * @param string $type
     * @return KontorX_File_Write
     */
    public function setChmod($mode, $type = null) {
        if (!is_numeric($mode)) {
            $message = "Mode is not numeric";
            require_once 'KontorX/File/Exception.php';
            throw new KontorX_File_Exception($message);
        }

        if (null === $type) {
            $this->_chmod[self::CHMOD_DIR] = $mode;
            $this->_chmod[self::CHMOD_FILE] = $mode;
        } elseif (array_key_exists($type, $this->_chmod)){
             $this->_chmod[$type] = $mode;
        } else {
            $message = "Chmod type do not exsists";
            require_once 'KontorX/File/Exception.php';
            throw new KontorX_File_Exception($message);
        }

        return $this;
    }

    /**
     * @param string $type
     * @return integer
     */
    public function getChmod($type) {
        switch ($type) {
            case self::CHMOD_DIR:
            case self::CHMOD_FILE:
                return $this->_chmod[$type];
                break;
        }
    }

    /**
     * @var KontorX_Filter_Pathname
     */
    protected $_filterPathname = null;

    /**
     * @param string $value
     * @return string
     */
    protected function _filterPathname($value) {
        if (null === $this->_filterPathname) {
            require_once 'KontorX/Filter/Pathname.php';
            $this->_filterPathname = new KontorX_Filter_Pathname();
        }
        return $this->_filterPathname->filter($value);
    }

    /**
     * @param string $pathname
     * @param string $data
     * @return void
     */
    public function write($pathname = null, $data = null) {
        if (null === $pathname) {
            if (null === ($pathname = $this->getPathname())) {
                $message = "Pathname is not set";
                require_once 'KontorX/File/Exception.php';
                throw new KontorX_File_Exception($message);
            }
        }

        $fullpath = $this->getBasedir() . DIRECTORY_SEPARATOR . $pathname;
        $fullpath = $this->_filterPathname($fullpath);

        if (null !== $data) {
            $pathname = dirname($fullpath);
        }
        
        if (!is_dir($pathname)) {
            if (!(@mkdir($pathname, $this->getChmod(self::CHMOD_DIR), $this->isForced()))) {
                $message = sprintf('Canot create directory "%s"', $pathname);
                if (function_exists('error_get_last')) {
                    $error = error_get_last();
                    $message .= ', ' . $error['message'];
                }
                require_once 'KontorX/File/Exception.php';
                throw new KontorX_File_Exception($message);
            }
        }

        if (($newFile = is_file($fullpath))) {
            if (!is_writable($fullpath)) {
                $message = "File '$fullpath' is not writable";
                require_once 'KontorX/File/Exception.php';
                throw new KontorX_File_Exception($message);
            }
            if (!$this->isForced()) {
                $message = "File '$fullpath' exsists, set 'force' flag to true to overwrite";
                require_once 'KontorX/File/Exception.php';
                throw new KontorX_File_Exception($message);
            }
        }

        if (!(@file_put_contents($fullpath, $data))) {
            $message = "Error occure during saving data to file '$fullpath'";
            if (function_exists('error_get_last')) {
                $error = error_get_last();
                $message .= ', ' . $error['message'];
            }
            require_once 'KontorX/File/Exception.php';
            throw new KontorX_File_Exception($message);
        }

        if ($newFile) {
            @chmod($fullpath, $this->getChmod(self::CHMOD_FILE));
        }
    }
}
