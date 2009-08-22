<?php
require_once 'KontorX/Update/Interface.php';
class KontorX_Update_File_Sql implements KontorX_Update_Interface {
	/**
	 * Update
	 * @return void
	 */
	public function up() {
		
	}
	
	/**
	 * Downgrade
	 * @return void
	 */
	public function down() {
		// nie można downgradować updateu!
		return false;
	}
}