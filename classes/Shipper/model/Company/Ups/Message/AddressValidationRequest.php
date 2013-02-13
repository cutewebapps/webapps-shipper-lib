<?php
class Shipper_Company_Ups_Message_AddressValidationRequest extends Shipper_Company_Ups_Message
{
	const XpciVersion = '1.0';
	
	const OptionAddressValidation                  = '1';
	const OptionAddressClassification              = '2';
	const OptionAddressValidationAndClassificaion  = '3';
	
	private $_addressKeyFormat;
	private $_customerContext;
	private $_requestedOption;
	
	public function __construct($in_xmlVersion=false, $in_encoding=false)
	{
		parent::__construct($in_xmlVersion, $in_encoding);
		
		$this->_addressKeyFormat = null;
		$this->_customerContext = '';
		$this->_requestedOption = '3';
	}
	public function setCustomerContext($in_customerContext)
	{
		$this->_customerContext = $in_customerContext;
	}
	public function setNodes( 
	   Shipper_Company_Ups_Node_AddressKeyFormat $in_addressKeyFormat, 
	   $in_requestedOption )
	{
		if($in_requestedOption != Shipper_Company_Ups_Message_AddressValidationRequest::OptionAddressClassification &&
			$in_requestedOption != Shipper_Company_Ups_Message_AddressValidationRequest::OptionAddressValidation &&
			$in_requestedOption != Shipper_Company_Ups_Message_AddressValidationRequest::OptionAddressValidationAndClassificaion)
		{
			throw new Shipper_Exception('Invalid Address Validation Request Option!');
		}
		$this->_requestedOption = $in_requestedOption;
		$this->_addressKeyFormat = $in_addressKeyFormat;
	}
	public function toXml()
	{
		$retValue = "<?xml version=\"1.0\"?>
<AddressValidationRequest xml:lang=\"en-US\">
    <Request>
        <TransactionReference>";
		if(!empty($this->_customerContext))
		{
			$retValue .= '<CustomerContext>'.$this->_customerContext.'</CustomerContext>'."\n";
		}
		
		$retValue .= '<XpciVersion>'
		  .Shipper_Company_Ups_Message_AddressValidationRequest::XpciVersion
		  .'</XpciVersion>'
		  .'</TransactionReference>'
		  ."\n"
		  .'      <RequestAction>XAV</RequestAction>'."\n";
		if(!empty($this->_requestedOption))
		{
			$retValue .= '<RequestOption>'.$this->_requestedOption."</RequestOption>\n";
		}
		$retValue .= "</Request>";
		if(!empty($this->_addressKeyFormat) && !is_null($this->_addressKeyFormat))
		{
			$retValue .= $this->_addressKeyFormat->toXml();
		}
		return $retValue."</AddressValidationRequest>";
	}
}