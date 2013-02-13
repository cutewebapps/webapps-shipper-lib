<?php

final class Shipper_Company_Usps_Value_ImageFormat extends Shipper_Company_Usps_Value
{
    const EPL2 = 0;
    const GIF = 1;
    const JPEG = 2;
    const PDF = 3;
    const PNG = 4;
    const ZPLII = 5;
    protected $values = array(
        0 => 'EPL2',
        1 => 'GIF',
        2 => 'JPEG',
        3 => 'PDF',
        4 => 'PNG',
        5 => 'ZPLII'
    );
    public function __construct($in_value)
    {
        if($in_value >= 0 && $in_value <= 5)
        {
            $this->value = $in_value;
        }
        else
        {
            throw new Shipper_Exception( __CLASS__.': Invalid parameter passed.');
                    }
    }
    public function getValue()
    {
        return $this->values[$this->value];
    }
    public function __toString()
    {
        return $this->getValue();
    }
}
