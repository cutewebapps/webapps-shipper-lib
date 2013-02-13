<?php

class Shipper_Company_Usps_Service_Recredit extends Shipper_Company_Usps_Service
{ 
    const WSDL = 'https://LabelServer.Endicia.com/LabelService/EwsLabelService.asmx/BuyPostageXML'; 
    protected $strLogFilePath = ''; 

    public function __construct($strRequesterId, $strAccountId, $strPassPhrase)
    {
        $this->wsdlPath = self::WSDL;
        parent::__construct( $strRequesterId, $strAccountId, $strPassPhrase );
    }
    
    public function getXmlInput( $nAmount )
    {
        return '
            <RecreditRequest>
            <RequesterID>'.$this->strRequesterId.'</RequesterID>
            <RequestID>RC'.date('jnYHis') . rand(100,999) .'</RequestID>
            <CertifiedIntermediary>
            <AccountID>'.$this->strAccountId.'</AccountID>
            <PassPhrase>'.$this->strPassPhrase.'</PassPhrase>
            </CertifiedIntermediary>
            <RecreditAmount>'.$this->nAmount.'</RecreditAmount>
            </RecreditRequest>
        ';
    }
    public function getError()
    {
        return $this->error;
    }
    public function  getRecredit( $nAmount ) 
    {
         $curl_handle = curl_init (); 
         $this->nAmount = $nAmount;
         $request = $this->getXmlInput( $nAmount );
     
         $this->error = '';
         
         $params = array('http' => array(
            'method' => 'POST',
            'content' => 'recreditRequestXML='.$request,
            'header'  => 'Content-Type: application/x-www-form-urlencoded'));
         $ctx = stream_context_create($params);
         $fp = fopen($this->wsdlPath, 'rb', false, $ctx);
         if (!$fp) {
             $this->Status = 'Problem with server';
             return $this->Status;
         }
         $response = stream_get_contents($fp);

         if ($response === false) {
            $this->Status = "Problem reading data from ".$this->wsdlPath;
         } else {
             $this->strLogFilePath = Zend_Registry::get('AppFolder').'/var/usps/recredit/'.date('YmdHis').'.xml';
            $fnLog = new Common_File( $this->strLogFilePath );
            $fnLog->save( $request."\n---\n".$response);

                
            if ( preg_match( '/<ErrorMessage>(.+)<\/ErrorMessage>/simU', $response, $err_msg )) {
                $this->error = $err_msg[1];
            }
            if ( preg_match( '/<CertifiedIntermediary>(.+)<\/CertifiedIntermediary>/simU', $response, $si )) {
                $acc_info = $si[1];
                $ret_postageBalance = '';
                if ( preg_match('/<PostageBalance>(.+)<\/PostageBalance>/simu', $acc_info, $pb)){
                    $ret_postageBalance = "Postage Balance: ".$pb[1];
                }
                $ret_ascendingBalance = '';
                if ( preg_match('/<AscendingBalance>(.+)<\/AscendingBalance>/simu', $acc_info, $ab)){
                    $ret_ascendingBalance = 'Ascending Balance: '.$ab[1];
                }
                $this->Status = $ret_postageBalance.' '.$ret_ascendingBalance;
            }
        }
        return $this->Status;
        
    }

    public function getLogFilePath() {
        return $this->strLogFilePath;
    }

    public function getLogFileContent() {
        $strFilePath = $this->getLogFilePath();
        if (!file_exists($strFilePath)) {
            return null;
        }
        $hFile = fopen($strFilePath, "r");
        $strContent = fread($hFile, filesize($strFilePath));
        fclose($hFile);
        return $strContent;
    }
}