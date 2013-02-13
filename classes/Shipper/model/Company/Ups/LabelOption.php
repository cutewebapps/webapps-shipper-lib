<?php


class Shipper_Company_Ups_LabelOption extends Shipper_Label_OptionList
{
    protected $fltInsurance = 0.0;
    protected $bSaturdayDelivery = false;
    // protected $bSaturdayPickup = false;
    protected $strCustomerComment = false;
    
    public function setInsurance( $fltInsurance ) { $this->fltInsurance = $fltInsurance; }
    public function getInsurance() { return $this->fltInsurance; }

    public function setSaturdayDelivery( $bValue = 1 ) { $this->bSaturdayDelivery = $bValue; }
    public function getSaturdayDelivery() { return $this->bSaturdayDelivery; }
    
    // public function setSaturdayPickup( $bValue = 1 ) { $this->bSaturdayPickup = $bValue; }
    // public function getSaturdayPickup() { return $this->bSaturdayPickup; }
    public function setCustomerComment( $strComment ) { $this->strCustomerComment = $strComment; }
    public function getCustomerComment() { return $this->strCustomerComment; }
        
    public function isValid(){
        if ( !parent::isValid() ) return false;
        return true;
    } 
    
    public function __toString() {
        $strOut  = parent::__toString();
        $strOut .= ' insurance: '.sprintf( '%.2f', $this->getInsurance() )
                  .' saturday: '.intval( $this->bSaturdayDelivery );
        return $strOut;
    }
}