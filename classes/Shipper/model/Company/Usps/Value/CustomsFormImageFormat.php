<?php

final class Shipper_Company_Usps_Value_CustomsFormImageFormat extends Shipper_Company_Usps_Value
{
    const GIF = 0;
    const JPEG = 1;
    const PDF = 2;
    const PNG = 3;
    protected $values = array(
        0 => 'GIF',
        1 => 'JPEG',
        2 => 'PDF',
        3 => 'PNG',
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
        return $this->values[$this->value];
    }
}