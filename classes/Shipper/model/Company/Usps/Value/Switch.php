<?php

final class Shipper_Company_Usps_Value_Switch extends Shipper_Company_Usps_Value{
    const YES = 1;
    const NO = 0;
    const ON = 2;
    const OFF = 3;
    protected $values = array(1=>'YES', 0=>'NO', 2=>'ON', 3=>'OFF');
    public function __construct ($in_value)
    {
        if ($in_value >= 0 && $in_value <= 3) {
            $this->value = $in_value;
        } else {
            throw new Shipper_Exception(
                __CLASS__ . ': Invalid parameter passed.');
        }
    }
    public function __toString()
    {
        return $this->getValue();
    }
    public function getValue()
    {
        return $this->values[$this->value];
    }
}
