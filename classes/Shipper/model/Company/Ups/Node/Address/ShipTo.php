<?php

/*
Example
<ShipTo>
    <CompanyName>Company Name</CompanyName>
    <PhoneNumber>1234567890</PhoneNumber>
    <Address>
        <AddressLine1>Address Line</AddressLine1>
        <City>Corado</City>
        <PostalCode>00646</PostalCode> 
        <CountryCode>PR</CountryCode>
    </Address>
</ShipTo>
*/
final class Shipper_Company_Ups_Node_Address_ShipTo extends Shipper_Company_Ups_Node_Address
{
    private $companyName;
    private $phoneNumber;
    private $attentionName;
    
    /**
     * 
     * @param Shipper_Address or Shipper_Location $location
     * @param string $in_companyName
     * @param string $in_phoneNumer
     * @param string $in_attentionName
     */
    public function __construct( $location, 
            $in_companyName = '', $in_phoneNumer = '', $in_attentionName='' )
    {
        if ( $location instanceof Shipper_Location ) {
            
            $objAddress = $location->getAddress();
            $in_companyName = $location->getCompanyName();
            $in_phoneNumer = $location->getPhone(); 
            $in_attentionName = $location->getAttentionName();
            
        } else if ( $location instanceof Shipper_Address ) {
            
            $objAddress  = $location;
        
        } else throw new Shipper_Exception( 'Invalid parameters for ShipTo Constructor' );
        
        parent::__construct( $objAddress );

        $this->companyName = $in_companyName;
        $this->phoneNumber = $in_phoneNumer;
        if ( !$this->phoneNumber ) $this->phoneNumber = '99999999999';
        $this->attentionName = $in_attentionName;

    }
    
    public function toXml()
    {
        $retValue = '<ShipTo>'."\n";
        $retValue .= '<CompanyName>'.$this->companyName.'</CompanyName>'."\n"
                  .'<PhoneNumber>'.$this->phoneNumber.'</PhoneNumber>'."\n";
        if($this->attentionName != '')
            $retValue .= '<AttentionName>'.$this->attentionName.'</AttentionName>'."\n";

        $retValue .= parent::toXml();
        return $retValue.'</ShipTo>'."\n";
    }
}
