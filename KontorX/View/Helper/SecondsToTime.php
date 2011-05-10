<?php
class KontorX_View_Helper_SecondsToTime extends Zend_View_Helper_Abstract
{
    const TIME_YEAR = 31556926;
    const TIME_DAY = 86400;
    const TIME_HOUR = 3600;
    const TIME_MINUTES = 60;
    
    protected $_result = array(
        'years' => 0,
        'days' => 0,
        'hours' => 0,
    	'minutes' => 0,
    	'seconds' => 0,
    );
    
    protected $_translations = array(
    	'years' => '%d years',
        'days' => '%d days',
        'hours' => '%d hours',
    	'minutes' => '%d minutes',
    	'seconds' => '%d seconds',
    );
    
    function secondsToTime($time, $default = false)
    {
        if (!is_numeric($time)) {
            return $default;
        }
        
        switch (true)
        {
            case $time >= self::TIME_YEAR:
                $this->_result['years'] = floor($time / self::TIME_YEAR);
                $time = ($time % self::TIME_YEAR);

            case $time >= self::TIME_DAY:
                $this->_result['days'] = floor($time / self::TIME_DAY);
                $time = ($time % self::TIME_DAY);

            case $time >= self::TIME_HOUR:
                $this->_result['hours'] = floor($time / self::TIME_HOUR);
                $time = ($time % self::TIME_HOUR);

            case $time >= self::TIME_MINUTES:
                $this->_result['minutes'] = floor($time / self::TIME_MINUTES);
                $time = ($time % self::TIME_MINUTES);

            default:
                $this->_result['seconds'] = floor($time);
        }
        
        return $this;
    }

    public function toArray()
    {
        return $this->_result;
    }

    public function render()
    {
        $result = array();
        foreach ($this->_result as $key => $time) 
        {
            if ($time > 0) 
            {
                $value = $this->_translations[$key];
                $result[] = $this->view->translate($value, $time);
            }
        }

        return implode(' ', $result);
    }
    
    public function __toString()
    {
        try {
            return $this->render();
        } catch (Exception $e) {
            trigger_error($e->getMessage());
            return '';
        }
    }
}