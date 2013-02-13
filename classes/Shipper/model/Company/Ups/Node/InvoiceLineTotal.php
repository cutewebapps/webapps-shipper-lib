<?php
final class Shipper_Company_Ups_Node_InvoiceLineTotal extends Shipper_Company_Ups_Node
{
    private $currencyCode;
    private $monetaryValue;
    public function __construct($currencyCode = 'USD', $monetaryValue = 100 )
    {
        if(!empty($currencyCode) && !empty($monetaryValue))
        {
            $this->currencyCode = $currencyCode;
            $this->monetaryValue = ceil( $monetaryValue );
        }
    }
    public function toXml()
    {
        $retValue = '<InvoiceLineTotal>'."\n";
        $retValue .= "<CurrencyCode>{$this->currencyCode}</CurrencyCode>
        <MonetaryValue>{$this->monetaryValue}</MonetaryValue>"."\n";
        return $retValue.'</InvoiceLineTotal>'."\n";
    }
}