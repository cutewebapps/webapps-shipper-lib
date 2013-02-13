<?php

final class Shipper_Company_Ups_Node_Service extends Shipper_Company_Ups_Node
{
    const NEXT_DAY_AIR_EARLY_AM = '14';
    const NEXT_DAY_AIR = '01';
    const NEXT_DAY_AIR_SAVER = '13';
    const SECOND_DAY_AIR_AM = '59';
    const SECOND_DAY_AIR = '02';
    const THIDR_DAY_SELECT = '12';
    const GROUND = '03';

    private $code;
    public function __construct($in_code)
    {
        $this->code = $in_code;
    }
    public function toXml()
    {
        return '<Service><Code>'.$this->code.'</Code></Service>';
    }
}