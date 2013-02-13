<?php
/*
<?xml version="1.0" encoding="ISO-8859-1"?>
<ShipmentAcceptRequest>
	<Request>
		<TransactionReference>
			<CustomerContext>Customer Comment</CustomerContext>
		</TransactionReference>
		<RequestAction>ShipAccept</RequestAction>
		<RequestOption>1</RequestOption>
	</Request>
	<ShipmentDigest></ShipmentDigest>
</ShipmentAcceptRequest>
*/


final class Shipper_Company_Ups_Message_ShipAccept extends Shipper_Company_Ups_Message
{
	private $_request;
	private $_shipmentDigest;
	public function __construct($in_xmlVersion=false, $in_encoding=false)
	{
		parent::__construct($in_xmlVersion, $in_encoding);
	}
	public function setNodes(
	       Shipper_Company_Ups_Node_Request $in_request, 
	       Shipper_Company_Ups_Node_ShipmentDigest $in_shipmentDigest)
	{
		$this->_request = $in_request;
		$this->_shipmentDigest = $in_shipmentDigest;
	}
	public function toXml()
	{
		$retValue = '<?xml version="1.0" encoding="ISO-8859-1"?>
		<ShipmentAcceptRequest>'."\n";
		$retValue .= $this->_request->toXml().
			$this->_shipmentDigest->toXml();
		return $retValue
        .'</ShipmentAcceptRequest>'."\n";
	}
}
