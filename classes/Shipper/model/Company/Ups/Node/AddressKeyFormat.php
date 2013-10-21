<?php
/*
<AddressKeyFormat>
    <AddressLine>AIRWAY ROAD SUITE 7</AddressLine>  
    <AddressLine>APT 6</AddressLine>  
    <PoliticalDivision2>SAN DIEGO</PoliticalDivision2>
    <PoliticalDivision1>CA</PoliticalDivision1>
    <PostcodePrimaryLow>92154</PostcodePrimaryLow>
    <CountryCode>US</CountryCode>
</AddressKeyFormat>
*/
final class Shipper_Company_Ups_Node_AddressKeyFormat extends Shipper_Company_Ups_Node
{

    private $_objAdress;
    
    public function __construct( Shipper_Address $objAddress )
    {
        $this->_objAddress = $objAddress;
    }
    public function setPoliticalDivision1($strPoliticalDivision1)
    {
        $this->_objAddress->setState( $strPoliticalDivision1 );
    }
    public function setPoliticalDivision2($strPoliticalDivision2)
    {
        $this->_objAddress->setCity( $strPoliticalDivision2 );
    }
    public function setAddressLine($strAddressLine)
    {
        $this->_objAddress->setAddressLine( $strAddressLine );
    }
    public function setPostCode($strPostcodePrimaryLow)
    {
        $this->_objAddress->setZip( $strPostcodePrimaryLow );
    }
    public function setCountryCode($strCountryCode)
    {
        $this->_objAddress->setCountry( $strCountryCode );
    }
    
    public function toXml()
    {
        $retValue = "\n".'<AddressKeyFormat>'."\n";
        
        //this is too smart, we should ensure we have fields correctly 
        //$arrLines = $this->_objAddress->getAddressLines();
        //foreach( $arrLines as $strLine ) if ( trim( $strLine ) != '' )
        //        $retValue .= '<AddressLine>'.$strLine.'</AddressLine>'."\n";
        // resolve html entities
        $retValue .= '<AddressLine>' . htmlentities($this->_objAddress->getAddressLine1()) . '</AddressLine>'."\n";
        if ($this->_objAddress->getAddressLine2()) 
            $retValue .= '<AddressLine>' . htmlentities($this->_objAddress->getAddressLine2()) . '</AddressLine>'."\n";
        
                
        $retValue .= '<CountryCode>' . htmlentities($this->_objAddress->getCountry()) . '</CountryCode>'."\n";
        $retValue .= '<PoliticalDivision1>' . htmlentities($this->_objAddress->getState()) . '</PoliticalDivision1>'."\n";
        $retValue .= '<PoliticalDivision2>' . htmlentities($this->_objAddress->getCity()) . '</PoliticalDivision2>'."\n";
        $retValue .= '<PostcodePrimaryLow>' . htmlentities($this->_objAddress->getZip5()) . '</PostcodePrimaryLow>'."\n";

        return $retValue."\n"
                .'</AddressKeyFormat>'."\n";
    }
}
