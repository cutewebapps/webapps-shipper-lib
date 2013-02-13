<?php

final class Shipper_Company_Ups_Node_Address_Shipper extends Shipper_Company_Ups_Node_Address
{
    private $shipperName;
    private $shipperNumber;
    private $phoneNumber;
    private $taxNumber;
    private $attentionName;
    
    public function setAttentionName($in_attentionName)
    {
        $this->attentionName = $in_attentionName;
    }
    public function __construct( Shipper_Location $objLocation, Shipper_Account $objAccount )
    {
        parent::__construct( $objLocation->getAddress() );
        if (strlen( $objLocation->getCompanyName() ) > 35)
            $this->shipperName = substr($objLocation->getCompanyName(), 0, 35);
        else
            $this->shipperName = $objLocation->getCompanyName();
        $this->shipperNumber = $objAccount->getProperty( 'Ups', 'Account Number');

        $this->taxNumber     = $objLocation->getTaxPayerNumber();
        $this->phoneNumber   = $objLocation->getPhone();
        $this->attentionName = $objLocation->getAttentionName();
    }
    public function toXml()
    {
        $retValue = '<Shipper>'."\n";
        if($this->attentionName != '')
            $retValue .= '<AttentionName>'.$this->attentionName.'</AttentionName>'."\n";
        
        $retValue .= '<Name>'.$this->shipperName.'</Name>'."\n"
            .'<ShipperNumber>'.$this->shipperNumber.'</ShipperNumber>'."\n";
            
        if($this->phoneNumber != '')
            $retValue .= '<PhoneNumber>'.$this->phoneNumber.'</PhoneNumber>'."\n";
        if($this->taxNumber != '')
            $retValue .= '<TaxIdentificationNumber>'.$this->taxNumber.'</TaxIdentificationNumber>'."\n";
        $retValue .= parent::toXml();
        return $retValue.'</Shipper>'."\n";
    }
}