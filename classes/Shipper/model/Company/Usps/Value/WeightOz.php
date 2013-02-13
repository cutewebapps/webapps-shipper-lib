<?php

final class Shipper_Company_Usps_Value_WeightOz extends Shipper_Company_Usps_Value{
    public function __construct($in_value)
    {
        $this->value = $in_value;
    }
    public function getValue()
    {
        return $this->value;
    }
}