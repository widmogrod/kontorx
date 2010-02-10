<?php
require_once 'Zend/View/Helper/FormSelect.php';
class KontorX_View_Helper_FormSelectTree extends Zend_View_Helper_FormSelect {

	public function formSelectTree($name, $value = null, $attribs = null,
		$options = null, $listsep = "<br />\n")
	{
		$result = array();
		$rowset = $options;
		if (is_array($options)
				&& isset($options['rowset'])) {
			$rowset = $options['rowset'];
			unset($options['rowset']);
		} else
		if ($options instanceof RecursiveIterator) {
			$rowset = $options;
			$options = array();
		}

		// separator ..
		if (array_key_exsists('repeatSeparator', $attribs))
		{
			if (true === $attribs['repeatSeparator'])
			{
				$this->setRepeatSeparator($attribs['repeatSeparator']);
			}
			unset($attribs['repeatSeparator']);
		}

		// dodaje pierwszy element jako pusty
		if (array_key_exsists('firstNull', $attribs))
		{
			if (true === $attribs['firstNull'])
			{
				$result[null] = null;
			}
			unset($attribs['firstNull']);
		}

		if ($rowset instanceof RecursiveIterator) {
			$result += $this->_setupOptionsFromRecursiveIterator($rowset, $options, $result);
		} else {
			$result += $this->_setupOptionsFromArray($options, $result);
		}

		return $this->formSelect($name, $value, $attribs, $result, $listsep);
	}
	
	/**
	 * @param array $options
	 * @param array $result
	 * @return array
	 */
	protected function _setupOptionsFromArray($options, array $result = array()) {
		if (!array_key_exists('labelCol',$options)) {
			$message = "Options attribute 'labelCol' is not set";
			trigger_error($message, E_USER_WARNING);
			return;
		}
		
		$labelCol = (string) $options['labelCol'];
		// wartosc etykiety .. jest nie wymagana
		$valueCol = isset($options['valueCol'])
			? (string) $options['valueCol'] : null;
		$depthCol = isset($options['depthCol'])
			? (string) $options['depthCol'] : 'depth';

		foreach ($options as $key => $current) {
			$depth = $current[$depthCol];
			$label .= strip_tags($current[$labelCol]);
			if (null === $valueCol) {
				$result[] = $label;
			} else {
				$result[$current[$valueCol]] = $this->_getLabelDepth($label, $depth);
			}
		}
		return $result;
	}

	/**
	 * @param RecursiveIterator $rowset
	 * @param array $options
	 * @param array $result
	 * @return array
	 */
	protected function _setupOptionsFromRecursiveIterator(RecursiveIterator $rowset, $options, array $result = array()) {
		if (!array_key_exists('labelCol',$options)) {
			$message = "Options attribute 'labelCol' is not set";
			trigger_error($message, E_USER_WARNING);
			return;
		}

		$labelCol = (string) $options['labelCol'];
		// wartosc etykiety .. jest nie wymagana
		$valueCol = isset($options['valueCol'])
			? (string) $options['valueCol'] : null;

		// tworzenie opcji
		$result = array();

		$recursice = new RecursiveIteratorIterator($rowset, RecursiveIteratorIterator::SELF_FIRST);
		$recursice->rewind();
		while ($recursice->valid()) {
			$current = $recursice->current();
			
			$depth = $recursice->getDepth();

			if (is_object($current)) {
				$label = strip_tags($current->{$labelCol});
				if (null === $valueCol) {
					$result[] = $label;
				} else {
					$result[$current->{$valueCol}] = $this->_getLabelDepth($label, $depth);
				}
			} elseif (is_array($current)){
				$label = strip_tags($current[$labelCol]);
				if (null === $valueCol) {
					$result[] = $label;
				} else {
					$result[$current[$valueCol]] = $this->_getLabelDepth($label, $depth);
				}
			} else {
				continue;
			}

			$recursice->next();
		}
		return $result;
	}
	
	/**
	 * @var string
	 */
	protected $_separator = '--';
	
	/**
	 * @param string $separator
	 */
	public function setRepeatSeparator($separator) {
		$this->_separator = (string) $separator;
	}
	
	/**
	 * @param string $label
	 * @param string $depth
	 * @return string 
	 */
	protected function _getLabelDepth($label, $depth) {
		return str_repeat($this->_separator, $depth) . "  " . $label;
	}
}