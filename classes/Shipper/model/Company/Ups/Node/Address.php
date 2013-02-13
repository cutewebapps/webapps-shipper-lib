<?php

/*
Example
<Address>
    <AddressLine1>Address Line</AddressLine1>
    <City>City</City>
    <StateProvinceCode>NJ</StateProvinceCode>
    <PostalCode>07430</PostalCode>
    <CountryCode>US</CountryCode>
</Address>
or
<Address>
    <AddressLine1>Address Line</AddressLine1>
    <City>Corado</City>
    <PostalCode>00646</PostalCode> 
    <CountryCode>PR</CountryCode>
</Address>
*/
class Shipper_Company_Ups_Node_Address extends Shipper_Company_Ups_Node
{
    private $addressLines;
    private $city;
    private $postalCode;
    private $countryCode;
    private $stateCode;
    
    public function __construct( Shipper_Address $objAddress )
    {
        if ( !( $objAddress instanceof Shipper_Address ) )
            throw new Shipper_Exception( 'UPS Address Node require Shipper_Address object' );
            
        $this->addressLines = array(
            1 => $objAddress->getAddressLine1(),
            2 => $objAddress->getAddressLine2(),
            3 => $objAddress->getAddressLine3(),
        );
        $this->city        = $objAddress->getCity();
        $this->stateCode   = $objAddress->getState();
        $this->postalCode  = $objAddress->getZip5();
        $this->countryCode = $objAddress->getCountry();
    }
    public function setStateCode( $strStateCode = '' ) {
        $this->stateCode = $strStateCode;
    }
    public function toXml()
    {
        $retValue = '<Address>'."\n";
        if( is_array( $this->addressLines )) {
            if(isset($this->addressLines[1]) && $this->addressLines[1] != '')
                $retValue .= '<AddressLine1>'.$this->addressLines[1].'</AddressLine1>'."\n";

            if(isset($this->addressLines[2]) && $this->addressLines[2] != '')
                $retValue .= '<AddressLine2>'.$this->addressLines[2].'</AddressLine2>'."\n";
        } else {
            $retValue .= '<AddressLine1>'.$this->addressLines.'</AddressLine1>'."\n";
        }
                
        $retValue .= '<City>'.$this->city.'</City>'."\n".
            '<PostalCode>'.$this->postalCode.'</PostalCode>'."\n".
            '<CountryCode>'.strtoupper($this->countryCode).'</CountryCode>'."\n";
        if($this->stateCode != ''  )
            $retValue .= '<StateProvinceCode>'.strtoupper($this->stateCode).'</StateProvinceCode>'."\n";
            
        return $retValue.'</Address>';
    }
}
