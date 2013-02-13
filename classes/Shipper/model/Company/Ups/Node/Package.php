<?php
/*
Example
<Package>
      <PackagingType>
        <Code>02</Code>
      </PackagingType>
      <PackageWeight>
          <UnitOfMeasurement>
            <Code>LBS</Code>
          </UnitOfMeasurement>
        <Weight>10</Weight>
      </PackageWeight>   
</Package>
*/

final class Shipper_Company_Ups_Node_Package extends Shipper_Company_Ups_Node
{
    private $ptCode;
    private $uomCode;
    private $weight;
    private $packageServiceOptions;
    private $referenceBarCodeIndicator;
    private $referenceNumberCode;
    private $referenceNumberValue;
    private $description;
    
    public function __construct( $in_ptCode, $in_uomCode, $in_weight, 
            Shipper_Company_Ups_Node_PackageServiceOption 
                    $in_packageServiceOptions = null)
    {
        $this->ptCode = $in_ptCode;
        $this->uomCode = $in_uomCode;
        $this->weight = $in_weight;
        $this->packageServiceOptions = $in_packageServiceOptions;
        $this->referenceBarCodeIndicator = '';
        $this->referenceNumberCode = '';
        $this->referenceNumberValue = '';
        $this->description = '';
    }
    public function setPackageDescription($in_description)
    {
        $this->description = $in_description;
    }
    public function setReferenceNumber($in_barCodeIndicator, $in_code, $in_value)
    {
        if(strlen($in_code) > 2)  {
            $in_code = substr($in_code, 0, 2);
        }
        $this->referenceBarCodeIndicator = $in_barCodeIndicator;
        $this->referenceNumberCode = $in_code;
        $this->referenceNumberValue = $in_value;
    }
    public function toXml()
    {
        $retValue = "<Package>\n";
        if($this->referenceNumberCode != '' && $this->referenceNumberValue != '')
        {
            $retValue .= "<ReferenceNumber>\n";
            if($this->referenceBarCodeIndicator)  {
                $retValue .= "<BarCodeIndicator></BarCodeIndicator>\n";
            }
            $retValue .= "<Code>".$this->referenceNumberCode."</Code>
            <Value>".$this->referenceNumberValue."</Value>\n";
            $retValue .= "</ReferenceNumber>\n";
        }
        $retValue .= '<PackagingType>
            <Code>'.$this->ptCode.'</Code>
        </PackagingType>
        <PackageWeight>
            <UnitOfMeasurement>
                <Code>'.$this->uomCode.'</Code>
            </UnitOfMeasurement>
            <Weight>'.$this->weight.'</Weight>
        </PackageWeight>'."\n";
        if(!is_null($this->packageServiceOptions)) {
            $retValue .= $this->packageServiceOptions->toXml();
        }
        if(!empty($this->description))  {
            $retValue .= "<Description>{$this->description}</Description>"."\n";
        }
        return $retValue.'</Package>'."\n";
    }
}