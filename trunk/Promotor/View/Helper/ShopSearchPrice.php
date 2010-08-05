<?php
class Promotor_View_Helper_ShopSearchPrice extends Zend_View_Helper_Abstract
{
	public function shopSearchPrice()
	{
		$name = 'price';
		$value = $this->view->urlParams($name);

		$minName  = $name.'[min]';
		$minValue = @$value['min'];
		$fieldMin = $this->view->formText($minName, $minValue);

		$maxName  = $name.'[max]';
		$maxValue = @$value['max'];
		$fieldMax = $this->view->formText($maxName, $maxValue);
		
		$result = '<span class="shop_filter_price"><label>od %s</label> <label>do %s</label> zł</span>';
		$result = sprintf($result, $fieldMin, $fieldMax);
		
		return $result;
	}
}