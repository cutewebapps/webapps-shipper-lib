<?php
final class Shipper_Company_Usps_Value_MailpieceDimensions extends Shipper_Company_Usps_Value
{
    protected $length;
    protected $width;
    protected $height;
    public function __construct ($in_length, $in_width, $in_height)
    {
        $this->length = $in_length;
        $this->width = $in_width;
        $this->height = $in_height;
    }
    public function getValue ()
    {
        return array('Length' => $this->length, 
        'Width' => $this->width, 
        'Height' => $this->height);
    }
}