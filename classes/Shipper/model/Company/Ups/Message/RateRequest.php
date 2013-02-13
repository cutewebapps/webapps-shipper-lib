<?php
final class Shipper_Company_Ups_Message_RateRequest extends Shipper_Company_Ups_Message
{
    private $pickup;
    private $request;
    private $shipper;
    private $shipTo;
    private $shipFrom;
    private $service;
    private $payment;
    private $packages;
    private $serviceOptions;

    /**
     * @param str $in_xmlVersion
     * @param str $in_encoding
     * @return void
     */
    public function __construct($in_xmlVersion=false, $in_encoding=false)
    {
        #throw new Shipper_Exception( 'Using UPS rate-request interface that was never working' ); 
        parent::__construct($in_xmlVersion, $in_encoding);
    }


    /**
     * @param Shipper_Company_Ups_Node_Request $in_request
     * @param Shipper_Company_Ups_Node_Pickup $in_pickup
     * @param Shipper_Company_Ups_Node_Address_Shipper $in_shipper
     * @param Shipper_Company_Ups_Node_Address_ShipTo $in_shipTo
     * @param Shipper_Company_Ups_Node_Address_ShipFrom $in_shipFrom
     * @param Shipper_Company_Ups_Node_Service $in_service
     * @param Shipper_Company_Ups_Node_PaymentInformation $in_paymentInformation
     * @param Shipper_Company_Ups_Node_Package | array $in_packages
     * @param Shipper_Company_Ups_Node_ShipmentServiceOption $in_shipmentServiceOptions
     * @return void
     */
    public function setNodes(
        Shipper_Company_Ups_Node_Request $in_request, 
        Shipper_Company_Ups_Node_Pickup $in_pickup, 
        Shipper_Company_Ups_Node_Address_Shipper $in_shipper, 
        Shipper_Company_Ups_Node_Address_ShipTo $in_shipTo,
        Shipper_Company_Ups_Node_Address_ShipFrom $in_shipFrom,
        Shipper_Company_Ups_Node_Service $in_service,
        Shipper_Company_Ups_Node_PaymentInformation $in_paymentInformation,
        $in_packages,
        Shipper_Company_Ups_Node_ShipmentServiceOption $in_shipmentServiceOptions)
    {

        $this->packages = $in_packages;
        $this->pickup = $in_pickup;
        $this->request = $in_request;
        $this->shipper = $in_shipper;
        $this->shipTo = $in_shipTo;
        $this->shipFrom = $in_shipFrom;
        $this->service = $in_service;
        $this->payment = $in_paymentInformation;
        $this->serviceOptions = $in_shipmentServiceOptions;
    }
    
    public function toXml()
    {
        $retValue = '<?xml version="1.0"?>
        <RatingServiceSelectionRequest xml:lang="en-US">';
        $retValue .=
            $this->request->toXml().
            $this->pickup->toXml().
            '<Shipment>'.
            $this->shipper->toXml().
            $this->shipFrom->toXml().
            $this->shipTo->toXml().
            $this->service->toXml().
            $this->payment->toXml();
            
        if($this->packages instanceof Shipper_Company_Ups_Node_Package)
        {
            $retValue .= $this->packages->toXml();
        }
        else
        {
            $type = gettype($this->packages);
            if($type == 'array')
            {
                foreach($this->packages as $key=>$value)
                {
                    if($value instanceof Shipper_Company_Ups_Node_Package)
                    {
                        $retValue .= $value->toXml();
                    }
                }
            }
            else
            {
                throw new Shipper_Exception('Argument Exception.'.__CLASS__.'::'.__METHOD__);
            }
        }
        $retValue .= $this->serviceOptions->toXml();
        $retValue .= '</Shipment>';
        return $retValue.'</RatingServiceSelectionRequest>';
    }
}
