<?php
class Shipper_Company_Usps_LabelOption extends Shipper_Label_OptionList
{
    protected $bInsurance            = false;
    protected $bSignatureRequired    = false;
    protected $bDeliveryConfirmation = false;
    protected $bCertifiedEmail       = false;
    protected $bReturnReceipt        = false;
    
    protected $fltMinBalance         = 0.0;
    protected $fltRecreditAmt        = 0.0;
    
    public function setInsured( $bValue = 1 ) { $this->bInsurance = $bValue; }
    public function setCertifiedEmail( $bValue = 1 ) { $this->bCertifiedEmail = $bValue; }
    public function setDeliveryConfirmation( $bValue = 1 ) { $this->bDeliveryConfirmation = $bValue; }
    public function setSignatureRequired( $bValue = 1 ) { $this->bSignatureRequired = $bValue; }
    public function setReturnReceipt( $bValue = 1 ) { $this->bReturnReceipt = $bValue; }
    
    public function setAutoRecredit( $fltMinBalance, $fltRecreditAmt ) {
        $this->fltMinBalance  = $fltMinBalance;
        $this->fltRecreditAmt = $fltRecreditAmt;
    }
    
    public function isInsured() {              return $this->bInsurance; }
    public function isCertifiedEmail() {       return $this->bCertifiedEmail; }
    public function isDeliveryConfirmation() { return $this->bDeliveryConfirmation; }
    public function isSignatureRequired() {    return $this->bSignatureRequired; }
    public function isReturnReceipt() {        return $this->bReturnReceipt; }
    
    public function hasAutoRecredit() {
        return $this->fltRecreditAmt >= 10;
    }
    public function getAutoRecreditMinBalance() {
        return $this->fltMinBalance;
    }
    public function getAutoRecreditAmount() {
        return $this->fltRecreditAmt;
    }
    
}