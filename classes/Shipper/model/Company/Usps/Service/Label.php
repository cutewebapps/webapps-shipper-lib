<?php

class Shipper_Company_Usps_Service_Label extends Shipper_Company_Usps_Service{

    const WSDL_TEST = 'https://www.envmgr.com/labelservice/ewslabelservice.asmx?wsdl'; 
    const WSDL_LIVE = 'https://labelserver.endicia.com/LabelService/EwsLabelService.asmx?wsdl';
    
    public function __construct($strRequesterId, $strAccountId, $strPassPhrase, $bTestMode = true )
    {
        $this->wsdlPath = $bTestMode ? self::WSDL_TEST : self::WSDL_LIVE;
        parent::__construct( $strRequesterId, $strAccountId, $strPassPhrase );
    }
    public function getPostageLabel(
            Shipper_Company_Usps_Request_Label $in_labelRequest)
    {
        $this->requestObject = $in_labelRequest;
        $this->initSoap();        
        
        $this->responseObject = $this->soapClient->GetPostageLabel( $this->requestObject->toSoapArray() );


        $strFile = Zend_Registry::get( 'AppFolder' ).'/var/last_usps_label.txt';
        $file = new Common_File( $strFile );
        $file->save( 
            Develop_Debug::formattedXml( $this->soapClient->getLastRequest() )
          . "\n\n\n" 
          . Develop_Debug::formattedXml( $this->soapClient->getLastResponse() ) );

        
        return $this->responseObject->LabelRequestResponse->Status;
    }

  
    public function changePassPhrase(
        Shipper_Company_Usps_Request_ChangePass $in_request)
    {
        $this->requestObject = $in_request;
        $this->initSoap();        
        $this->responseObject = $this->soapClient->ChangePassPhrase($this->requestObject->toSoapArray());
        return $this->responseObject->Status;
    }
    
    
    public function getTrackingNumber()
    {
        return $this->responseObject->LabelRequestResponse->TrackingNumber;
    }
    public function getFinalPostage()
    {
        return $this->responseObject->LabelRequestResponse->FinalPostage;
    }
    public function getTransactionID()
    {
        return $this->responseObject->LabelRequestResponse->TransactionID;
    }
    public function getPostageBalance()
    {
        return $this->responseObject->LabelRequestResponse->PostageBalance;
    }
    public function getLabelCode()
    {
    	if(isset($this->responseObject->LabelRequestResponse->Base64LabelImage)) {
			return base64_decode($this->responseObject->LabelRequestResponse->Base64LabelImage);
    	} elseif(isset($this->responseObject->LabelRequestResponse->Label->Image->_)) {
			return base64_decode($this->responseObject->LabelRequestResponse->Label->Image->_);
    	}
    }
    public function getStatus()
    {
        return $this->responseObject->LabelRequestResponse->Status;
    }
    public function getErrorMessage()
    {
        return $this->responseObject->LabelRequestResponse->ErrorMessage;
    }
}