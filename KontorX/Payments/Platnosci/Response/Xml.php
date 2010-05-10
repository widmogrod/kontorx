<?php
/*
<response>
        <status>OK</status>
        <trans>
                <id>7</id>
                <pos_id>1</pos_id>
                <session_id>417419</session_id>
                <order_id></order_id>
                <amount>200</amount>
                <status>5</status>
                <pay_type>t</pay_type>
                <pay_gw_name>pt</pay_gw_name>
                <desc>Wpłata dla test@test.pl</desc>
                <desc2></desc2>
                <create>2004-08-23 10:39:52</create>
                <init>2004-08-31 13:42:43</init>
                <sent>2004-08-31 13:48:13</sent>
                <recv></recv>
                <cancel></cancel>
                <auth_fraud>0</auth_fraud>
                <ts>1094205828574</ts>
                <sig>a95dc2145079b16a3668175279c35736</sig>                             
           </trans>
</response>
*/

/**
 * Prztwarzanie odpowiedzi z platnosci.pl
 * 
 * @version $Id$
 * @author Gabriel Habryn, widmogrod@gmail.com
 * @license MIT License
 */
class KontorX_Payments_Platnosci_Response_Xml
{
	protected $_xml;

	public function __construct($responseXML)
	{
		if (false === ($this->_xml = simplexml_load_string($responseXML)))
		{
			throw new KontorX_Payments_Platnosci_Response_Exception('Nierozpoznane responseXML "'.$responseXML.'"');
		}
	}

	/**
	 * Status przetworzenia komunikatu - dla prawidłowego „OK”
	 */
	public function getStatus($asBool = true) 
	{
		if ($asBool)
		{
			switch($this->_xml->status)
			{
				case 'OK': return true;
				case 'ERROR': return false;
				default: return false;
			}
		}

		return $this->_xml->status;
	}

	/**
	 * @return multitype:NULL 
	 */
	public function getStatusError()
	{
		if ($this->_xml->status == 'ERROR') 
		{
			$message = KontorX_Payments_Platnosci::getErrorByCode($this->_xml->error->nr);
			return array(
				$this->_xml->error->nr,
				$message,
				'nr' => $this->_xml->error->nr,
				'message' => $message
			);
		}
	}

	/**
	 * Unikalny identyfikator transakcji nadawany przez Platnosci.pl
	 */
	public function getId()
	{
		return $this->_xml->trans->id;
	}

	/**
	 * Identyfikator Posa dla jakiego utworzono transakcję
	 */
	public function getPosId()
	{
		return $this->_xml->trans->pos_id;
	}
	
	/**
	 * Wartość nadana przez aplikację Sklepu podczas tworzenia transakcji
	 */
	public function getSessionId()
	{
		return $this->_xml->trans->session_id;
	}
	
	/**
	 * wartość nadana przez aplikację Sklepu
	 * podczas tworzenia transakcji
	 */
	public function getOrderId()
	{
		return $this->_xml->trans->order_id;
	}
	
	/**
	 * aktualna wartość transakcji w groszach 
	 */
	public function getAmount()
	{
		return $this->_xml->trans->amount;
	}
	
	/**
	 * aktualny status transakcji
	 * @param bool $description - jest pobierany opis status 
	 * 							  a nie jego numeryczna wartość
	 */
	public function getTransStatus($description = false)
	{
		if (true === $description)
		{
			return KontorX_Payments_Platnosci::getStatusTypes($this->_xml->trans->status);
		}
		
		return $this->_xml->trans->status;
	}
	
	/**
	 * typ płatności zgodnie z punktem 
	 */
	public function getPayType()
	{
		return $this->_xml->trans->pay_type;
	}
	
	/**
	 * nazwa   bramki   realizującej transakcję 
	 * - informacja wewnętrzna aplikacji Platnosci.pl
	 */
	public function getPayGwName()
	{
		return $this->_xml->trans->pay_gw_name;
	}
	
	/**
	 * wartość nadana przez aplikację Sklepu podczas tworzenia transakcji
	 */
	public function getDesc()
	{
		return $this->_xml->trans->desc;
	}
	
	/**
	 * wartość nadana przez aplikację Sklepu podczas tworzenia transakcji
	 */
	public function getDesc2()
	{
		return $this->_xml->trans->desc2;
	}
	
	/**
	 * data utworzenia transakcji 
	 */
	public function getCreate()
	{
		return $this->_xml->trans->create;
	}
	
	/**
	 * data rozpoczęcia transakcji
	 */
	public function getInit()
	{
		return $this->_xml->trans->init;
	}
	
	/**
	 * data przekazania transakcji do odbioru
	 */
	public function getSent()
	{
		return $this->_xml->trans->sent;
	}
	
	/**
	 * data odbioru transakcji
	 */
	public function getRecv()
	{
		return $this->_xml->trans->recv;
	}
	
	/**
	 * data anulowania transakcji
	 */
	public function getCancel()
	{
		return $this->_xml->trans->cancel;
	}
	
	/**
	 * informacja   wewnętrzna aplikacji Platnosci.pl
	 */
	public function getAuthFraud()
	{
		return $this->_xml->trans->auth_fraud;
	}

	/**
	 * wartość potrzebna do obliczenia podpisu
	 */
	public function getTs()
	{
		return $this->_xml->trans->ts;
	}

	/**
	 * podpis komunikatu - wynik funkcji md5 
	 */
	public function getSig()
	{
		/**
		 * W danych odesłanych przez Platnosci.pl wartość sig, obliczamy według następującego wzoru:
		 * sig = md5(pos id + session id + order id + status + amount + desc + ts + key2)
		 */

		return $this->_xml->trans->sig;
	}
}