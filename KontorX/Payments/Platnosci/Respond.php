<?php
class KontorX_Payments_Platnosci_Respond
{
	
	/**
	 * Stałe przekazywane w adresie powrotu 
	 * @var array
	 */
	protected $_urlParams = array(
		// identyfikator nowej transakcji utworzonej w aplikacji Platnosci.pl
		'trans_id' => '%transId%',
	 	// wartości pos id
		'pos_id' => '%posId%',
		// wartości pay type
		'pay_type' => '%payType%',
		// wartości session id
		'session_id' => '%sessionId%',
		// wartości amount - jako separator kropka
		'amount_ps' => '%amountPS%',
		// wartości amount - jako separator przecinek
		'amount_cs' => '%amountCS%',
		// wartości order id
		'order_id' => '%orderId%',
		// numer błędu zgodnie z tabelką 2.1 (s. 3), jest wykorzystywany tylko przy - UrlNegatywny
		'error' => '%error%'
	);

	/**
	 * @var array
	 */
	protected $_params;

	public function __construct(array $data)
	{
		// pobierz wartości które nas interesują
		$this->_params = array_intersect_key($data, $this->_urlParams);
		
		$values = array_fill(0, count($this->_urlParams), null);
		$keys   = array_keys($this->_urlParams);
		$params = array_combine($keys, $values);
		
		// dopełnij null jeżeli wartości podstawowe nie zostały uwzględnione
		$this->_params = array_merge($params, $this->_params);
	}

	/**
	 * identyfikator nowej transakcji utworzonej w aplikacji Platnosci.pl
	 * @return string
	 */
	public function getTransId()
	{
		return $this->_params['trans_id'];
	}

	/**
	 * wartości pos id
	 * 
	 * @return string
	 */
	public function getPosId()
	{
		return $this->_params['pos_id'];
	}

	/**
	 * Wartości pay type
	 * 
	 * @return string
	 */
	public function getPayType()
	{
		return $this->_params['pay_type'];
	}

	/**
	 * wartości session id
	 * 
	 * @return string
	 */
	public function getSessionId()
	{
		return $this->_params['session_id'];
	}

	/**
	 * wartości amount - jako separator kropka
	 * 
	 * @return numeric
	 */
	public function getAmountPs()
	{
		return $this->_params['amount_ps'];
	}

	/**
	 * wartości amount - jako separator przecinek
	 * 
	 * @return numeric
	 */
	public function getAmountCs()
	{
		return $this->_params['amount_cs'];
	}

	/**
	 * wartości order id
	 * 
	 * @return integer
	 */
	public function getOrderId()
	{
		return $this->_params['order_id'];
	}

	/**
	 * numer błędu zgodnie z tabelką 2.1 (s. 3), jest wykorzystywany tylko przy - UrlNegatywny
	 * 
	 * @return integer
	 */
	public function getError()
	{
		return $this->_params['error'];
	}
}