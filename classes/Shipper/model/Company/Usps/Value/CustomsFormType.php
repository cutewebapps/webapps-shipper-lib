<?php

final class Shipper_Company_Usps_Value_CustomsFormType extends Shipper_Company_Usps_Value
{
    const NONE = 0;
    const FORM2976 = 1;
    const FORM2976A = 2;
    protected $values = array(
        0 => 'None',
        1 => 'Form2976',
        2 => 'Form2976A',
    );
    public function __construct($in_value)
    {
        if ($in_value >= 0 && $in_value <= 2) {
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