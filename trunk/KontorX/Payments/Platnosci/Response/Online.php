<?php
/**
 * @author gabriel
 *
 */
class KontorX_Payments_Platnosci_Response_Online
{
	/**
	 * pos id     identyfikator Posa
	 * session id wartość podana przez Sklep w trakcie tworzenia płatności
	 * ts         znacznik czasowy, wartość potrzebna w celu weryfikacji podpisu
	 * sig		  podpis przesłanej informacji
	 * 
	 * @var array 
	 */
	protected $_data = array(
		'pos_id' => null,
		'session_id' => null,
		'ts' => null,
		'sig' => null,
	);
	/**
	 * @param array $postData
	 */
	public function __construct(array $postData)
	{
		$data = array_intersect_key($postData, $this->_data);
		if (count($data) !== 4)
		{
			throw new KontorX_Payments_Exception('KontorX_Payments_Platnosci_Response_Online wrong data');
		}
		$this->_data = $data;
	}

	public function getPosId()
	{
		return $this->_data['pos_id'];
	}
	
	public function getSessionId()
	{
		return $this->_data['session_id'];
	}
	
	public function getTs()
	{
		return $this->_data['ts'];
	}
	
	public function getSig()
	{
		return $this->_data['sig'];
	}
}