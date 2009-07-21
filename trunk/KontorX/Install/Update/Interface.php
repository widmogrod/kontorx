<?php
interface KontorX_Install_Update_Interface {
	/**
	 * Update
	 * @return void
	 */
	public function up();
	
	/**
	 * Downgrade
	 * @return void
	 */
	public function down();
}