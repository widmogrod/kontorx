<?php
class Promotor_View_Helper_ShopBreadcrumbs extends Zend_View_Helper_Abstract
{
	const PRODUCT 	= 'Product';
	const TAG 		= 'ProductTag';
	const CATEGORY 	= 'Category';
	
	// TODO manufacturer
	// TODO search
	
	
	/**
	 * @var int
	 */
	protected $_productId;
	
	/**
	 * @var int
	 */
	protected $_groupId;
	
	/**
	 * @var Shop_Model_Product
	 */
	protected $_model;
	
	/**
	 * @var Shop_Model_Product_PrevNext_Interface
	 */
	protected $_data;
	
	/**
	 * @var string
	 */
	protected $_type;
	
	/**
	 * Dostępne typy akcji z wskazaniem na nazwe wywoływanej metody
	 * 
	 * @var array
	 */
	protected $_methodTypes = array(
		self::PRODUCT 	=> 'getBreadcrumbsProduct',
		self::CATEGORY 	=> 'getBreadcrumbsCategory',
		self::TAG 		=> 'getBreadcrumbsTag',
	);
	
	/**
	 * @var array
	 */
	protected $_typeName = array(
		self::PRODUCT 	=> 'wszystkie produkty',
		self::CATEGORY 	=> 'układ kalendarza',
		self::TAG 		=> 'motyw na kalendarzu',
	);
	
	/**
	 * Zwróć czytelną nazwę typu produktu,
	 * w/g którego są "przewijane" produkty
	 *  
	 * @return string|null
	 */
	public function getTypeName() 
	{
		return isset($this->_typeName[$this->_type])
			? $this->_typeName[$this->_type]
			: null;
	}

	/**
	 * Pobiera nazwę grupy z danych dostarczonych przez model
	 * 
	 * @return string
	 */
	public function getGroupName() 
	{
		$data = $this->getData();
		return $data->getGroupName();
	}
	
	/**
	 * Pobierz obiekt modelu 
	 * i wywołaj na nim odpowiednią metodę 
	 * w celu pobrania danych
	 * 
	 * @return getNavigation
	 */
	public function getData() 
	{
		if (null !== $this->_data)
			return $this->_data;

		if (null === $this->_model)
			$this->_model = new Shop_Model_Product();

		// gdy identyfikator grupy jest pusty wtedy 
		// uruchomiana jest metoda z grupy self::PRODUCT
		// dlatego że jako jedyna nie wymaga parametru
		if (null === $this->_groupId)
			$this->setType(self::PRODUCT);
					
		$methodName = $this->_methodTypes[$this->_type] . 'Cache';

		// call..
		$this->_data = $this->_model->$methodName($this->_productId, $this->_groupId);
		
		return $this->_data;
	}

	/**
	 * Czyszczenie wszystkich zmiennych
	 * 
	 * @return void
	 */
	public function reset() 
	{
		$this->_data = null;
		$this->_groupId = null;
		$this->_type = self::PRODUCT;
	}

	/**
	 * Ustaw typ danych
	 * 
	 * @param string $type
	 */
	public function setType($type) 
	{
		if (array_key_exists($type, $this->_methodTypes))
			$this->_type = $type;
			
		return $this;
	}

	/**
	 * Ustaw ID produktu w okolicach którego będa poszukiwane produkty
	 * 
	 * @param integer $productId
	 * @return Promotor_View_Helper_ShopPrevNext
	 */
	public function setProductId($productId) 
	{
		$this->_productId = (int) $productId;
		return $this;
	}
	
	/**
	 * Ustaw ID dlas dodatkowego sposobu grupowania tj.
	 * - poprzedni i nasępnu produkt w kategorii
	 * - poprzedni i nasępnu produkt w etykiecie
	 * 
	 * @param integer|string $groupId
	 * @return Promotor_View_Helper_ShopPrevNext
	 */
	public function setGroupId($groupId) 
	{
		$this->_groupId = $groupId;
		return $this;
	}

	/**
	 * Głowna metoda incjująca działanie helpera
	 * 
	 * @param integer $productId
	 * @param string $type
	 * @param string $groupId
	 * @return Promotor_View_Helper_ShopBreadcrumbs
	 */
	public function shopBreadcrumbs($productId, $type = null, $groupId = null) 
	{
		$this->reset();
		$this->setProductId($productId);

		if (null === $type) {
			$type = $this->view->getHelper('ShopHistory')->type;
			$this->setType($type);
		}
		
		if (null === $groupId) {
			$groupId = $this->view->getHelper('ShopHistory')->id;
			$this->setGroupId($groupId);
		}
		
		return $this;
	}

	/**
	 * Wyświetlenia nawigacji
	 * @return Zend_Navigation
	 */
	public function render() 
	{
		$container = $this->getData();

		$breadcrumbs = $this->view->getHelper('navigation')
			->setContainer($container)
			->menu()
			->setPartial('_partial/breadcrumbs.phtml');

		return $breadcrumbs;
	}
	
	/**
	 * @return string
	 */
	public function __toString() 
	{
		try {
			return (string) $this->render();
		} catch (Exception $e) {
			$error = sprintf('%s::%s[%d]', get_class($e), $e->getMessage(), $e->getLine());
			trigger_error($error, E_USER_WARNING);
		}
		return '';
	}
}