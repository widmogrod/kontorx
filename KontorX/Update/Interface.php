<?php
interface KontorX_Update_Interface {

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

	/**
	 * @return array
	 */
	public function getMessages();
	
	/**
	 * @return string
	 */
	public function getStatus();
}