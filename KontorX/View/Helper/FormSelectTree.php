<?php
require_once 'Zend/View/Helper/FormSelect.php';
class KontorX_View_Helper_FormSelectTree extends Zend_View_Helper_FormSelect {

	public function formSelectTree($name, $value = null, $attribs = null,
		$options = null, $listsep = "<br />\n")
	{
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

		if (true === @$attribs['repeatSeparator']) {
			$this->setRepeatSeparator($attribs['repeatSeparator']);
		}
		
		if ($rowset instanceof RecursiveIterator) {
			$options = $this->_setupOptionsFromRecursiveIterator($rowset, $options);
		} else {
			$options = $this->_setupOptionsFromArray($options);
		}

		// dodaje pierwszy element jako pusty
		if (true === @$attribs['firstNull']) {
			array_unshift($options, array(null=>null));
		}

		return $this->formSelect($name, $value, $attribs, $options, $listsep);
	}
	
	/**
	 * @param array $options
	 * @return array
	 */
	protected function _setupOptionsFromArray($options) {
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

		$result = array();
		
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
	 * @return array
	 */
	protected function _setupOptionsFromRecursiveIterator(RecursiveIterator $rowset, $options) {
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
		$options = array();

		$recursice = new RecursiveIteratorIterator($rowset, RecursiveIteratorIterator::SELF_FIRST);
		$recursice->rewind();
		while ($recursice->valid()) {
			$current = $recursice->current();
			
			$depth = $recursice->getDepth();

			if (is_object($current)) {
				$label = strip_tags($current->{$labelCol});
				if (null === $valueCol) {
					$options[] = $label;
				} else {
					$options[$current->{$valueCol}] = $this->_getLabelDepth($label, $depth);
				}
			} elseif (is_array($current)){
				$label = strip_tags($current[$labelCol]);
				if (null === $valueCol) {
					$options[] = $label;
				} else {
					$options[$current[$valueCol]] = $this->_getLabelDepth($label, $depth);
				}
			} else {
				continue;
			}

			$recursice->next();
		}
		return $options;
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