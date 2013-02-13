<?php
final class Shipper_Company_Ups_Service_Ship extends Shipper_Company_Ups_Service
{
	const shipConfirmRemoteAddress      = 'https://www.ups.com/ups.app/xml/ShipConfirm';
	const shipAcceptRemoteAddress       = 'https://www.ups.com/ups.app/xml/ShipAccept';
	const shipVoidRemoteAddress         = 'https://www.ups.com/ups.app/xml/Void';
	
	const shipConfirmRemoteAddressDebug = 'https://wwwcie.ups.com/ups.app/xml/ShipConfirm';
	const shipAcceptRemoteAddressDebug  = 'https://wwwcie.ups.com/ups.app/xml/ShipAccept';
	const shipVoidRemoteAddressDebug    = 'https://wwwcie.ups.com/ups.app/xml/Void';
    
	public function shipConfirmRequest (
            Shipper_Company_Ups_Message_ShipConfirm $in_msg)
    {
        if ($this->isTest) {
            $this->initChannel(self::shipConfirmRemoteAddressDebug);
        } else {
            $this->initChannel(self::shipConfirmRemoteAddress);
        }
        $test = new Shipper_Company_Ups_Message_AccessRequest( '1.0');
        $test->setCredentials($this->userId, $this->password, $this->licenseNumber);
        $this->sendRequest( $test->toXML() . $in_msg->toXml());
    }
    
    public function shipAcceptRequest (
            Shipper_Company_Ups_Message_ShipAccept $in_msg)
    {
        if ($this->isTest) {
            $this->initChannel(self::shipAcceptRemoteAddressDebug);
        } else {
            $this->initChannel(self::shipAcceptRemoteAddress );
        }
        $test = new Shipper_Company_Ups_Message_AccessRequest('1.0');
        $test->setCredentials($this->userId, $this->password, $this->licenseNumber);
        $this->sendRequest( $test->toXML() . $in_msg->toXml());
    }
    
    public function voidShipmentRequest (
            Shipper_Company_Ups_Message_VoidShipment $in_msg)
    {
        if ($this->isTest) {
            $this->initChannel( self::shipVoidRemoteAddressDebug );
        } else {
            $this->initChannel( self::shipVoidRemoteAddress );
        }
        $test = new Shipper_Company_Ups_Message_AccessRequest('1.0');
        $test->setCredentials($this->userId, $this->password, $this->licenseNumber);
        $this->sendRequest( $test->toXML() . $in_msg->toXml() );
    }
	
	public function getShipmentDigest()
	{
		return $this->getResponseValue( 'ShipmentConfirmResponse', '/ShipmentDigest' );
	}
	
	/**
	 * @return array of trackings 
	 */
	public function getOrderTracking()
	{
	    $strTracking = $this->getResponseValue( 'ShipmentAcceptResponse', '/ShipmentResults/PackageResults/TrackingNumber' );
	    if ( $strTracking ) return $strTracking;
	    $strTracking = $this->getResponseValue( 'ShipmentConfirmResponse', '/ShipmentIdentificationNumber' );
	    return $strTracking;
	}
	
	public function getResponseCode()
	{
		return $this->getResponseCodeInternal('ShipmentConfirmResponse');
	}
	
	public function getErrorText()
	{
		return $this->getErrorTextInternal('ShipmentConfirmResponse');
	}
	
	public function getOrderShipping()
	{
		return $this->getResponseValue('ShipmentAcceptResponse','/ShipmentResults/PackageResults/ServiceOptionsCharges/MonetaryValue');
	}
	
	public function getLabels()
	{
		return $this->xmlReader->xpath('/ShipmentAcceptResponse/ShipmentResults/PackageResults');
	}
	
	public function getTotalCharges()
	{
		return $this->getResponseValue('ShipmentAcceptResponse','/ShipmentResults/ShipmentCharges/TotalCharges/MonetaryValue');
	}
	
	public function getOptionsCharges()
	{
		return $this->getResponseValue('ShipmentAcceptResponse','/ShipmentResults/ShipmentCharges/ServiceOptionsCharges/MonetaryValue');
	}
	
	public function getLabelCode()
	{
		$items = $this->xmlReader->xpath('/ShipmentAcceptResponse/ShipmentResults/PackageResults/LabelImage/GraphicImage');
		foreach( $items as $key => $value ) return base64_decode($value);
		return '';
	}
}