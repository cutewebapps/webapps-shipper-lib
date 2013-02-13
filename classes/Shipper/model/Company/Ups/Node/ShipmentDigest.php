<?php

/*
Example
<ShipmentAcceptRequest>
    <Request>
        <TransactionReference>
            <CustomerContext>Customer Comment</CustomerContext>
        </TransactionReference>
        <RequestAction>ShipAccept</RequestAction>
        <RequestOption>1</RequestOption>
    </Request>
    <ShipmentDigest>
        ShipmentDigest From ShipmentConfirmResponse
    </ShipmentDigest>
</ShipmentAcceptRequest>
*/

final class Shipper_Company_Ups_Node_ShipmentDigest extends Shipper_Company_Ups_Node
{
    private $shipmentDigest;
    public function __construct($in_shipmentDigest)
    {
        $this->shipmentDigest = $in_shipmentDigest;
    }
    public function toXml()
    {
        return '<ShipmentDigest>'.$this->shipmentDigest.'</ShipmentDigest>'."\n";
    }
}