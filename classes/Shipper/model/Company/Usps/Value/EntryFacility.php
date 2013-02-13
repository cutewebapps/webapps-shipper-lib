<?php

final class Shipper_Company_Usps_Value_EntryFacility extends Shipper_Company_Usps_Value
{
    const DBMC = 0;
    const DDU = 1;
    const DSCF = 2;
    const OBMC = 3;
    const OTHER = 4;
    protected $values = array(
        0 => 'DBMC',
        1 => 'DDU',
        2 => 'DSCF',
        3 => 'OBMC',
        4 => 'Other',
    );
    public function __construct($in_value)
    {
        if($in_value >= 0 && $in_value <= 4) {
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
