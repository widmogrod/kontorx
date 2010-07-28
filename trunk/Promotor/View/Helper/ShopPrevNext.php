<?php
/**
 * Wyświetlanie poprzedniego i następnego produktu
 * - poprzedni i nasępnu produkt
 * - poprzedni i nasępnu produkt w kategorii
 * - poprzedni i nasępnu produkt w etykiecie
 * 
 * 
 * @author $Author$
 * @version $Id$
 */
class Promotor_View_Helper_ShopPrevNext extends Zend_View_Helper_Abstract
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
		self::PRODUCT 	=> 'getPrevNextProduct',
		self::CATEGORY 	=> 'getPrevNextCategory',
		self::TAG 		=> 'getPrevNextTag',
	);
	
	/**
	 * @var array
	 */
	protected $_typeName = array(
		self::PRODUCT 	=> '',
		self::CATEGORY 	=> 'wzoru',
		self::TAG 		=> 'motywu',
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
	 * @return Shop_Model_Product_PrevNext_Interface
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
		$type = ucfirst($type);
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
	 * @return Promotor_View_Helper_ShopPrevNext
	 */
	public function shopPrevNext($productId, $type = null, $groupId = null) 
	{
		$this->reset();
		$this->setProductId($productId);
		
		if (null === $type) {
			$type = $typeName = $this->view->getHelper('ShopHistory')->type;
			$this->setType($type);
		}
		
		if (null === $groupId) {
			$groupId = $typeName = $this->view->getHelper('ShopHistory')->id;
			$this->setGroupId($groupId);
		}
		
		return $this;
	}

	/**
	 * Wyświetlenia produktów.
	 */
	public function render() 
	{
		$result = '';
		
		$templateExists	= '<li class="%s"><a href="%s" title="%s"><img src="%s" alt="%s"/><span>%s</span></a></li>';
		$templateNoName = '<li class="%s end"><img src="%s" alt="%s"/><span>%s</span></li>';

		$data = $this->getData();

		$d = array(
			'prev' => $data->getPrevData(),
			'next' => $data->getNextData()
		);

		foreach ((array) $d as $key => $value)
		{
			$class 	= $key;
				
			if (count($value)) {
				// przygotowywanie danych istniejącego rekordu
				$href 	= $this->view->url($value, 'shop-product');
				$title 	= $value['name'];
				$src 	= 'upload/shop/product/small_width/' . $value['image'];
				
				$description = ($key == 'prev')
					? '&laquo; poprzedni'
					: 'następny &raquo;';
				
				$result .= sprintf($templateExists, $class, $href, $title, $src, $title, $description);
			} else {
				// przygotowywanie danych "noname"
				$src 	= 'upload/shop/small_crop/prevNext-end.jpg';
				
				$description = ($key == 'prev')
					? 'początek'
					: 'koniec';
				
				$result .= sprintf($templateNoName, $class, $src, $description, $description);
			}
		}
		
		return '<ul class="browser">' . $result . '</ul>';
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