<?php

final class Shipper_Company_Usps_Value_CustomsFormImageResolution extends Shipper_Company_Usps_Value
{
    const _150 = 0;
    const _300 = 1;
    protected $values = array(
        0 => '150',
        1 => '300',
    );
    public function __construct($in_value)
    {
        if($in_value >= 0 && $in_value <= 1) {
            $this->value = $in_value;
        } else {
            throw new Shipper_Exception( __CLASS__.': Invalid parameter passed.');
        }
    }
    public function getValue()
    {
        return $this->values[$this->value];
    }
}