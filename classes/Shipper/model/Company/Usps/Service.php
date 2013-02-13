<?php

abstract class Shipper_Company_Usps_Service
{
    protected $wsdlPath;
    
    protected $strRequesterId;
    protected $strAccountId;
    protected $strPassPhrase;
        
    protected $soapClient;
    protected $requestObject;
    protected $responseObject;
    
    public function __construct( $strRequesterId, $strAccountId, $strPassPhrase )
    {
        if(!empty($strRequesterId)) {
            $this->strRequesterId = $strRequesterId;
        } else {
            throw new Shipper_Exception('Argument empty exception! $strRequesterId is empty.');
        }
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
    protected function initSoap() 
    {
        $this->initSoapAccountData();

        $this->soapClient = new Zend_Soap_Client( $this->wsdlPath, array(
              'soap_version'  => SOAP_1_2,
              'uri'           => 'http://schemas.xmlsoap.org/soap/envelope/',
              'encoding'      => 'utf-8',
        ) );
    }

    /**
     * @author Sergey Palutin
     * @return void
     */
    protected function initSoapAccountData() {
        $this->requestObject->RequesterID = $this->strRequesterId;
        $this->requestObject->AccountID   = $this->strAccountId;
        $this->requestObject->PassPhrase  = $this->strPassPhrase;
    }
    
    public function getLastRequest()
    {
        if($this->soapClient instanceof SoapClient)
        {
            return $this->soapClient->getLastRequest();
        }
        else
        {
            throw new Shipper_Exception('SoapClient object not initialized or has invalid type.');
        }
    }
    public function getLastResponse()
    {
        if($this->soapClient instanceof SoapClient)
        {
            return $this->soapClient->getLastResponse();
        }
        else
        {
            throw new Shipper_Exception('SoapClient object not initialized or has invalid type.');
        }
    }
    public function getRequestObject()
    {
        return $this->requestObject;
    }
    public function getResponseObject()
    {
        return $this->responseObject;
    }


    public function setRequest ($objRequest)
    {
        $this->requestObject = $objRequest;
    }

    public function setResponse( $arrResponse = array() )
    {
        $this->responseObject = $arrResponse;
    }

}
