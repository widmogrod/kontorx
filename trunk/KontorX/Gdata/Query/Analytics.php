<?php
/**
 * @author gabriel
 * @version $Id$
 * @link http://code.google.com/intl/pl/apis/analytics/docs/gdata/gdataReferenceDataFeed.html
 */
class KontorX_Gdata_Query_Analytics extends Zend_Gdata_Query
{
    /**
     * The unique table ID used to retrieve the Analytics Report data. 
     * This ID is provided by the <ga:tableId> element for each entry in the account feed. 
     * This value is composed of the ga: namespace and the profile ID of the web property.
     * 
     * Required
     * 
     * @var string
     */
    protected $_ids;
    
    /**
     * The dimensions parameter defines the primary data keys for your Analytics report, 
     * such as ga:browser or ga:city. Use dimensions to segment your web property metrics. 
     * For example, while you can ask for the total number of pageviews to your site, 
     * it might be more interesting to ask for the number of pageviews segmented by browser. 
     * In this case, you'll see the number of pageviews from Firefox, 
     * Internet Explorer, Chrome, and so forth. 
     * 
     * Optional
     * 
     * @var array
     */
    protected $_dimensions = array();
    
    /**
     * The aggregated statistics for user activity in a profile, such as clicks or pageviews. 
     * When queried by alone, metrics provide aggregate values for the requested date range, 
     * such as overall pageviews or total bounces. However, when requested with dimensions, 
     * values are segmented by the dimension. For example, ga:pageviews requested with ga:country
     * returns the total pageviews per country rather than the total pageviews for the entire profile. 
     * 
     * When requesting metrics, keep in mind:
     * - Any request must supply at least one metric because a request cannot consist only of dimensions.
     * - You can supply a maximum of 10 metrics for any query.
     * - Most combinations of metrics from multiple categories can be used together, provided no dimensions are specified.
     * - The exception to the above is the ga:visitors metric, which can only be used in combination with a subset of metrics. See the Query Validation Chart for details.
     * - Any given metric can be used in combination with other dimensions or metrics, but only where Valid Combinations apply for that metric.
     * - Metric values are always reported as an aggregate because the Data Export API does not provide calculated metrics. For a list of common calculations based on aggregate metrics, see Common Calculations.
     * 
     * Required
     * 
     * @var array
     */
    protected $_metrics = array();
    
    /**
     * Indicates the sorting order and direction for the returned data. For example, 
     * the following parameter would first sort by ga:browser and then by ga:pageviews
     * in ascending order.
     * 
     * When using the sort parameter, keep in mind the following:
     * - Sort only by dimensions or metrics value that you have used in the dimensions 
     *   or metrics parameter. If your request sorts on a field that is not indicated 
     *   in either the dimensions or metrics parameter, you will receive a request error.
     * - Strings are sorted in ascending alphabetical order in an en-US locale.
     * - Numbers are sorted in ascending numeric order.
     * - Dates are sorted in ascending order by date.
     * 
     * Optional
     * 
     * @var array
     */
    protected $_sort = array();
    
    /**
     * The filters query string parameter restricts the data returned from your request 
     * to the Analytics servers. When you use the filters parameter, you supply a dimension
     * or metric you want to filter, followed by the filter expression. 
     * For example, the following feed query requests ga:pageviews and ga:browser 
     * from profile 12134, where the ga:browser dimension starts with the string Firefox:
     * 
     * 	 https://www.google.com/analytics/feeds/data
     *   ?ids=ga:12134
     *   &dimensions=ga:browser&metrics=ga:pageviews
     *   &filters=ga:browser%3D~%5EFirefox
     *   &start-date=2007-01-01
     *   &end-date=2007-12-31
     * 
     * Optional
     * 
     * @var array
     */
    protected $_filters = array();
    
    /**
     * @todo
     * @var array
     */
    protected $_segment = array();
    
    /**
     * All Analytics feed requests must specify a beginning and ending date range. 
     * If you do not indicate start- and end-date values for the request, 
     * the server returns a request error. Date values are in the form YYYY-MM-DD.
     * The earliest valid start-date is 2005-01-01. There is no upper limit restriction
     * for a start-date. However, setting a start-date that is too far in the 
     * future will most likely return empty results.
     *     
     * Required
     * 
     * @var YYYY-MM-DD
     */
    protected $_startDate;
    
    /**
     * All Analytics feed requests must specify a beginning and ending date range. 
     * If you do not indicate start- and end-date values for the request, 
     * the server returns a request error. Date values are in the form YYYY-MM-DD.
     * The earliest valid end-date is 2005-01-01. There is no upper limit restriction 
     * for an end-date. However, setting an end-date that is too far in the 
     * future might return empty results.
     * 
     * Required
     * 
     * @var YYYY-MM-DD
     */
    protected $_endDate;
    
    /**
     * @var boolean
     */
    protected $_prettyPrint = false;
    
    /**
     * @var boolean
     */
    protected $_compare = true;
    
