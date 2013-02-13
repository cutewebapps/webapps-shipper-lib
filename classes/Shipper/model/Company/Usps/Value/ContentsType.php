<?php

final class Shipper_Company_Usps_Value_ContentsType extends Shipper_Company_Usps_Value
{
    const DOCUMENTS = 0;
    const GIFT = 1;
    const MERCHANDISE = 2;
    const OTHER = 3;
    const RETURNEDGOODS = 4;
    const SAMPLE = 5;
    
    protected $values = array(
        0 => 'Documents',
        1 => 'Gift',
        2 => 'Merchandise',
        3 => 'Other',
        4 => 'ReturnedGoods',
        5 => 'Sample',
    );
    public function __construct($in_value)
    {
        if($in_value >= 0 && $in_value <= 5) {
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