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
	const FORCE = 'FORCE';

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
		
		/**
		 * katalog z updatemi jest wymagany!
		 * ale można też zrobić żeby nie był i np. dodawać ręcznie updaty
		 * w szczególnych przypadkach taka opcja może być bardzo przydatna
		 * ale dopuki nie napotkałem takiego zjawiska.. nic nie zmieniam
		 */
		if (!isset($options['updatePath'])) {
			require_once 'KontorX/Update/Exception.php';
			throw new KontorX_Update_Exception('path to updates is not set');
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
	 * @throws KontorX_Update_Exception
	 */
	public function setUpdatePath($path) {
		if (!is_dir($path)) {
			require_once 'KontorX/Update/Exception.php';
			throw new KontorX_Update_Exception(sprintf('update path "%s" do not exsists', $path));
		}
		$this->_updatePath = (string) rtrim($path,'\\/') . '/';
		return $this;
	}

	/**
	 * @return string
	 */
	public function getUpdatePath() {
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
		return sprintf('/^%s([0-9]+)\.[\w_]+/', preg_quote($prefix, '/'));
	}

	/**
	 * @return int (-1, no update)
	 */
	public function getLastUpdate() {
		$pathname = $this->getUpdatePath();
		$pathname .= self::FILENAME_INFO;

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
	 * @throws KontorX_Update_Exception
	 */
	protected function _saveInfo($updateId) {
		$pathname = $this->getUpdatePath();
		$pathname .= self::FILENAME_INFO;

		if (!@file_put_contents($pathname, (int) $updateId)) {
			$message = function_exists('error_get_last')
				? error_get_last()
				: sprintf('can\'t save last update info to file "%s"', $pathname);

			require_once 'KontorX/Update/Exception.php';
			throw new KontorX_Update_Exception($message);
		}
		
		@chmod($pathname, 0777);
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

			$path = $this->getUpdatePath();
			$iterator = new DirectoryIterator($path);
			$pattern = $this->getRegexpFilenamePattern();
			while ($iterator->valid()) {
				if ($iterator->isFile()) {
					$subject = $iterator->getFilename();
					$matches = array();

					// dopasuj nazwę pliku do wymaganego formatu
					if (preg_match($pattern, $subject, $matches)) {
						$key = array_key_exists(1, $matches)
							? (int) $matches[1]
							: -1;

						$this->_updateFileList[$key] = $subject;
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
	 * @throws KontorX_Update_Exception
	 */
	protected function _loadUpdates() {
		if (!$this->_loaded) {
			$path = $this->getUpdatePath();
	
			$list = $this->getUpdateFileList();
			foreach ($list as $idx => $filename) {
				$pathname  = $path . $filename;
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
			
			$this->ksort();
		}
		$this->_loaded = true;
	}

	/**
	 * @param string $flag
	 * @return void
	 */
	public function update($flag = null) {
		// załaduj aktualizacje
		$this->_loadUpdates();

		// pobierz id ostatniej aktualizacji
		$updateId = $this->getLastUpdate();
		$lastUpdateId = $updateId;

		$this->rewind();
		while ($this->valid()) {
			// aktualizuj od ostatniej aktualizacji
			if ($this->key() > $updateId) {
				/* @var $update KontorX_Update_Interface */
				$update = $this->current();
	
				try {
					// update
					$update->up();
					$status = $update->getStatus();
				} catch (Exception $e) {
					// update failure
					$status = self::FAILURE;
					// forsowanie aktualizacji wyłączone..
					if (self::FORCE !== $flag) {
						$this->_saveInfo($lastUpdateId);
						throw $e;
					}
				}

				if (self::FAILURE === $status) {
					if (self::FORCE !== $flag) {
						// zapisz informacje o ostatniej aktualizacji
						$this->_saveInfo($lastUpdateId);
						return false;
					}
				}

				$lastUpdateId = (int) $this->key();
			}

			$this->next();
		}

		// zapisz informacje o ostatniej aktualizacji
		$this->_saveInfo($lastUpdateId);

		return true;
	}

	/**
	 * @param string $flag
	 * @return void
	 */
	public function downgrade($flag = null) {
		// załaduj aktualizacje
		$this->_loadUpdates();

		// pobierz id ostatniej aktualizacji
		$updateId = $this->getLastUpdate();
		$lastUpdateId = $updateId;

		$this->rewind();

		require_once 'KontorX/Iterator/Reverse.php';
		$iterator = new KontorX_Iterator_Reverse($this);
		
		while ($iterator->valid()) {
			/**
			 * Cofnij aktualizacje od ostatniej aktualizacji. 
			 * <= bo cofam od ostatniegj aktulizacji
			 */
			if ($iterator->key() <= $updateId) {
				$lastUpdateId = (int) $iterator->key();

				/* @var $update KontorX_Update_Interface */
				$update = $iterator->current();
	
				try {
					// update
					$update->down();
					$status = $update->getStatus();
				} catch (Exception $e) {
					// update failure
					$status = self::FAILURE;
					// forsowanie dezaktualizacji wyłączone..
					if (self::FORCE !== $flag) {
						$this->_saveInfo($lastUpdateId);
						throw $e;
					}
				}

				if (self::FAILURE === $status) {
					if (self::FORCE !== $flag) {
						// zapisz informacje o ostatniej dezaktualizacji
						$this->_saveInfo($lastUpdateId);
						return false;
					}
				}
			}

			$iterator->next();
		}

		/**
		 * @todo może ($lastUpdateId - 1) bo jest to aktualizacj wstecz..
		 * zrozumieni kryje się w metodzie {@see getUpdateFileList()}
		 */

		// Zapisz informacje o ostatniej dezaktualizacji
		$this->_saveInfo($lastUpdateId);
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
     * @return void
     * @throws KontorX_Update_Exception
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
     * @throws KontorX_Update_Exception
     */
    public function getPluginLoader($type = null) {
        if (!isset($this->_pluginLoader[$type])) {
            switch ($type) {
                case self::FILE:
                    $prefixSegment = 'Update_File';
                    $pathSegment   = 'Update/File';
                    break;
                default:
                    require_once 'KontorX/Update/Exception.php';
                    throw new KontorX_Update_Exception(sprintf('Invalid type "%s" provided to getPluginLoader()', $type));
            }

            require_once 'Zend/Loader/PluginLoader.php';
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