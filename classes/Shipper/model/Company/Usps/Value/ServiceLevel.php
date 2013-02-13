<?php
final class Shipper_Company_Usps_Value_ServiceLevel extends Shipper_Company_Usps_Value{
    const NEXTDAY2NDDAYPOTOADDRESSEE = 0;
    protected $values = array(0 => 'NextDay2ndDayPOToAddressee');
    public function __construct($in_value)
    {
        if($in_value == 0)
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