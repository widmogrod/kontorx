<?php
class KontorX_Archive {
	public static function factory($file, $options = null) {
		if (is_string($options)) {
			$options['type'] = $options;
		}
	}
}