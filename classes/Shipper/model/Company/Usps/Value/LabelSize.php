<?php
final class Shipper_Company_Usps_Value_LabelSize extends Shipper_Company_Usps_Value 
{
    const _4X6 = 0;
    const _4X5 = 1;
    const _4X4_5 = 2;
    const DOCTAB = 3;
    const _6x4 = 4;
    const _7x3 = 5;
    const DYMO30384 = 6;
    const ENVELOPESIZE10 = 7;
    const MAILER7X5 = 8;
    const _7X4 = 9;
    const _8X3 = 10;
    const BOOKLET = 11;
    protected $values = array(
        0 => '4X6',
        1 => '4X5',
        2 => '4X4.5',
        3 => 'DocTab',
        4 => '6X4',
        5 => '7X3',
        6 => 'Dymo30384',
        7 => 'EnvelopeSize10',
        8 => 'Mailer7x5',
        9 => '7X4',
        10 => '8X3',
        11 => 'Booklet'
    );
    public function __construct($in_value)
    {
        if($in_value >= 0 && $in_value <= 11)
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
