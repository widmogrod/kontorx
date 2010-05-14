<?php
class KontorX_DataGrid_Cell_KeyInArray extends KontorX_DataGrid_Cell_Abstract
{
	/**
	 * @var array
	 */
	protected $_array = array();

	/**
	 * @param array $array
	 */
	public function setArray(array $array)
	{
		$this->_array = array();
		
		foreach ($array as $key => $value)
		{
			if (is_array($value))
			{
				if (!array_key_exists('key', $value) 
					|| !array_key_exists('value', $value))
				{
					continue;
				}

				$key   = $value['key'];
				$value = $value['value'];
			}

			$this->_array[$key] = $value;
		}
	}

	public function render()
	{
		$value = $this->getValue();

		return (array_key_exists($value, $this->_array))
			? $this->_array[$value]
			: $value;
	}
}