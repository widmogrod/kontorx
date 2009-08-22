<?php
class KontorX_Update_Manager {
	
	/**
	 * Nazwa pliku, w którym jest zachowywana informacja o ostatnim updacie
	 * @var string
	 */
	const FILENAME_INFO = '.update';

	/**
	 * Prefix dla plików, które są skanowane jako dane zawierające update
	 * @var string
	 */
	const UPDATE_FILENAME_PREFIX = 'r';

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
	 * @throws KontorX_Update_Exception
	 */
	public function setUpdatePath($path) {
		if (is_dir($path)) {
			$this->_updatePath = (string) $path;
			return $this;
		}

		require_once 'KontorX/Update/Exception.php';
		throw new KontorX_Update_Exception(sprintf('update path "%s" do not exsists')); 
	}

	/**
	 * @return string
	 */
	public function getUpdatePath() {
		return $this->_updatePath;
	}
	
	/**
	 * @return void
	 */
	public function update() {
		
	}

	/**
	 * @return void
	 */
	public function downgrade() {
		
	}
}