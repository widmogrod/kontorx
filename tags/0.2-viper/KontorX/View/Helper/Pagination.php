<?php
/**
 * Pagination
 * 
 * @category 	KontorX
 * @package 	KontorX_View_Helper
 * @version 	0.1.0
 * @license		GNU GPL
 * @author 		Marcin `widmogror` Habryn, widmogrod@gmail.com
 * 
 * @todo		Dodać opisy
 */
class KontorX_View_Helper_Pagination {
	/**
	 * Enter description here...
	 *
	 * @var array
	 */
	protected $_style = array(
		'start' 	=> '<a href="{uri}">&lt;&lt;start</a>',
		'stop'		=> '<a href="{uri}">koniec&gt;&gt;</a>',
		'next'		=> '<a href="{uri}">dalej&gt;</a>',
		'back' 	=> '<a href="{uri}">&lt;wstecz</a>',
		'start_unactive'	=> '<span>&lt;&lt;start</span>',
		'stop_unactive'		=> '<span>koniec&gt;&gt;</span>',
		'next_unactive'		=> '<span>dalej&gt;</span>',
		'back_unactive'	=> '<span>&lt;wstecz</span>',	
		'active'  	=> '<span>{id}</span>',
		'break'		=> '&nbsp;',
		'current'	=> '<a href="{uri}">{id}</a>',
	);
	
	/**
	 * Enter description here...
	 *
	 * @var string
	 */
	protected $_uri = null;
	
	/**
	 * Enter description here...
	 *
	 * @var integer
	 */
	protected $_records = null;
	
	/**
	 * Enter description here...
	 *
	 * @var integer
	 */
	protected $_onpage = null;
	
	/**
	 * Enter description here...
	 *
	 * @var integer
	 */
	protected $_active = null;

	/**
	 * Enter description here...
	 *
	 * @return string
	 */
	public function run(){
		$records = round($this->_records/$this->_onpage,2);
		$this->_active = ($this->_active < 1) ? 1 : $this->_active ;
		// Bo nie jest od 0 tylko od 1 ...
		$records = ($records < 1) ? 1 : $records+1;
		$return = null;
		$serch = array('{uri}', '{id}');

		if ($this->_active < 2) {
			$replace = array($this->_uri, 1);

			// Start && Back
			$content = $this->_style['start_unactive'];
			$content .= $this->_style['break'];
			$content .= $this->_style['back_unactive'];
			$return .= str_ireplace($serch, $replace, $content);
		} else {
			// Start
			$replace = array($this->_uri, 1);
			$content = $this->_style['start'];
			$return .= str_ireplace($serch, $replace, $content);

			$return .= $this->_style['break'];

			// Back
			// Zabespieczenie przed nActive np 1000 ...
			$active = ($this->_active > $records) ? $records : $this->_active;

			$replace = array($this->_uri, $active -1);
			$content = $this->_style['back'];
			$return .= str_ireplace($serch, $replace, $content);
		}

		$content = null;
		$return .= $this->_style['break'];

		if ($records >= 9) {
			if($this->_active-3 < 1) {
				for ($i=1; $i<=6; $i++) {
					$replace = array($this->_uri, $i);
		
					switch (true) {
						case $this->_active < 1 && $i < 2 :
							$content = $this->_style['active'];
							break;
						case $i == $this->_active:
							$content = $this->_style['active'];
							break;
						default:
							if($this->_active > $records && $i == $records){
								$content = $this->_style['active'];
							} else {
								$content = $this->_style['current'];
							}
					}
		
					$return .= str_ireplace($serch, $replace, $content);
					$return .= $this->_style['break'];
				}
			} else
			if ($this->_active+3 > $records){
				for ($i=$records-6; $i<=$records; $i++) {
					$replace = array($this->_uri, $i);
		
					switch (true) {
						case $this->_active < 1 && $i < 2 :
							$content = $this->_style['active'];
							break;
						case $i == $this->_active:
							$content = $this->_style['active'];
							break;
						default:
							if($this->_active > $records && $i == $records){
								$content = $this->_style['active'];
							} else {
								$content = $this->_style['current'];
							}
					}
		
					$return .= str_ireplace($serch, $replace, $content);
					$return .= $this->_style['break'];
				}
			} else {
				for ($i=$this->_active-3; $i<=$this->_active+3; $i++) {
					$replace = array($this->_uri, $i);
		
					switch (true) {
						case $this->_active < 1 && $i < 2 :
							$content = $this->_style['active'];
							break;
						case $i == $this->_active:
							$content = $this->_style['active'];
							break;
						default:
							if($this->_active > $records && $i == $records){
								$content = $this->_style['active'];
							} else {
								$content = $this->_style['current'];
							}
					}
		
					$return .= str_ireplace($serch, $replace, $content);
					$return .= $this->_style['break'];
				}
			}
		} else {
			// 1,2,3,4,5,6,7
			for ($i=1; $i<=$records; $i++) {
				$replace = array($this->_uri, $i);
	
				switch (true) {
					case $this->_active < 1 && $i < 2 :
						$content = $this->_style['active'];
						break;
					case $i == $this->_active:
						$content = $this->_style['active'];
						break;
					default:
						if($this->_active > $records && $i == $records){
							$content = $this->_style['active'];
						} else {
							$content = $this->_style['current'];
						}
				}
	
				$return .= str_ireplace($serch, $replace, $content);
				$return .= $this->_style['break'];
			}
		}
		
		if ($this->_active >= $records){
			$replace = array($this->_uri, $records);

			// Next & Stop ..
			$content = $this->_style['next_unactive'];
			$content .= $this->_style['break'];
			$content .= $this->_style['stop_unactive'];
			$return .= str_ireplace($serch, $replace, $content);
		} else {
			// Next
			// Zabespieczenie przed nActive np -1000 ...
			$active = ($this->_active < 1) ? 1 : $this->_active;
			$replace = array($this->_uri, $active+1);
			$content = $this->_style['next'];
			$return .= str_ireplace($serch, $replace, $content);

			$return .= $this->_style['break'];

			// Stop
			$replace = array($this->_uri, $records);
			$content = $this->_style['stop'];
			$return .= str_ireplace($serch, $replace, $content);
		}

		return $return;
	}
	
	/**
	 * Enter description here...
	 *
	 * @param array $style
	 * @return KontorX_View_Helper_Pagination
	 */
	public function setStyle($style){
		if (is_array($style)){
			$this->_style = array_merge($this->_style, $style);
		}
		
		return $this;
	}
	
	/**
	 * Enter description here...
	 *
	 * @param string $uri
	 * @return KontorX_View_Helper_Pagination
	 */
	public function setUri($uri){
		$this->_uri = (string) $uri;
		
		return $this;
	}
	
	/**
	 * Enter description here...
	 *
	 * @param integer $records
	 * @return KontorX_View_Helper_Pagination
	 */
	public function setRecords($records){
		$this->_records = (int) $records;
		
		return $this;
	}

	/**
	 * Enter description here...
	 *
	 * @param integer $active
	 * @return KontorX_View_Helper_Pagination
	 */
	public function setActive($active){
		$this->_active = (int) $active;
		
		return $this;
	}

	/**
	 * Enter description here...
	 *
	 * @param integer $onpage
	 * @return KontorX_View_Helper_Pagination
	 */
	public function setOnpage($onpage){
		$this->_onpage = (int) $onpage;
		
		return $this;
	}
}
?>