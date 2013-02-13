<?php

final class Shipper_Company_Usps_Value_NonDeliveryOption extends Shipper_Company_Usps_Value{
    const RETURN_ = 0;
    const ABANDON = 1;
    protected $values = array(
        0 => 'Return',
        1 => 'Abandon'
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
