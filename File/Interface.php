<?php
interface KontorX_File_Interface {
	public function __construct($pathname = null);
	public function save($data);
	public function delete($data);
}
?>