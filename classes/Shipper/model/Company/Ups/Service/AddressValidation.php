<?php

final class Shipper_Company_Ups_Service_AddressValidation extends Shipper_Company_Ups_Service
{
    
	const addressValidationRemoteAddressDebug = 'https://wwwcie.ups.com/ups.app/xml/XAV';
	const addressValidationRemoteAddress = 'https://onlinetools.ups.com/ups.app/xml/XAV';
	
	public function addressValidationRequest( Shipper_Company_Ups_Message_AddressValidationRequest $in_msg)
	{
		$this->initChannel(self::addressValidationRemoteAddress);
		$test = new Shipper_Company_Ups_Message_AccessRequest('1.0');
		
		$test->setCredentials($this->userId, $this->password, $this->licenseNumber);
		$this->sendRequest($test->toXML().$in_msg->toXml());
	}
	public function getErrorMessage()
	{
		$items = $this->xmlReader->xpath('/AddressValidationResponse/Response/Error');
        if (count($items) > 0) {
            return $items[0]->ErrorDescription;
        } else {
            return '';
        }
	}
	public function getErrorCode()
	{
		$items = $this->xmlReader->xpath('/AddressValidationResponse/Response/Error');
        if (count($items) > 0) {
            return $items[0]->ErrorCode;
        } else {
            return 0;
        }
	}
	public function isAmbiguousAddress()
	{
		$items = $this->xmlReader->xpath('/AddressValidationResponse/AmbiguousAddressIndicator');
        if (count($items) > 0) {
            return true;
        } else {
            return false;
        }
	}
	public function getAddressCanidates()
	{
		$items = $this->xmlReader->xpath('/AddressValidationResponse/AddressKeyFormat');
        if (count($items) > 0) {
            $retValue = array();
            foreach ($items as $key => $value) {
                $addressLines = $value->xpath('/AddressLine');
                $retValue[] = array(
                'AddressLine1' => (string) $value->AddressLine[0], 
                'AddressLine2' => (string) $value->AddressLine[1], 
                'Region' => (string) $value->Region, 
                'PoliticalDivision2' => (string) $value->PoliticalDivision2, 
                'PoliticalDivision1' => (string) $value->PoliticalDivision1, 
                'PostcodePrimaryLow' => (string) $value->PostcodePrimaryLow, 
                'PostcodeExtendedLow' => (string) $value->PostcodeExtendedLow, 
                'CountryCode' => (string) $value->CountryCode);
            }
            return $retValue;
        } 
        return array();
        
	}
	public function getValidatedAddressLine1()
	{
		$lines = $this->xmlReader->xpath('/AddressValidationResponse/AddressKeyFormat/AddressLine');
        if (isset($lines[0])) 
            return (string) $lines[0];
        return '';
	}
	public function getValidatedAddressLine2()
	{
		$lines = $this->xmlReader->xpath('/AddressValidationResponse/AddressKeyFormat/AddressLine');
        if (isset($lines[1])) 
            return (string) $lines[1];
        return '';
	}
	public function getValidatedCity()
	{
		$validatedAddress = $this->xmlReader->xpath('/AddressValidationResponse/AddressKeyFormat');
		$validatedAddress = $validatedAddress[0];
		return (string)$validatedAddress->PoliticalDivision2;
	}
	public function getValidatedState()
	{
		$validatedAddress = $this->xmlReader->xpath('/AddressValidationResponse/AddressKeyFormat');
		$validatedAddress = $validatedAddress[0];
		return (string)$validatedAddress->PoliticalDivision1;
	}
	public function getValidatedZip()
	{
		$validatedAddress = $this->xmlReader->xpath('/AddressValidationResponse/AddressKeyFormat');
		$validatedAddress = $validatedAddress[0];
		return (string)$validatedAddress->PostcodePrimaryLow;
	}
	public function getValidatedPostalLow()
	{
		$validatedAddress = $this->xmlReader->xpath('/AddressValidationResponse/AddressKeyFormat');
		$validatedAddress = $validatedAddress[0];
		return (string)$validatedAddress->PostcodeExtendedLow;
	}
	public function getValidatedCountry()
	{
		$validatedAddress = $this->xmlReader->xpath('/AddressValidationResponse/AddressKeyFormat');
		$validatedAddress = $validatedAddress[0];
		return (string)$validatedAddress->CountryCode;
	}
	public function isNoCandidates()
	{
		$items = $this->xmlReader->xpath('/AddressValidationResponse/NoCandidatesIndicator');
        if (count($items) > 0) 
            return true;
        return false;
	}
	public function isValidAddress()
	{
		$items = $this->xmlReader->xpath('/AddressValidationResponse/ValidAddressIndicator');
        if (count($items) > 0) 
            return true;
        return false;
	}
}