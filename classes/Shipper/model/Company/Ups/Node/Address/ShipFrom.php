<?php

final class Shipper_Company_Ups_Node_Address_ShipFrom extends Shipper_Company_Ups_Node_Address
{
    private $companyName;
    private $phoneNumber;
    private $attentionName;
    private $faxNumber = '';
    
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

            $this->faxNumber = $location->getFax();
            
        } else if ( $location instanceof Shipper_Address ) {
            
            $objAddress  = $location;
        
        } else throw new Shipper_Exception( 'Invalid parameters for ShipFrom Constructor' );
        
        parent::__construct( $objAddress );

        $this->companyName = $in_companyName;
        $this->phoneNumber = $in_phoneNumer;
        if ( !$this->phoneNumber ) $this->phoneNumber = '99999999999';
        $this->attentionName = $in_attentionName;

    }
    
    public function toXml()
    {
        $retValue = '<ShipFrom>'."\n";
        $retValue .= '<CompanyName>'.$this->companyName.'</CompanyName>'."\n"
                  .'<PhoneNumber>'.$this->phoneNumber.'</PhoneNumber>'."\n";
        if($this->attentionName != '')
            $retValue .= '<AttentionName>'.$this->attentionName.'</AttentionName>'."\n";
       if($this->faxNumber != '')
            $retValue .= '<FaxNumber>'.$this->attentionName.'</FaxNumber>'."\n";
            
        $retValue .= parent::toXml();
        return $retValue.'</ShipFrom>'."\n";
    }
}