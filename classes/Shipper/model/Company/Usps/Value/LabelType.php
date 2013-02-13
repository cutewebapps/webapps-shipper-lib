<?php
final class Shipper_Company_Usps_Value_LabelType extends Shipper_Company_Usps_Value
{
    const DEFAULT_LABEL_TYPE = 0;
    const CERTIFIEDMAIL = 1;
    const DESTINATIONCONFIRM = 2;
    const INTERNATIONAL = 3;
    
    protected $values = array(
        0 => 'Default', 
        1 => 'CertifiedMail', 
        2 => 'DestinationConfirm', 
        3 => 'International');
    public function __construct ($in_value)
    {
        if ($in_value >= 0 && $in_value <= 3) {
            $this->value = $in_value;
        } else {
            throw new Shipper_Exception( __CLASS__.': Invalid parameter passed. '
               .' Must be ::DEFAULT_LABEL_TYPE or '
               .' ::CERTIFIEDMAIL or '
               .' ::DESTINATIONCONFIRM or '
               .' ::INTERNATIONAL.');
        }
    }
    public function getValue ()
    {
        return $this->values[$this->value];
    }
    public function __toString()
    {
        return $this->getValue();
    }
}