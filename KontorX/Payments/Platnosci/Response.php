<?php
class KontorX_Payments_Platnosci_Response
{
	const XML = 'XML';
	const TXT = 'TXT';

	public function factory($responseData, $type = null)
	{
		switch ($type)
		{
			case self::XML:
				return new KontorX_Payments_Platnosci_Response_Xml($responseData);
				break;

			case self::TXT:
				throw new KontorX_Payments_Platnosci_Response_Exception("exception type not implemented");
				break;

			default:
				throw new KontorX_Payments_Platnosci_Response_Exception("exception type undefinded");
		}
	}
}