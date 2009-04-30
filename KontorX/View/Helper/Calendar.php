<?php
require_once 'Zend/View/Helper/Abstract.php';
class KontorX_View_Helper_Calendar extends Zend_View_Helper_Abstract {

	/**
	 * @var KontorX_Calendar_Month
	 */
	private $_month = null;
	
	/**
	 * @param KontorX_Calendar_Month $month
	 * @return KontorX_View_Helper_Calendar
	 */
	public function calendar(KontorX_Calendar_Month $month) {
		$this->_month = $month;
		return $this;
	}
	
	/**
	 * @var Zend_Date
	 */
	protected $_date = null;
	
	/**
	 * @return Zend_Date
	 */
	protected function _getDate() {
		if (null === $this->_date) {
			$this->_date = new Zend_Date();
		}
		return $this->_date;
	}

	/**
	 * @var array
	 */
	protected $_fromTo = array();
	
	/**
	 * @param $array
	 * @return KontorX_View_Helper_Calendar
	 */
	public function setFromTo($array) {
		$this->_fromTo = array();
		foreach ($array as $key => $val) {
			if (is_array($val)) {
				if (isset($val['from']) && isset($val['to'])) {
					$this->addFromTo($val['from'], $val['to'], @$val['options']);
				}
			}
		}
		return $this;
	}
	
	/**
	 * @param string $from
	 * @param string $to
	 * @param mixed $options
	 * @return KontorX_View_Helper_Calendar
	 */
	public function addFromTo($from, $to, $options = null) {
		$this->_fromTo[] = array($from, $to, $options);
		return $this;
	}

	/**
	 * @return array
	 */
	public function getFromTo() {
		return $this->_fromTo;
	}

	/**
	 * @param $day
	 * @return bool|mixed
	 */
	public function isFromTo(KontorX_Calendar_Day  $day) {
		$date = $this->_getDate();
		foreach ($this->_fromTo as $fromTo) {
			list($from, $to, $options) = $fromTo;
			$date->setTimestamp($day->getTimestamp());
			if (($date->isEarlier($to) && $date->isLater($from))) {
				return $options;
			} else
			// zapewnia że te same dni równiez są poprawne
			if (date('Y-m-d',strtotime($to)) == date('Y-m-d', $day->getTimestamp())
				|| date('Y-m-d',strtotime($from)) == date('Y-m-d', $day->getTimestamp())) {
				return $options;
			}
		}
		return false;
	}
	
	/**
	 * @return string
	 */
	public function render() {
		$result = array('<table>');
		$result[] = '<caption>';
		$result[] = ucfirst($this->view->translate($this->_month->getMonthName()));
		$result[] = '</caption>';

		$hasDayName = false;
		$dayNames = array('<tr>');

		$weeks = $this->_month->getWeeks();
		$weeks->rewind();
		while ($weeks->valid()) {
			$week = $weeks->current();
			
			$weeksView = array();
			$weeksView[] = '<tr>';
			$week->rewind();
			while ($week->valid()) {
				$day = $week->current();
				$dayName = $day->getDayName();

				// zbierz nazwy dni tygodnia
				if (!$hasDayName) {
					$dayNames[] = sprintf('<td class="day-name %s">', $dayName);
					$dayNames[] = $this->view->translate($dayName);
					$dayNames[] = '</td>';
				}

				$dayName .= ($this->_month->hasDay($day))
					? ' '
					: ' day-out-of-month';
					
				$dayName .= ($this->isFromTo($day) !== false)
					? ' reserv'
					: '';
				
				$weeksView[] = sprintf('<td class="%s">', $dayName);
				$weeksView[] = (string) $day;
				$weeksView[] = '</td>';

				$week->next();
			}		
			$weeksView[] = '</tr>';

			if (!$hasDayName) {
				$hasDayName = true;
				
				$dayNames[] = '</tr>';
				$result[] = implode($dayNames);
			}
			
			$result[] = implode($weeksView);
			
			$weeks->next();
		}

		$result[] = '</table>';
		return implode($result);
	}

	/**
	 * @return string
	 */
	public function __toString() {
		return $this->render();
	}
}