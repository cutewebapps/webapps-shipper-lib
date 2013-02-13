<?php

class Shipper_Company_Usps_Request_Label extends Shipper_Company_Usps_Request
{
/**
* put your comment there...
* 
* @param mixed $in_testRequest
* @param mixed $in_labelType
* @param mixed $in_imageFormat
* @return Shipper_Usps_Request_LabelRequest
*/
    public function __construct(
            $in_testRequest='YES', 
            $in_labelType='Default', 
            $in_imageFormat='ZPLII' )
    {
        $this->Test = $in_testRequest;
        $this->LabelType = new Shipper_Company_Usps_Value_LabelType( $in_labelType );
        $this->ImageFormat = $in_imageFormat;
        
        $this->LabelSize = new Shipper_Company_Usps_Value_LabelSize( 
                Shipper_Company_Usps_Value_LabelSize::DOCTAB );
    }
    public function setOriginAddress( Shipper_Address $objAddressFrom ) 
    {
        $this->OriginCountry = 
            (string)(new Shipper_Company_Usps_CountryName( 
            $objAddressFrom->getCountry() ));
        $this->ReturnAddress1 = $objAddressFrom->getAddressLine();
        $this->FromCity = $objAddressFrom->getCity();
        $this->FromState = $objAddressFrom->getState();
        $this->FromPostalCode = $objAddressFrom->getZip5();
    }
    public function setOrigin( Shipper_Location $objShipperLocation )
    {
        $this->setOriginAddress( $objShipperLocation->getAddress());
        $this->FromCompany = $objShipperLocation->getCompanyName();
        $this->FromPhone = $objShipperLocation->getPhone();
    }
    public function setDestinationAddress( Shipper_Address $objAddress )
    {
        $this->ToCountry = 
            (string)(new Shipper_Company_Usps_CountryName( 
            $objAddress->getCountry() ));
            $this->ToAddress1 = $objAddress->getAddressLine1();
        $this->ToAddress2 = $objAddress->getAddressLine2();
        $this->ToCity = $objAddress->getCity();
        $this->ToState = $objAddress->getState();
        $this->ToPostalCode = $objAddress->getZip5();
    } 
    
    public function setDestination( Shipper_Address $objAddress, 
            Shipper_Company_Usps_LabelOption $objLabelOptions )
    {
        $this->setDestinationAddress( $objAddress );
        $this->ToName = $objLabelOptions->getReceiverName();
        $this->ToPhone = $objLabelOptions->getReceiverPhone();
    }
   
    public function setZeroCustoms()
    {
        $this->CustomsQuantity1 = '0';
        $this->CustomsValue1 = '0';
        $this->CustomsWeight1 = '0';
        $this->CustomsQuantity2 = '0';
        $this->CustomsValue2 = '0';
        $this->CustomsWeight2 = '0';
        $this->CustomsQuantity3 = '0';
        $this->CustomsValue3 = '0';
        $this->CustomsWeight3 = '0';
        $this->CustomsQuantity4 = '0';
        $this->CustomsValue4 = '0';
        $this->CustomsWeight4 = '0';
        $this->CustomsQuantity5 = '0';
        $this->CustomsValue5 = '0';
        $this->CustomsWeight5 = '0';
    }

    public function toXML()
    {
        $retValue = '<Label Test="'.$this->Test.'" LabelType="'.$this->LabelType.'" LabelSize="'.$this->LabelSize.'" ImageFormat="'.$this->ImageFormat.'">';
        $retValue .= parent::toXML();
        return $retValue
            .'</Label>';
    }

    public function toSoapArray()
    {
        return $this->makeSoapArray('LabelRequest');
    }
}