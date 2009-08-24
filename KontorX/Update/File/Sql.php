<?php
require_once 'KontorX/Update/File/Abstract.php';
class KontorX_Update_File_Sql extends KontorX_Update_File_Abstract {

	/**
	 * Update
	 * @return void
	 */
	public function up() {
		$adapter = Zend_Db_Table_Abstract::getDefaultAdapter();
		if (!$adapter instanceof Zend_Db_Adapter_Abstract) {
			$this->_addMessage(sprintf('%s: Zend_Db_Table_Abstract is not set or is not instanceof "Zend_Db_Adapter_Abstract"', get_class($this)));
			$this->_setStatus(self::FAILURE);
			return;
		}

		$pathname = $this->getPathname();

		if (false === ($sql = @file_get_contents($pathname))) {
			$message = function_exists('error_get_last')
				? error_get_last()
				: sprintf('sql file "%s" do not exsists', $pathname);

			$this->_addMessage($message);
			$this->_setStatus(self::FAILURE);
			return;
		}

		try {
			$stmt = $adapter->query($sql);
			$stmt->execute();
			$this->_setStatus(self::SUCCESS);
		} catch(Exception $e) {
			$this->_addException($e);
			$this->_setStatus(self::FAILURE);
		}
	}
	
	/**
	 * Downgrade
	 * @return void
	 */
	public function down() {
		// nie można downgradować updateu!
		$this->_setStatus(self::FAILURE); 
	}
}