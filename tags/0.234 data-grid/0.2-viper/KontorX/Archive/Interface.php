<?php
interface KontorX_Archive_Interface {
	public function setFile($file);
	public function getFile();
	public function extract($path);
}