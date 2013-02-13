<?php

final class Shipper_Company_Usps_Value_InsuredMail extends Shipper_Company_Usps_Value
{
    const OFF = 0;
    const ON = 1;
    const USPSONLINE = 2;
    const ENDICIA = 3;
    protected $values = array(
        0 => 'OFF',
        1 => 'ON',
        2 => 'UspsOnline',
        3 => 'Endicia'
    );
    public function __construct($in_value)
    {
        if($in_value >= 0 && $in_value <= 3) {
            $this->value = $in_value;
        } else {
            throw new Shipper_Exception( __CLASS__.': Invalid parameter passed.');
        }
    }
    public function getValue()
    {
        return $this->vaues[$this->value];
    }
}