<?php

class Shipper_Company_Ups_LabelSpecification
{
    private $imageFormat;
    private $httpUserAgent;
    
    private function _getMethodDescription()
    {
        return 'gif file';
    }
    private function _getFormatDescription()
    {
        return 'gif';
    }
    public function __construct($in_imageFormat, $in_httpUserAgent='')
    {
        $this->imageFormat = $in_imageFormat;
        $this->httpUserAgent = $in_httpUserAgent;
    }
    public function toXml()
    {
        $retValue = '';
        $retValue = '<LabelSpecification>'.
        '<LabelPrintMethod>'.
        '<Code>'.$this->imageFormat.'</Code>'.
        '<Description>'.$this->_getMethodDescription().'</Description>'.
        '</LabelPrintMethod>'.
        '<HTTPUserAgent>'.$this->httpUserAgent.'</HTTPUserAgent>'.
        '<LabelImageFormat>'.
        '<LabelImageFormat>'.
        '<Code>'.$this->imageFormat.'</Code>'.
        '<Description>'.$this->_getFormatDescription().'</Description>'.
        '</LabelImageFormat>'.
        '</LabelImageFormat>'.
        '<LabelSpecification>';
        return $retValue;
    }
}