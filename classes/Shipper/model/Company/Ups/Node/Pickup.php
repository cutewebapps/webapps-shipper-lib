<?php
/*
Example
<PickupType>
    <Code>07</Code>
</PickupType>
*/
final class Shipper_Company_Ups_Node_Pickup extends Shipper_Company_Ups_Node
{
    const DAILY = '01';
    const CUSTOMER_COUNTER = '03';
    const ONE_TIME_PICKUP = '06';
    const ON_CALL_AIR = '07';
    const SUGGESTED_RETAIL = '11';
    const LETTER_CENTER = '19';
    const SIR_SERVICE = '20';
    
    private $code;
    public function __construct($in_code)
    {
        $this->code = $in_code;
    }
    public function toXml()
    {
        $retValue = '';
        $retValue = '<PickupType><Code>'.$this->code.'</Code></PickupType>'."\n";
        return $retValue;
    }
}
