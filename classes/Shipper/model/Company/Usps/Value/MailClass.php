<?php
final class Shipper_Company_Usps_Value_MailClass extends Shipper_Company_Usps_Value
{
    const EXPRESS = 0;
    const FIRST = 1;
    const LIBRARYMAIL = 2;
    const MEDIAMAIL = 3;
    const PARCELPOST = 4;
    const PARCELSELECT = 5;
    const PRIORITY = 6;
    const STANDARDMAIL = 7;
    const EXPRESSMAILINTERNATIONAL = 8;
    const FIRSTCLASSMAILINTERNATIONAL = 9;
    const PRIORITYMAILINTERNATIONAL = 10;
    
    protected $values = array(
        0 => 'Express',
        1 => 'First',
        2 => 'LibraryMail',
        3 => 'MediaMail',
        4 => 'ParcelPost',
        5 => 'ParcelSelect',
        6 => 'Priority',
        7 => 'StandardMail',
        8 => 'ExpressMailInternational',
        9 => 'FirstClassMailInternational',
        10 => 'PriorityMailInternational',
    );
    
    public function __construct($in_value)
    {
        if($in_value >= 0 && $in_value <= 10)
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

    public function getAllValues() 
    {
        return $this->values;
    }
}
