<?php
final class Shipper_Company_Ups_Message_VoidShipment extends Shipper_Company_Ups_Message
{
    private $_request;
    private $_idNumber;
    
    public function __construct($in_xmlVersion=false, $in_encoding=false)
    {
        parent::__construct($in_xmlVersion, $in_encoding);
    }
    
    public function setNodes(RequestNode $in_request, $in_idNumber)
    {
        $this->request = $in_request;
        $this->idNumber = $in_idNumber;
    }
    
    public function toXml()
    {
        $retValue = '<?xml version="1.0"?>
        <VoidShipmentRequest>';
        $retValue .= $this->request->toXml().
            '<ShipmentIdentificationNumber>'.$this->idNumber.'</ShipmentIdentificationNumber>';
        return $retValue.'</VoidShipmentRequest>';
    }
}