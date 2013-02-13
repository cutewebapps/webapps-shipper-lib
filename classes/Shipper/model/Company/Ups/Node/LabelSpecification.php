<?php

/*
Example
<LabelSpecification>
    <LabelPrintMethod>
        <Code>GIF</Code>
        <Description>gif file</Description>
    </LabelPrintMethod>
    <HTTPUserAgent>Mozilla/4.5</HTTPUserAgent>
    <LabelImageFormat>
        <Code>GIF</Code>
        <Description>gif</Description>
    </LabelImageFormat>
</LabelSpecification>
*/

final class Shipper_Company_Ups_Node_LabelSpecification extends Shipper_Company_Ups_Node
{
    const LPM_GIF = 'GIF';
    const LPM_ZPL = 'ZPL';
    
    const LIF_GIF = 'GIF';
    const LIF_ZPL = '';
    
    private $lpmCode;
    private $userAgent;
    private $lifCode;
    private $labelWidth;
    private $labelHeight;
    
    public function __construct($in_lpmCode, $in_lifCode, $in_userAgent='')
    {
        $this->lpmCode = $in_lpmCode;
        $this->lifCode = $in_lifCode;
        if($in_userAgent == '')
        {
            $this->userAgent = 'Mozilla/4.5';
        }
        $this->labelWidth = -1;
        $this->labelHeight = -1;
    }
    
    public function setLabelHeight($in_height)
    {
        $this->labelHeight = $in_height;
    }
    
    public function setLabelWidth($in_width)
    {
        $this->labelWidth = $in_width;
    }
    
    public function toXml()
    {
        $retValue = "
<LabelSpecification>
    <LabelPrintMethod>
        <Code>".$this->lpmCode."</Code>
    </LabelPrintMethod>
    <HTTPUserAgent>".$this->userAgent."</HTTPUserAgent>"."\n";
        if($this->labelWidth != -1 && $this->labelHeight != -1 && $this->lpmCode == 'ZPL')
        {
            $retValue .= '
            <LabelStockSize>
                <Height>'.$this->labelHeight.'</Height>
                <Width>'.$this->labelWidth.'</Width>
            </LabelStockSize>'."\n";
        }
        else
        {
            $retValue .= '
            <LabelStockSize>
                <Height>4</Height>
                <Width>6</Width>
            </LabelStockSize>'."\n";
        }
        if($this->lpmCode == 'GIF')
        {
            $retValue .= '
            <LabelImageFormat>
                <Code>'.$this->lifCode.'</Code>
            </LabelImageFormat>'."\n";
        }
        return $retValue.'</LabelSpecification>'."\n";
    }
}