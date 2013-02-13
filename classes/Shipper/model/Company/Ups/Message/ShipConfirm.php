<?php
final class Shipper_Company_Ups_Message_ShipConfirm extends Shipper_Company_Ups_Message
{
	private $request;
	private $labelSpecifications;
	private $shipper;
	private $shipFrom;
	private $shipTo;
	private $payment;
	private $service;
	private $shipmentServiceOptions;
	private $packages;
	private $shipmentDescription;
	private $invoiceLineTotal;
	private $returnService = null;
	
	public function setInvoiceLineTotal( Shipper_Company_Ups_Node_InvoiceLineTotal $line )
	{
		$this->invoiceLineTotal = $line;
	}
	public function __construct($in_xmlVersion=false, $in_encoding=false)
	{
        parent::__construct($in_xmlVersion, $in_encoding);
	    
        $this->shipmentDescription = '';
		$this->invoiceLineTotal = null;
		$this->returnService = null;
	}

	
	/**
	 * @param Shipper_Company_Ups_Node_Request $nodeRequest
	 * @param Shipper_Company_Ups_Node_LabelSpecification $nodeLabelSpecification
	 * @param Shipper_Company_Ups_Node_Address_Shipper $nodeShipper
	 * @param Shipper_Company_Ups_Node_Address_ShipFrom $nodeShipFrom
	 * @param Shipper_Company_Ups_Node_Address_ShipTo $nodeShipTo
	 * @param Shipper_Company_Ups_Node_PaymentInformation $nodePayment
	 * @param Shipper_Company_Ups_Node_Service $nodeService
	 * @param Shipper_Company_Ups_Node_ShipmentServiceOption $nodeShipmentServiceOptions
	 * @param Shipper_Company_Ups_Node_Package or array of them $nodePackages
	 */
	public function setNodes(
	    Shipper_Company_Ups_Node_Request               $nodeRequest,
		Shipper_Company_Ups_Node_LabelSpecification    $nodeLabelSpecification,
		Shipper_Company_Ups_Node_Address_Shipper       $nodeShipper,
		Shipper_Company_Ups_Node_Address_ShipFrom      $nodeShipFrom,
		Shipper_Company_Ups_Node_Address_ShipTo        $nodeShipTo,
		Shipper_Company_Ups_Node_PaymentInformation    $nodePayment,
		Shipper_Company_Ups_Node_Service               $nodeService,
		Shipper_Company_Ups_Node_ShipmentServiceOption $nodeShipmentServiceOptions, 
		$nodePackages)
	{
		$this->request = $nodeRequest;
		$this->labelSpecifications = $nodeLabelSpecification;
		$this->shipper = $nodeShipper;
		$this->shipFrom = $nodeShipFrom;
		$this->shipTo = $nodeShipTo;
		$this->payment = $nodePayment;
        $this->service = $nodeService;
		$this->shipmentServiceOptions = $nodeShipmentServiceOptions;
		$this->packages = $nodePackages;
	}
	public function setReturnType($in_returnType = true )
	{
		$this->returnService = $in_returnType;
	}
    public function setShipmentDescription($in_shipmentDescription)
    {
        $this->shipmentDescription = $in_shipmentDescription;
    }
    public function toXml()
	{
		if ($this->returnService)
			$this->shipmentServiceOptions->setReturnService(true);

		$retValue = '<'.'?xml version="1.0"?'.'>
		<ShipmentConfirmRequest xml:lang="en-US">'."\n";
		$retValue .= $this->request->toXml().
			$this->labelSpecifications->toXml().
			'<Shipment>'."\n".
			$this->shipper->toXml().
			$this->shipFrom->toXml().
			$this->shipTo->toXml().
			$this->payment->toXml().
			$this->service->toXml().
			$this->shipmentServiceOptions->toXml();;
		
       
		if ( is_array($this->packages) ) {
			foreach($this->packages as $key=>$value)
				if($value instanceof Shipper_Company_Ups_Node_Package)
					$retValue .= $value->toXml();
		} else if ($this->packages instanceof Shipper_Company_Ups_Node_Package)	{
			$retValue .= $this->packages->toXml();
		} else {
			throw new Shipper_Exception('Argument Exception!'.__CLASS__.'::'.__METHOD__);
		}
		
		if($this->shipmentDescription != '')
			$retValue .= "<Description>{$this->shipmentDescription}</Description>\n";
		if(!is_null($this->invoiceLineTotal))
			$retValue .= $this->invoiceLineTotal->toXml();
		if(!is_null($this->returnService))
			$retValue .= '<ReturnService><Code>9</Code></ReturnService>'."\n";

		$retValue .= '</Shipment>'."\n";
		return $retValue.'</ShipmentConfirmRequest>'."\n";
	}
}
