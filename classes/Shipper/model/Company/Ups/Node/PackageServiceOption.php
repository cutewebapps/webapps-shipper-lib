<?php

final class Shipper_Company_Ups_Node_PackageServiceOption 
            extends Shipper_Company_Ups_Node
{
    private $DCISType;
    private $code;
    private $currencyCode;
    private $monetaryValue;
    
    public function __construct( $in_dcisType, 
                $in_code, 
                $in_currencyCode, 
                $in_monetaryValue)
    {
        $this->DCISType = $in_dcisType;
        $this->code = $in_code;
        $this->currencyCode = $in_currencyCode;
        $this->monetaryValue = $in_monetaryValue;
    }
    public function toXml()
    {
        $retValue = '<PackageServiceOptions>';
        $retValue .= '<InsuredValue>
        <Code>'.$this->code.'</Code>
        <CurrencyCode>'.$this->currencyCode.'</CurrencyCode>
        <MonetaryValue>'.$this->monetaryValue.'</MonetaryValue>
        </InsuredValue >';
        return $retValue.'</PackageServiceOptions>';
    }
}

