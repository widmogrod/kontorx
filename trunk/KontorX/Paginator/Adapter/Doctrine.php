<?php
require_once 'Zend/Paginator/Adapter/Interface.php';

/**
 *  
 * @author g.habryn (widmogrod@gmail.com)
 */
class KontorX_Paginator_Adapter_Doctrine implements Zend_Paginator_Adapter_Interface
{
    /**
     * @var Doctrine_Query
     */
    protected $_query;
    
	public function __construct(Doctrine_Query $query) 
	{
        $this->_query = $query;
    }
    
    /**
     * @var Doctrine_Pager
     */
    protected $_pager;
    
    /**
     * @return Doctrine_Pager
     */
    public function getPager()
    {
        if (null === $this->_pager)
        {
            $this->_pager = new Doctrine_Pager($this->_query, 1);
        }
        
        return $this->_pager;
    }

    /**
     * @var array
     */
    protected $_items;
    
    /**
     * Returns an collection of items for a page.
     *
     * @param  integer $offset Page offset
     * @param  integer $itemCountPerPage Number of items per page
     * @return array
     */
    public function getItems($offset, $itemCountPerPage)
    {
        $pager = $this->getPager();
        $pager->setPage($offset);
        $pager->setMaxPerPage($itemCountPerPage);
        
        if (!$pager->getExecuted()) {
            $this->_items = $pager->execute(array(), Doctrine::HYDRATE_SCALAR);
        }

        return $this->_items;
    }
    
    public function count()
    {
        $pager = $this->getPager();
        
        if (!$pager->getExecuted()) {
            $pager->execute();
        }

        return $pager->getNumResults();
    }
}