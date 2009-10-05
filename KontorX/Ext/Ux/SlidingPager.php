<?php
require_once 'KontorX/Ext/Abstract.php';

/**
 * @author gabriel
 * 
 */
class KontorX_Ext_Ux_SlidingPager extends KontorX_Ext_Abstract {

	public function toJavaScript() {
		return 'new Ext.ux.SlidingPager();';
	}
}