<?php
/**
 * Datesorter
 * 
 * @category 	KontorX
 * @package 	KontorX_View_Helper
 * @version 	0.1.0
 * @license		GNU GPL
 * @author 		Marcin `widmogror` Habryn, widmogrod@gmail.com
 * 
 * @todo		Dodać opisy
 */
class KontorX_View_Helper_Datesorter {
	const TODAY = 'today';
	const TOMOROW = 'tomorow';
	const NEXTWEEK = 'nextweek';
	const EALIER = 'ealier';
	const THISWEEK = 'thisweek';
	const UNCATORIZED = 'default';
	const FROMTODAY = 'fromtoday';
	
	protected $timeField = null;
	protected $timeFormat = null;

	protected $_rowset = array(
		self::TODAY => array(),
		self::TOMOROW => array(),
		self::NEXTWEEK => array(),
		self::THISWEEK => array(),
		self::EALIER => array(),
		self::UNCATORIZED => array(),
		self::FROMTODAY => array()
	);

	/**
	 * Enter description here...
	 *
	 * @param Zend_Db_Table_Rowset_Abstract $rowset
	 * @param string $timeField
	 * @param string $timeFormat
	 * @return KontorX_View_Helper_Datesorter
	 */
	public function datesorter($rowset, $timeField, $timeFormat = 'Y-m-d') {
		$zd = new Zend_Date();
		$zdToday = new Zend_Date();
		$zdWeek = new Zend_Date(mktime(0,0,0,date('m'),date('d')+7,date('Y')));
		
		foreach ($rowset as $row) {
			if (is_object($row)) {
				$zd->set(strtotime($row->{$timeField}));
			} else {
				$zd->set(strtotime($row[$timeField]));
			}
			
			switch (true) {
				default:						$this->_rowset[self::UNCATORIZED][] = $row;
				case $zd->isToday(): 			$this->_rowset[self::TODAY][] 	 	= $row; #break;
				case $zd->isTomorrow(): 		$this->_rowset[self::TOMOROW][]  	= $row; #break;
				case $zd->isEarlier($zdToday): 	$this->_rowset[self::EALIER][] 	 	= $row; #break;
				case $zd->isEarlier($zdWeek): 	$this->_rowset[self::THISWEEK][] 	= $row; #break;
				case $zd->isLater($zdToday): 	$this->_rowset[self::FROMTODAY][] 	= $row; #break;
				case $zd->isLater($zdWeek): 	$this->_rowset[self::NEXTWEEK][] 	= $row; #break;
			}
		}

		return $this;
	}

	public function get($flag) {
		if (array_key_exists($flag, $this->_rowset)) {
			return $this->_rowset[$flag];
		}
		return false;
	}
}
?>