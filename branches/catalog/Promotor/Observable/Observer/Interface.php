<?php
interface Promotor_Observable_Observer_Interface {
	public function update(Promotor_Observable_List $list);

	public function getStatus();

	public function getResult();

	public function getMessages();

	public function getExceptions();
}