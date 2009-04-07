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
	 * @return string
	 */
	public function render() {
		$day = 0;
		$days = $this->_month->getDays();
		$weeks = $this->_month->getWeeksCount();
		$startDay = $this->_month->getMonthStartFromDay();

		$result = array('<table>');
		$result[] = '<caption>';
		$result[] = ucfirst($this->_month->getMonthName());
		$result[] = '</caption>';

		// każdy tydzień
		for($i=1; $i <= $weeks; ++$i) {
			$result[] = '<tr>';
			$resultWeek = array();

			// kazdy dzień w tygodniu
			for ($j=0; $j<=6; ++$j) {
				$resultWeek[] = '<td>';

				$dayValue = null;

				// dla pierwszego tygodnia
				if ($i == 1) {
					// sprawdz  od którego dnia zaczyna się miesiąc o go ewentualnie dopełnij..
					if ($startDay <= $j) {
						$dayValue = ++$day;
					}
				} else
				if ($day < $days) {
					$dayValue = ++$day;
				}

				$resultWeek[] = $dayValue;
				$resultWeek[] = '</td>';
			}
			$result[] = implode($resultWeek);
			$result[] = '</tr>';
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