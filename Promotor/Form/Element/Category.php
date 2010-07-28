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
class Promotor_Form_Element_Category extends Zend_Form_Element
{
	const MAIN_CATEGORY_NS = 'MAIN_CATEGORY';
	const CHOSEN_CATEGORY_NS = 'CHOSEN_CATEGORY';
	
	public function init() 
	{
		$this->addPrefixPath(
			'Promotor_Form_Decorator',
			'Promotor/Form/Decorator',
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
            $this->addDecorator('Category')
                ->addDecorator('Errors')
                ->addDecorator('Description', array('tag' => 'p', 'class' => 'description'))
                ->addDecorator('HtmlTag', array('tag' => 'dd',
                                                'id'  => $this->getName() . '-element'))
                ->addDecorator('Label', array('tag' => 'dt'));
        }
	}
	
	public function getValue() 
	{
		return $this->getMainCategory();
	}
	
	public function setValue($value)
	{
		parent::setValue($value);

		if (is_numeric($value)){
			$this->setMainCategory($value);
		}

		if (is_array($value))
		{
			if (isset($value[self::CHOSEN_CATEGORY_NS]))
			{
				$this->setChosenCategories($value[self::CHOSEN_CATEGORY_NS]);
			}
			
			if (isset($value[self::MAIN_CATEGORY_NS]))
			{
				$this->setMainCategory($value[self::MAIN_CATEGORY_NS]);
			}
		}

		return $this;
	}

	/**
	 * @var integer
	 */
	protected $_mainCategory;
	
	/**
	 * @param integer $value
	 */
	public function setMainCategory($value)
	{
		$this->_value = (int) $value;
		$this->_mainCategory = (int) $value;
	}

	/**
	 * Zwracane jest główny rekord dla danego produktu.
	 * 
	 * - gdy nie został podana wartość a został podany primary key
	 *   wczytaj z bazy wartość głównej kategorii
	 * 
	 * @return integer 
	 */
	public function getMainCategory()
	{
		return $this->_mainCategory;
	}

	/**
	 * @var KontorX_Db_Table_Tree_Rowset_Abstract
	 */
	protected $_rowset;
	
	/**
	 * @param KontorX_Db_Table_Tree_Rowset_Abstract $rowset
	 */
	public function setCategoryRowset(KontorX_Db_Table_Tree_Rowset_Abstract $rowset)
	{
		$this->_rowset = $rowset;
	}
	
	public function getCategoryRowset()
	{
		if (null === $this->_rowset)
		{
			$categoryTable = new Shop_Model_DbTable_Category();
			$this->_rowset = $categoryTable->fetchAll();
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
	protected $_chosenCategories;
	
	/**
	 * @param array $rowset
	 */
	public function setChosenCategories(array $rowset)
	{
		$this->_chosenCategories = $rowset;
	}
	
	/**
	 * Wyszukuje kategorie, do których dany produkt została przypisany.
	 * @return array
	 */
	public function getChosenCategories()
	{
		if (null !== $this->_chosenCategories)
		{
			return $this->_chosenCategories;
		}

		$primaryKey = $this->getPrimaryKey();
		if (null === $primaryKey)
		{
			return $this->_chosenCategories;
		}

		$hasCategoryTable = new Shop_Model_DbTable_HasCategory();
		$select 		  = $hasCategoryTable->select(Zend_Db_Table_Abstract::SELECT_WITH_FROM_PART)
											 ->where('product_id = ?', $primaryKey, Zend_Db::INT_TYPE);

		/* @var $stmt Zend_Db_Statement */
		$stmt = $select->query();
		
		$this->_chosenCategories = array();
		while($categoryId = $stmt->fetchColumn(0))
		{
			$this->_chosenCategories[] = $categoryId; 
		}

		return $this->_chosenCategories;
	}
}