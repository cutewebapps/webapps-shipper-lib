<?php
final class Shipper_Company_Usps_Value_ImageRotation extends Shipper_Company_Usps_Value
{
    const NONE = 0;
    const ROTATE90 = 1;
    const ROTATE180 = 2;
    const ROTATE270 = 3;
    protected $values = array(0 => 'None', 1 => 'Rotate90', 2 => 'Rotate180', 3 => 'Rotate270');
    public function __construct($in_value)
    {
        if($in_value >= 0 && $in_value <= 3)
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
}
