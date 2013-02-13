<?php
/**
 * @class: Shipper_Company_Usps_Service_Rate
 * @author: sergey.palutin
 * @since: 21.10.2010 15:04:20 EET
 */
class Shipper_Company_Usps_Service_Rate extends Shipper_Company_Usps_Service
{
    const WSDL_TEST = 'https://www.envmgr.com/labelservice/ewslabelservice.asmx?wsdl';
    const WSDL_LIVE = 'https://labelserver.endicia.com/LabelService/EwsLabelService.asmx?wsdl';
    const RESPONSE_ROOT_ELEMENT = 'PostageRateResponse';

    public function __construct($strRequesterId, $strAccountId, $strPassPhrase, $bTestMode = true )
    {
        $this->wsdlPath = $bTestMode ? self::WSDL_TEST : self::WSDL_LIVE;
        if(!empty($strRequesterId)) {
            $this->strRequesterId = $strRequesterId;
        } else {
            throw new Shipper_Exception('Argument empty exception! $strRequesterId is empty.');
        }
        //$this->CertifiedIntermediary
        if(!empty($strAccountId)) {
            $this->strAccountId = $strAccountId;
        } else {
            throw new Shipper_Exception('Argument empty exception! $strAccountId is empty.');
        }
        if(!empty($strPassPhrase)) {
            $this->strPassPhrase = $strPassPhrase;
        } else {
            throw new Shipper_Exception('Argument empty exception! $strPassPhrase is empty.');
        }

        $this->requestObject = null;
        $this->responseObject = null;
    }


    public function sendRateRequest(
            Shipper_Company_Usps_Request_Rate $objRequestRate)
    {
        $this->requestObject = $objRequestRate;
        $this->initSoap();

        $this->responseObject = $this->soapClient->CalculatePostageRate( $this->requestObject->toSoapArray() );
        return Develop_Debug::formattedXml( $this->soapClient->getLastResponse() );
    }

    protected function initSoapAccountData() {
        $this->requestObject->RequesterID = $this->strRequesterId;
        $this->requestObject->CertifiedIntermediary = array(
            'AccountID'  => $this->strAccountId,
            'PassPhrase' => $this->strPassPhrase,
        );
    }

    public function getStatus()
    {
        $strRootElement = self::RESPONSE_ROOT_ELEMENT;
        return $this->responseObject->{$strRootElement}->Status;
    }

    public function getShipmentRateValue() {
        $strRootElement = self::RESPONSE_ROOT_ELEMENT;
        #Develop_Debug::dumpDie($this->responseObject->{$strRootElement}->Postage->Rate);
        return $this->responseObject->{$strRootElement}->Postage->Rate;
    }
}
