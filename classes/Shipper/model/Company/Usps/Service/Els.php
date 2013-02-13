<?php

class Shipper_Company_Usps_Service_Els  extends Shipper_Company_Usps_Service{

    const WSDL = 'http://www.endicia.com/ELS/ELSServices.cfc?wsdl';
    
    public function __construct($strRequesterId, $strAccountId, $strPassPhrase)
    {
        parent::__construct( $strRequesterId, $strAccountId, $strPassPhrase );
        $this->wsdlPath = self::WSDL;
    }
    
    /**
     * @return bool status
     */
    public function getStatus ( )
    {
        $this->error = '';
        if ( preg_match( '/<ErrorMsg>(.+)<\/ErrorMsg>/simU', $this->responseContent, $arrMatch ) )
        {
            $this->error = $arrMatch[1];
        }
        $this->Status = 'NO';

        if ( preg_match( '/<RefundList>(.+)<\/RefundList>/simU', $this->responseContent, $rl )) {
            $refund_list = $rl[1];
            if ( preg_match('/<IsApproved>(.+)<\/IsApproved>/simu', $refund_list, $is_approved)){
                $this->Status = $is_approved[1];
            }
        }
        return  intval($this->Status == 'YES');
    }
    /**
     * @return string of Error Message if status is false
     */
    public function getError() 
    {
        return $this->error;   
    }
    
    /**
     * @retrun bool whether it was a successfull refund
     * @param Shipper_Company_Usps_Request_Void $in_request
     */
    public function getRefund( Shipper_Company_Usps_Request_Void $in_request ) {
         $this->requestObject = $in_request;
  /*       
         $this->initSoap();    
        
         try{ 
             echo  $this->soapClient->getWsdl().'<hr />';
             Develop_Debug::dump( Develop_Debug::formattedXml(
                  $this->requestObject->getXmlInput()  ) );
             $this->responseObject = $this->soapClient->RefundRequest(
                    $this->requestObject->toSoapArray() );
               
         } catch ( Exception $e )  {
         
         }
         Develop_Debug::dump( $this->soapClient->getLastRequestHeaders());
         Develop_Debug::dump(  Develop_Debug::formattedXml( 
                $this->soapClient->getLastRequest() ) );
             
         Develop_Debug::dumpDie( $this->soapClient->getLastResponse() );
         return $this->responseObject->Status;
   */
         
         $curl_handle = curl_init (); 
         curl_setopt ($curl_handle, CURLOPT_URL, 
                'http://www.endicia.com/ELS/ELSServices.cfc?wsdl'); 
         $postfields = array(  
                'method'   => 'RefundRequest', 
                'XMLInput' => $this->requestObject->getXmlInput() 
         ); 
 
        curl_setopt ($curl_handle, CURLOPT_FOLLOWLOCATION, 1); 
        curl_setopt ($curl_handle, CURLOPT_RETURNTRANSFER, 1); 
        curl_setopt ($curl_handle, CURLOPT_SSL_VERIFYPEER, 0); 
        curl_setopt ($curl_handle, CURLOPT_POST, 1); 
        curl_setopt ($curl_handle, CURLOPT_POSTFIELDS, $postfields); 
         
        $curl_result = curl_exec ($curl_handle);
        if ( !$curl_result ) throw new Exception ('There has been a Curl error'); 
        $this->responseContent = $curl_result;
        $bStatusYes = $this->getStatus();
       // echo 'STATUS YES: '.$bStatusYes;
        
        return $bStatusYes;
    }
    
}