<?php
class KontorX_Update_Manager extends ArrayIterator {
	
	/**
	 * Statusy aktualizacji 
	 */
	const SUCCESS = 'SUCCESS';
	const FAILURE = 'FAILURE';
	
	/**
	 * Nazwa pliku, w którym jest zachowywana informacja o ostatnim updacie
	 * @var string
	 */
	const FILENAME_INFO = '.update';

	/**
	 * Prefix dla plików, które są skanowane jako dane zawierające update
	 * @var string
	 */
	const REGEXP_FILENAME_PREFIX = 'r';

	/**
	 * Aktualizuje
	 * @var void
	 */
	const FORCE_UPDATE = 'FORCE_UPDATE';

	/**
	 * @param Zend_Config|array|string $options
	 * @return void
	 */
	public function __construct($options = null) {
		parent::__construct(array(), self::STD_PROP_LIST);

		if ($options instanceof Zend_Config) {
			$options = $options->toArray();
		} elseif (is_string($options)) {
			$options = array(
				'updatePath' => $options
			);
		} elseif (!is_array($options)) {
			$options = array();
		}

		$this->setOptions($options);
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
	 * @var string
	 */
	protected $_updatePath;

	/**
	 * @param string $path
	 * @return KontorX_Update_Manager
	 */
	public function setUpdatePath($path) {
		$this->_updatePath = (string) $path;
		return $this;
	}

	/**
	 * @param bool $throwException
	 * @return string
	 * @throws KontorX_Update_Exception
	 */
	public function getUpdatePath($throwException = true) {
		if (!is_dir($this->_updatePath)) {
			if (!$throwException) {
				return false;
			}

			require_once 'KontorX/Update/Exception.php';
			throw new KontorX_Update_Exception(sprintf('update path "%s" do not exsists', $this->_updatePath));
		}

		return $this->_updatePath;
	}
	
	/**
	 * @var string
	 */
	protected $_regexpFilenamePrefix;
	
	/**
	 * @param string $prefix
	 * @return KontorX_Update_Manager
	 */
	public function setRegexpFilenamePrefix($prefix) {
		$this->_regexpFilenamePrefix = (string) $prefix;
		return $this;
	}
	
	/**
	 * @return string
	 */
	public function getRegexpFilenamePrefix() {
		return (null === $this->_regexpFilenamePrefix)
			? self::REGEXP_FILENAME_PREFIX
			: $this->_regexpFilenamePrefix;
	}

	/**
	 * @return string
	 */
	public function getRegexpFilenamePattern() {
		$prefix = $this->getRegexpFilenamePrefix();
		return sprintf('/^%s(\d)+/', preg_quote($prefix, '/'));
	}

	/**
	 * @return int (-1, no update)
	 */
	public function getLastUpdate() {
		$pathname = $this->getUpdatePath();
		$pathname .= DIRECTORY_SEPARATOR . self::FILENAME_INFO;

		if (!is_file($pathname)) {
			return -1;
		}

		return (false === ($lastUpdate = @file_get_contents($pathname)))
			? -1
			: $lastUpdate;
	}

	/**
	 * @param integer $updateId
	 * @return void
	 */
	protected function _saveInfo($updateId) {
		$pathname = $this->getUpdatePath();
		$pathname .= DIRECTORY_SEPARATOR . self::FILENAME_INFO;

		if (!@file_put_contents($pathname, (int) $updateId)) {
			$message = function_exists('error_get_last')
				? error_get_last()
				: sprintf('can\'t save last update info to file "%s"', $pathname);

			require_once 'KontorX/Update/Exception.php';
			throw new KontorX_Update_Exception($message);
		}
	}

	/**
	 * @var array
	 */
	protected $_updateFileList;

	/**
	 * Zwraca listę wszystkich updateów
	 * @return array
	 */
	public function getUpdateFileList() {
		if (null === $this->_updateFileList) {
			$this->_updateFileList = array();

			$lastUpdate = $this->getLastUpdate();
			
			$path = $this->getUpdatePath();
			$iterator = new DirectoryIterator($path);
			$pattern = $this->getRegexpFilenamePattern();
			while ($iterator->valid()) {
				if ($iterator->isFile()) {
					$subject = $iterator->getFilename();
					$matches = array();

					// dopasuj nazwę pliku do wymaganego formatu
					if (false !== preg_match($pattern, $subject, $matches)) {
						$key = (int) $matches[1];
						if ($key > $lastUpdate) {
							$this->_updateFileList[$key] = $subject;
						}
					}
				}
				
				$iterator->next();
			}
			
			// sortuj klucze.. od najmniejszego do największego
			ksort($this->_updateFileList);
		}
		return $this->_updateFileList;
	}

	/**
	 * @var bool
	 */
	protected $_loaded = false;

	/**
	 * Ładuje wszystkie znalezione obiekty 'KontorX_Update_Interface'
	 * @return void
	 */
	protected function _loadUpdates() {
		if ($this->_loaded) {
			$path = $this->getUpdatePath();
	
			$list = $this->getUpdateFileList();
			foreach ($list as $idx => $filename) {
				$pathname  = $path . DIRECTORY_SEPARATOR . $filename;
				$extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
	
				switch ($extension) {
					case 'php':
						// w pliku php.. musi się odwołać do managera i dodac objekt update!
						$instance = include_once $pathname;
						if (!$instance instanceof KontorX_Update_Interface) {
							require_once 'KontorX/Update/Exception.php';
	            			throw new KontorX_Update_Exception(
	            					sprintf('object "%s" is not instance of KontorX_Update_Interface',
	            							is_object($instance) ? get_class($instance) : (string) $instance));
						}
						break;
	
					default:
						$className = $this->getPluginLoader(self::FILE)->load($extension);
						$instance = new $className($pathname);
				}
				
				// dodaj update
				$this->offsetSet($idx, $instance);
			}
		}
		$this->_loaded = true;
	}

	/**
	 * @param string $flag
	 * @return void
	 */
	public function update($flag = null) {
		$this->ksort();
		$this->rewind();
		while ($this->valid()) {
			/* @var $update KontorX_Update_Interface */
			$update = $this->current();

			// update
			$update->up();

			if (self::FAILURE === $update->getStatus()) {
				if (self::FORCE_UPDATE !== $flag) {
					return false;
				}
			}
		}

		// zapisz informacje o ostatniej aktualizacji
		$this->_saveInfo($this->key());

		return true;
	}

	/**
	 * @param string $flag
	 * @return void
	 */
	public function downgrade($flag = null) {
		$this->uksort(array($this, '_cmp'));
		$this->rewind();		

		while ($this->valid()) {
			/* @var $update KontorX_Update_Interface */
			$update = $this->current();

			$update->down();

			if (self::FAILURE === $update->getStatus()) {
				if (self::FORCE_UPDATE !== $flag) {
					return false;
				}
			}
		}
		
		// zapisz informacje o ostatniej dezaktualizacji
		$this->_saveInfo($this->key());
	}

	/**
	 * @param int $a
	 * @param int $b
	 * @return void
	 */
	private function _cmp($a, $b) {
		if ($a < $b) return 1;
		if ($a > $b) return -1;
		return 0;
	}
	
	/**
	 * @param bool $grouped
	 * @return array
	 */
	public function getMessages($grouped = false) {
		$messages = array();
		foreach ($this as $updateId => $update) {
			/* @var $update KontorX_Update_Interface */
			if ($grouped) {
				$messages[$updateId] = $update->getMessages();
			} else {
				array_map('array_push', $messages, $update->getMessages());
			}
		}
		return $messages;
	}
	
	/**
     * Types of @see Zend_Loader_PluginLoader
     */
    const FILE = 'FILE';

    /**
     * @var array of @see Zend_Loader_PluginLoader
     */
    private $_pluginLoader = array();

    /**
     * Set @see Zend_Loader_PluginLoader
     * @param Zend_Loader_PluginLoader $loader
     * @param string $type
     */
    public function setPluginLoader(Zend_Loader_PluginLoader $loader, $type) {
        switch ($type) {
            case self::FILE:
                $this->_pluginLoader[$type] = $loader;
                break;
        default:
            require_once 'KontorX/Update/Exception.php';
            throw new KontorX_Update_Exception(sprintf('Invalid type "%s" provided to getPluginLoader()', $type));
        }
    }

    /**
     * Get @see Zend_Loader_PluginLoader
     *
     * @param string $type
     * @return Zend_Loader_PluginLoader
     */
    public function getPluginLoader($type = null) {
        if (!isset($this->_pluginLoader[$type])) {
            switch ($type) {
                case self::FILE:
                    $prefixSegment = 'Update_File';
                    $pathSegment   = 'Update/File';
                    break;
                default:
                    require_once 'KontorX/DataGrid/Exception.php';
                    throw new KontorX_DataGrid_Exception(sprintf('Invalid type "%s" provided to getPluginLoader()', $type));
            }

            $this->_pluginLoader[$type] = new Zend_Loader_PluginLoader(array(
                "KontorX_$prefixSegment" => "KontorX/$pathSegment"
            ));
        }

        return $this->_pluginLoader[$type];
    }

    /**
     * @param array $paths
     * @return void
     */
    public function addPrefixPaths(array $paths) {
    	foreach ($paths as $data) {
    		if (isset($data['prefix']) && isset($data['path']) && isset($data['type'])) {
    			$this->addPrefixPath($data['prefix'], $data['path'], $data['type']);
    		}
    	}
    }

    /**
     * @param string $prefix
     * @param string $path
     * @param string $type
     * @return void
     */
    public function addPrefixPath($prefix, $path, $type) {
    	$loader = $this->getPluginLoader($type);
    	$loader->addPrefixPath($prefix, $path);
    }
	
}