    public function __construct()
    {
        parent::__construct(KontorX_Gdata_Analytics::ANALYTICS_DATA_URI);
    }
    
    public function setCompare($flag = true)
    {
        $this->_compare = (bool) $flag;
    }
    
    public function isCompared()
    {
        return $this->_compare;
    }
    
    public function setIds($ids)
    {
        $this->_ids = (string) $ids;
        return $this;
    }
    
    public function getIds()
    {
        return $this->_ids;
    }
    
    public function addFilter($filter)
    {
        $this->_filters[] = (string) $filter;
        return $this;
    }
    
    public function setFilters($filters)
    {
        $this->_filters = array();
        
        if (is_array($filters)) 
        {
            $this->_filters = $filters;
        }
        else 
        {
            $filters = explode(',', $filters);
            $filters = array_map('trim', $filters);
            $filters = array_filter($filters);
            array_map(array(&$this, 'addFilter'), $filters);
        }

        return $this;
    }

    public function getFilters()
    {
        return $this->_filters;
    }
    
    public function addDimension($dimension)
    {
        $this->_dimensions[] = (string) $dimension;
        return $this;
    }
    
    public function setDimensions($dimensions)
    {
        $this->_dimensions = array();
        
        if (is_array($dimensions)) 
        {
            $this->_dimensions = $dimensions;
        }
        else 
        {
            $dimensions = explode(',', $dimensions);
            $dimensions = array_map('trim', $dimensions);
            $dimensions = array_filter($dimensions);
            array_map(array(&$this, 'addDimension'), $dimensions);
        }

        return $this;
    }

    public function getDimensions()
    {
        return $this->_dimensions;
    }
    
    public function addMetric($metric)
    {
        if (count($this->_metrics) > 9) {
            throw new Exception('You can supply a maximum of 10 metrics for any query.');
        }
        
        $this->_metrics[] = (string) $metric;
        return $this;
    }
    
    public function setMetrics($metrics)
    {
        $this->_metrics = array();

        if (is_array($metrics)) 
        {
            $this->_metrics = $metrics;
        }
        else 
        {
            $metrics = explode(',', $metrics);
            $metrics = array_map('trim', $metrics);
            $metrics = array_filter($metrics);
            array_map(array(&$this, 'addMetric'), $metrics);
        }

        return $this;
    }
    
    public function getMetrics()
    {
        return $this->_metrics;
    }
    
    public function addSort($sort)
    {
        $this->_sort[] = (string) $sort;
        return $this;
    }
    
    public function setSort($sort)
    {
        $this->_sort = array();

        if (is_array($sort)) 
        {
            $this->_sort = $sort;
        }
        else 
        {
            $sort = explode(',', $sort);
            $sort = array_map('trim', $sort);
            $sort = array_filter($sort);
            array_map(array(&$this, 'addSort'), $sort);
        }

        return $this;
    }
    
    public function getSort()
    {
        return $this->_sort;
    }
    
    public function setStartDate($date)
    {
        $this->_startDate = $date;
    }
    
    public function getStartDate()
    {
        if ($this->isCompared())
        {
            $ts = strtotime($this->_startDate);
            $ts = mktime(0,0,0, date('m', $ts)-1, 1, date('y', $ts));
            $this->_startDate = date('Y-m-d', $ts);
        }
        return $this->_startDate;
    }
    
    public function setEndDate($date)
    {
        $this->_endDate = $date;
    }
    
    public function getEndDate()
    {
        return $this->_endDate;
    }

    public function setPrettyPrint($flag = true)
    {
        $this->_prettyPrint = (bool) $flag;
        return $this;
    }
    
    public function getPrettyPrint()
    {
        return  $this->_prettyPrint;
    }
    
	/**
     * @return string querystring
     */
    public function getQueryString()
    {
        # Required
        
        if (empty($this->_ids))
        {
            throw new Exception('Required: ids');
        }
        
        if (empty($this->_metrics))
        {
            throw new Exception('Required: metrics');
        }
        
        if (empty($this->_startDate))
        {
            throw new Exception('Required: start-date');
        }
        
        if (empty($this->_endDate))
        {
            throw new Exception('Required: end-date');
        }

        $this->setParam('ids', $this->getIds()); // Required
        $this->setParam('metrics', implode(',', $this->getMetrics())); // Required
        $this->setParam('start-date', $this->getStartDate());
        $this->setParam('end-date', $this->getEndDate());
        
        # Optional
        
        if (!empty($this->_sort))
        {
            $this->setParam('sort', implode(',', $this->getSort()));
        }
        
        if (!empty($this->_filters))
        {
            $this->setParam('filters', implode(',', $this->getFilters()));
        }
        
        if (!empty($this->_dimensions))
        {
            $this->setParam('dimensions', implode(',', $this->getDimensions()));
        }
        
        $this->setParam('prettyprint', $this->getPrettyPrint());
//        $this->setParam('v', 2);
        
        return parent::getQueryString();
    }
}