<?php
/**
 * Element formularza odpowiada za wyświetlanie drzewa dzielnic 
 * z możliwością zaznaczenia wielu oraz
 * wybrania głównej dzielnicy dla wizytówki,
 * prezentacji firmy|gabinetu
 * 
 * @author Gabriel
 * @version $Id$
 */
class Promotor_Form_Element_District extends Zend_Form_Element
{
	public function init() 
	{
		$this->addPrefixPath(
			'KontorX_Form_Decorator',
			'KontorX/Form/Decorator',
			self::DECORATOR
		);

		$this->setIsArray(true);
	}
	
	public function loadDefaultDecorators() 
	{
		if ($this->loadDefaultDecoratorsIsDisabled()) 
		{
            return;
        }

        $decorators = $this->getDecorators();
        if (empty($decorators)) 
        {
            $this->addDecorator('District')
                ->addDecorator('Errors')
                ->addDecorator('Description', array('tag' => 'p', 'class' => 'description'))
                ->addDecorator('HtmlTag', array('tag' => 'dd',
                                                'id'  => $this->getName() . '-element'))
                ->addDecorator('Label', array('tag' => 'dt'));
        }
	}
	
	public function setValue($value)
	{
		parent::setValue($value);

		if (is_numeric($value)){
			$this->setMainDistrict($value);
		}

		if (is_array($value))
		{
			if (isset($value['chosen_districts']))
			{
				$this->setChosenDistricts($value['chosen_districts']);
			}
			
			if (isset($value['main_district']))
			{
				$this->setMainDistrict($value['main_district']);
			}
		}

		return $this;
	}

	/**
	 * @var integer
	 */
	protected $_mainDistrict;
	
	/**
	 * @param integer $value
	 */
	public function setMainDistrict($value)
	{
		$this->_value = (int)$value;
		$this->_mainDistrict = (int) $value;
	}

	/**
	 * Zwracane jest główny rekord dla danej wizytówki.
	 * 
	 * - gdy nie został podana wartość a został podany primary key
	 *   wczytaj z bazy wartość głównego oobszaru
	 * 
	 * @return integer 
	 */
	public function getMainDistrict()
	{
		if (null !== $this->_mainDistrict)
		{
			return $this->_mainDistrict;
		}

		$primaryKey = $this->getPrimaryKey();
		if (null === $primaryKey)
		{
			return $this->_mainDistrict;
		}

		$catalogTable = new Catalog_Model_DbTable_Catalog();
		$row = $catalogTable->find($primaryKey)->current();

		if (($row instanceof Zend_Db_Table_Row_Abstract))
		{
			$this->_mainDistrict = $row->catalog_district_id;
		}
		
		return $this->_mainDistrict;
	}

	/**
	 * @var KontorX_Db_Table_Tree_Rowset_Abstract
	 */
	protected $_rowset;
	
	/**
	 * @param KontorX_Db_Table_Tree_Rowset_Abstract $rowset
	 */
	public function setDistrictRowset(KontorX_Db_Table_Tree_Rowset_Abstract $rowset)
	{
		$this->_rowset = $rowset;
	}
	
	public function getDistrictRowset()
	{
		if (null === $this->_rowset)
		{
			$districtTable = new Catalog_Model_DbTable_District();
			$this->_rowset = $districtTable->fetchAll();
		}
		return $this->_rowset;
	}

	/**
	 * @var integer
	 */
	protected $_primaryKey;
	
	/**
	 * @param integer $primaryKey
	 */
	public function setPrimarykey($primaryKey)
	{
		$this->_primaryKey = $primaryKey;
	}
	
	/**
	 * @return integer
	 */
	public function getPrimaryKey()
	{
		if (null === $this->_primaryKey)
		{
			// pobierz identyfikator z adresu URL
			// jest to bardzo nieprecyzyjne, ale
			// jako że ten element będzie wykorzystywany tylko 
			// w jednym miejscu w aplikacji.. jest to bardzo przydatne i przyjemne :)
			$this->_primaryKey = Zend_Controller_Front::getInstance()
									->getRequest()
									->getParam('id');
		}
		return $this->_primaryKey;
	}
	
	/**
	 * @var array
	 */
	protected $_chosenDistricts;
	
	/**
	 * @param array $rowset
	 */
	public function setChosenDistricts(array $rowset)
	{
		$this->_chosenDistricts = $rowset;
	}
	
	/**
	 * Wyszukuje obszary, w których dana wizytówka została przypisana.
	 * @return array
	 */
	public function getChosenDistricts()
	{
		if (null !== $this->_chosenDistricts)
		{
			return $this->_chosenDistricts;
		}

		$primaryKey = $this->getPrimaryKey();
		if (null === $primaryKey)
		{
			return $this->_chosenDistricts;
		}

		$hasDistrictTable = new Catalog_Model_DbTable_HasDistrict();
		$select 		  = $hasDistrictTable->select(Zend_Db_Table_Abstract::SELECT_WITH_FROM_PART)
											 ->where('catalog_id = ?', $primaryKey, Zend_Db::INT_TYPE);

		/* @var $stmt Zend_Db_Statement */
		$stmt = $select->query();
		
		$this->_chosenDistricts = array();
		while($districtId = $stmt->fetchColumn(1))
		{
			$this->_chosenDistricts[] = $districtId; 
		}

		return $this->_chosenDistricts;
	}
}