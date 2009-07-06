<?php
interface KontorX_Navigation_Recursive_Visitor_Interface {

	/**
	 * @param array $current
	 * @return array
	 */
	public function prepare(array $current);
}