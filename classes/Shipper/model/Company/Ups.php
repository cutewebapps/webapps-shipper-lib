<?php
/**
 * UPS company class 
 * 
 * Online calculator:
 * https://wwwapps.ups.com/ctc/request?loc=en_US
 * 
 * Fuel surcharge: 
 * http://www.ups.com/content/us/en/shipping/cost/zones/fuel_surcharge.html
 *
 * Zone Charts downloading:
 * http://www.ups.com/content/us/en/shipping/cost/zones/continental_us.html
 * 
 * Daily Rates (2010):
 * http://www.ups.com/content/us/en/shipping/cost/zones/daily_rates.html
 * http://www.ups.com/media/en/daily_rates.xls
 */
class Shipper_Company_Ups implements Shipper_Company
{
    public function getName ()
    {
        return 'UPS';
    }
    public function getServices ()
    {
        return array(
            'Shipping Label',
            'Void Label', 
            'Return Label', 
            'Address Validation',
            'Shipping Rate',
        );
    }
    
    public function getShippingMethodCodes ()
    {
        return array(
        '03' => 'UPS Ground', 
        '02' => 'UPS 2nd Day Air', 
        '01' => 'UPS Next Day Air', 
        '12' => 'UPS 3 Day Select', 
        '07' => 'UPS Express', 
        '08' => 'UPS Expedited', 
        '11' => 'UPS Standard', 
        '13' => 'UPS Next Day', 
        '14' => 'UPS Next Day Air Early AM',  
        '54' => 'UPS Express Plus',  
        '59' => 'UPS 2nd Day Air AM',  
        '65' => 'UPS Saver', 
        '82' => 'UPS Today Standard',
        '83' => 'UPS Today Dedicated Courie',
        '84' => 'UPS Today Intercity',
        '85' => 'UPS Today Express',
        '86' => 'UPS Today Express Saver',
        
        // only two are international
        '65' => 'UPS Worldwide Saver',
        '07' => 'UPS Worldwide Express',
        );
    }
    
    public function getMethodByName( $strMethod )
    {
        $strMethod = strtolower( $strMethod );
        foreach ( $this->getShippingMethodCodes() as $strCode => $strTitle ) {
            
           if ( strstr( $strMethod, strtolower( $strTitle )) 
             || strstr( strtolower( $strTitle ), $strMethod ) ) {
                return $strCode;
           }
        }
        return '';
    }

    public function getAccountFields()  
    {
        return array(
          'shacc_requesterid'  => 'User ID',
          'shacc_pass'         => 'Password',
          'shacc_licence'      => 'License',
          'shacc_accountid'    => 'Account Number',
        );      
    }
    public function getTrackingLink( $strTracking ) 
    {
        // this is a public form
        // return 'http://www.ups.com/WebTracking/track?loc=en_US';
        return 'http://wwwapps.ups.com/WebTracking/processInputRequest?TypeOfInquiryNumber=T&'
            .'InquiryNumber1='. $strTracking ;
    }
    
    public function getPackageEstimateCost( $objShipperCalc )
    {
        return 6.95;
    }
    
    /**
     * @return Shipper_Company_Ups_Service
     * @param $objShipperAccount
     */
    protected function initService( $objShipperAccount, $className )
    {
       if ( !( $objShipperAccount instanceof Shipper_Account ) )
            throw new Shipper_Exception('Shipper account is not provided');
       if ( !$objShipperAccount->isValid() )
            throw new Shipper_Exception('Shipper account is not valid' );
            
       $objService = new $className (
              $objShipperAccount->getProperty( 'Ups', 'User ID'), 
              $objShipperAccount->getProperty( 'Ups', 'License'), 
              $objShipperAccount->getProperty( 'Ups', 'Password'),
              $objShipperAccount->isTestMode() || Develop_Mode::isLocal() || Develop_Mode::isDev()
       );
       return $objService;
    }
    
    /**
     * send request to get shipping label
     * @return Shipper_Label object of label, ready to save
     */
    public function requestShippingLabel( 
            Shipper_Account $objShipperAccount,
            Shipper_Location $objShipperLocation, 
            Shipper_Address $objShipperAddressTo, 
            $objLabelOptions,
            $strOutputFormat = 'ZPL' ) 
    {
        /**
         * @throws Shipper_Label_Exception
            *
         */
        /**
         * @var Shipper_Company_Ups_Service_Ship $objShipService
         */
       $objShipService = $this->initService( $objShipperAccount,
                'Shipper_Company_Ups_Service_Ship' );
       
       // Validating shipping method code
       $arrCodes = $this->getShippingMethodCodes(); 
       
       if ( !isset( $arrCodes[ $objLabelOptions->getCode() ] ) )
            throw new Shipper_Label_Exception( 
                    'Shipping Method Code '.$objLabelOptions->getCode().' is invalid' );
       if ( $strOutputFormat != 'ZPL' ) 
            throw new Shipper_Label_Exception(
                    'Shipping Label Format Error: Only ZPL format is supported' ); 
       if ( !$objLabelOptions->isValid() ) 
            throw new Shipper_Label_Exception( 
                    'Shipping Label Option Error: '.$objLabelOptions->getError() );
       
       $arrPackages = array();
       $fltInsuredValue = $objLabelOptions->getInsurance();

       // declaring package node
       $nodePackage = new Shipper_Company_Ups_Node_Package ( 
          '02', 'LBS', $objLabelOptions->getWeight(), 
          (($fltInsuredValue <= 0) ? null :  
              new Shipper_Company_Ups_Node_PackageServiceOption( '', 'EVS', 'USD', $fltInsuredValue )));
       
       if ( $objShipperAddressTo->isDomestic() )
            $nodePackage->setReferenceNumber( true, 'ID', $objLabelOptions->getOrderId() );
     
       // node of label specification
       $nodeLabelSpecification = new Shipper_Company_Ups_Node_LabelSpecification(
                Shipper_Company_Ups_Node_LabelSpecification::LPM_ZPL,
                Shipper_Company_Ups_Node_LabelSpecification::LIF_ZPL, 'Mozilla/4.5' );
       if ( !( $objShipperAddressTo->isDomestic() ) ) { // if zpl!
                $nodeLabelSpecification->setLabelHeight(4);
                $nodeLabelSpecification->setLabelWidth(8);
       }              

       // node shipment service
       $nodeShipmentServiceOptions = new Shipper_Company_Ups_Node_ShipmentServiceOption(); 
       if ( $objLabelOptions->getSaturdayDelivery() )
            $nodeShipmentServiceOptions->setSaturdayDelivery();
       foreach( $objShipperAccount->getNotificationEmails() as $strEmail ) 
            $nodeShipmentServiceOptions->setEmailNotification( $strEmail );
       
       $nodeShipTo = new Shipper_Company_Ups_Node_Address_ShipTo(  
                   $objShipperAddressTo, 
                   $objLabelOptions->getReceiverName(), 
                   $objLabelOptions->getReceiverPhone(),
                   $objLabelOptions->getReceiverName()); 
       // if ( !$objShipperAddressTo->isDomestic() )
       //     $nodeShipTo->setStateCode( '' );     
                   
       // REQUEST #1: preparing confirm XML-message: 
       $shipConfirmMsg = new Shipper_Company_Ups_Message_ShipConfirm('1.0');       
       $shipConfirmMsg->setShipmentDescription( 
            $objShipperLocation->getCompanyName().' Shipment' );

       if ( $objShipperAddressTo->isCanada() ) // or Puerto Rico!
            $shipConfirmMsg->setInvoiceLineTotal( 
                new Shipper_Company_Ups_Node_InvoiceLineTotal('USD', $objLabelOptions->getSubTotal() ) );            
            
       $shipConfirmMsg->setNodes(
            new Shipper_Company_Ups_Node_Request( 
                   'ShipConfirm', 'UPS Ship Request '.$objLabelOptions->getOrderId(), 'validate' ),
            $nodeLabelSpecification,
            new Shipper_Company_Ups_Node_Address_Shipper( 
                   $objShipperLocation, $objShipperAccount ),
            new Shipper_Company_Ups_Node_Address_ShipFrom( 
                   $objShipperLocation ),
            $nodeShipTo,            
            new Shipper_Company_Ups_Node_PaymentInformation( 
                   $objShipperAccount->getProperty( 'Ups', 'Account Number') ),
            new Shipper_Company_Ups_Node_Service( $objLabelOptions->getCode() ),
            $nodeShipmentServiceOptions,
            $nodePackage );                
      
       // Develop_Debug::dumpDie( Develop_Debug::formattedXml( $shipConfirmMsg->toXml() ) );
         
       $hash = $shipConfirmMsg->getHash();     
       $objLogEntry = Shipper_Component::getLogEntryByHash( 'Ups', $hash );

       /**
        * Changed by Sergey Palutin
        */
       $strRequest = '';
       $strResponse = '';
       if ( is_object( $objLogEntry ) ) {
           $strRequest = $objLogEntry->getRequest();
           $strResponse = $objLogEntry->getResponse();
       }
      if(!empty($strResponse)){
          $objShipService->setRequest( $strRequest );
          $objShipService->setResponse( $strResponse );
      } else {
            $objShipService->shipConfirmRequest( $shipConfirmMsg );
            Shipper_Component::addLogEntry( 'Ups', 'SHIP_CONFIRM', 
                  $objShipService->getResponseCode() ,    
                  $objShipService->getRequest(), 
                  $objShipService->getResponse(), 
                  $objLabelOptions->getOrderId(), $hash );
       }
       
       if ( $objShipService->getResponseCode() == 0) {
           // if shipping labe error occured
           if ( Develop_Mode::is( 'shippinglabel' ) ) {
               Develop_Debug::dump ( Develop_Debug::formattedXml( $objShipService->getRequest() ) );
               echo '<hr />';
               Develop_Debug::dump ( Develop_Debug::formattedXml( $objShipService->getResponse() ) );
           }
           throw new Shipper_Label_Exception( $objShipService->getErrorText() );
       }
       // Develop_Debug::dump ( Develop_Debug::formattedXml( $objShipService->getResponse() ) );
       if ( $objShipService->getOrderTracking() == '' ) {
            throw new Shipper_Label_Exception( 'Error while getting label tracking number ' );
       }

       // REQUEST #2 : ship accept message
       $shipAcceptMsg = new Shipper_Company_Ups_Message_ShipAccept('1.0');
       $shipAcceptMsg->setNodes(
            new Shipper_Company_Ups_Node_Request( 
                    Shipper_Company_Ups_Node_Request::ActionShipAccept, 
                    $objLabelOptions->getCustomerComment(), 
                    '1' ), 
            new Shipper_Company_Ups_Node_ShipmentDigest( 
                    $objShipService->getShipmentDigest() )
       );

       // show response for debug
       $hash = $shipAcceptMsg->getHash();     
       $objLogEntry = Shipper_Component::getLogEntryByHash( 'Ups', $hash );
       /**
        * Changed by Sergey Palutin
        */
       
       $strRequest = '';
       $strResponse = '';
       if ( is_object( $objLogEntry ) ) {
           $strRequest = $objLogEntry->getRequest();
           $strResponse = $objLogEntry->getResponse();
       }
       if(!empty($strResponse)){
          $objShipService->setRequest( $strRequest );
          $objShipService->setResponse( $strResponse );
       } else {
            $objShipService->shipAcceptRequest( $shipAcceptMsg );
            Shipper_Component::addLogEntry( 'Ups', 'SHIP_ACCEPT', 
                  $objShipService->getResponseCode() ,    
                  $objShipService->getRequest(), 
                  $objShipService->getResponse(), 
                  $objLabelOptions->getOrderId(), $hash );
       }
       if ( $objShipService->getOrderTracking() == '' ) {
            if ( Develop_Mode::is( 'shippinglabel' ) ) {
                Develop_Debug::dump ( Develop_Debug::formattedXml( $objShipService->getRequest() ) );
                echo '<hr />';
                Develop_Debug::dump ( Develop_Debug::formattedXml( $objShipService->getResponse() ) );
            }
            throw new Shipper_Label_Exception( 'Tracking was not set' );
       }
              
       //Develop_Debug::dump( 
       //     Develop_Debug::formattedXml( $objShipService->getResponse() ) );
            
       $zplLabel = $objShipService->getLabelCode();
       $file = new Common_File( Zend_Registry::get('AppFolder') 
            . '/out/shipping_labels/' . $objShipService->getOrderTracking().'.zpl');
       $file->save( (string)$zplLabel );
            
       $tblLabel = new Shipper_Label_Table();
       $objLabel = $tblLabel->createRow();
       $objLabel->shl_order_id = $objLabelOptions->getOrderId();
       $objLabel->shl_dt_printed = date( 'Y-m-d H:i:s' );
       $objLabel->shl_method = $arrCodes[ $objLabelOptions->getCode() ] ;
       $objLabel->shl_weight = $objLabelOptions->getWeight();
       $objLabel->shl_charge = $objShipService->getTotalCharges(); 
       $objLabel->shl_surcharge = $objShipService->getOptionsCharges();
       $objLabel->shl_tracking = $objShipService->getOrderTracking();
       //$objLabel->shl_notified = 0;
       $objLabel->shl_is_return = 0;
       $objLabel->shl_test_mode = $objShipService->isTest();
       
       $objLabel->shl_user = Shipper_Component::getUserName();
       $objLabel->setDestinationAddress( $objShipperAddressTo );
       $objLabel->setShipperAccount( $objShipperAccount ); 
       $objLabel->setShipperLocation( $objShipperLocation );
       $objLabel->shl_company = 'Ups'; 
       return $objLabel;             
    }

    /**
     * send request to void shipping label
     * @return bool result
     */    
    public function requestVoidLabel( Shipper_Label $objLabel ) 
    { 
        // throw new Shipper_Exception( 'Voiding Shipping Label is not Required for UPS' );
        return true;
    }
    
    /**
     * send request for return label
     * if success, result is saved 
     * as GIF under /out/return_labels/#initial tracking#.gif
     * as PDF under /out/return_labels/#initial tracking#.pdf
     * 
     * @param Shipper_Label $objShipperLabel,
     * @param Shipper_Address $objShipperAddressTo,

     * 
     * Warning: tracking of return label is 
     * @return tracking number of return package
     */    
    public function requestReturnLabel( 
            Shipper_Account $objShipperAccount,
            Shipper_Location $objShipperLocation, 
            Shipper_Address $objShipperAddressTo,
            $objLabelOptions ) 
    { 
    
       $objShipService = $this->initService( $objShipperAccount, 
                'Shipper_Company_Ups_Service_Ship' );
       
              // Validating shipping method code
       $arrCodes = $this->getShippingMethodCodes(); 
       
       if ( !isset( $arrCodes[ $objLabelOptions->getCode() ] ) )
            throw new Shipper_Label_Exception( 
                    'Shipping Method Code '.$objLabelOptions->getCode().' is invalid' );
       if ( !$objLabelOptions->isValid() ) 
            throw new Shipper_Label_Exception( 
                    'Shipping Label Option Error: '.$objLabelOptions->getError() );

       // declaring package node
       $nodePackage = new Shipper_Company_Ups_Node_Package ( 
          '02', 'LBS', $objLabelOptions->getWeight() );
       $nodePackage->setReferenceNumber( true, 'ID', 
            $objLabelOptions->getOrderId() );
       $nodePackage->setPackageDescription( 'Order #'.$objLabelOptions->getOrderId() );
     
       // node of label specification
       $nodeLabelSpecification = new Shipper_Company_Ups_Node_LabelSpecification(
                Shipper_Company_Ups_Node_LabelSpecification::LPM_GIF,
                Shipper_Company_Ups_Node_LabelSpecification::LPM_GIF, 
                'Mozilla/4.5' );
       $nodeLabelSpecification->setLabelHeight(4);
       $nodeLabelSpecification->setLabelWidth(8);

       $nodeShipmentServiceOptions = new Shipper_Company_Ups_Node_ShipmentServiceOption(); 
       foreach( $objShipperAccount->getNotificationEmails() as $strEmail ) 
            $nodeShipmentServiceOptions->setEmailNotification( $strEmail );
/*
 * WAS INITIALLY, but we are switching source and destination
 
       $nodeShipTo = new Shipper_Company_Ups_Node_Address_ShipTo(  
                   $objShipperAddressTo, 
                   $objLabelOptions->getReceiverName(), 
                   $objLabelOptions->getReceiverPhone(),
                   $objLabelOptions->getReceiverName());       
       $nodeShipFrom = new Shipper_Company_Ups_Node_Address_ShipFrom( 
                   $objShipperLocation );   
*/                 
            
       $nodeShipFrom = new Shipper_Company_Ups_Node_Address_ShipFrom(  
                   $objShipperAddressTo, 
                   $objLabelOptions->getReceiverName(), 
                   $objLabelOptions->getReceiverPhone(),
                   $objLabelOptions->getReceiverName());       
       $nodeShipTo = new Shipper_Company_Ups_Node_Address_ShipTo( 
                   $objShipperLocation );        
            
       // REQUEST #1: preparing confirm XML-message: 
       $shipConfirmMsg = new Shipper_Company_Ups_Message_ShipConfirm('1.0');       
       $shipConfirmMsg->setShipmentDescription( 
            $objShipperLocation->getCompanyName().' Order '
            .$objLabelOptions->getOrderId().' Return Shipment' );
       $shipConfirmMsg->setReturnType(true);
       $shipConfirmMsg->setNodes(
            new Shipper_Company_Ups_Node_Request( 
                   'ShipConfirm', 'UPS Return Ship Request '
                   .$objLabelOptions->getOrderId(), 'validate' ),
            $nodeLabelSpecification,
            new Shipper_Company_Ups_Node_Address_Shipper( 
                   $objShipperLocation, $objShipperAccount ),
            $nodeShipFrom,
            $nodeShipTo,            
            new Shipper_Company_Ups_Node_PaymentInformation( 
                   $objShipperAccount->getProperty( 'Ups', 'Account Number') ),
            new Shipper_Company_Ups_Node_Service( $objLabelOptions->getCode() ),
            $nodeShipmentServiceOptions,
            $nodePackage );

       $hash = $shipConfirmMsg->getHash();     
       $objLogEntry = Shipper_Component::getLogEntryByHash( 'Ups', $hash ); 
        /**
         * Changed by Sergey Palutin
         */
        $strRequest = '';
        $strResponse = '';
        if ( is_object( $objLogEntry ) ) {
            $strRequest = $objLogEntry->getRequest();
            $strResponse = $objLogEntry->getResponse();
        }
        if(!empty($strResponse)){
           $objShipService->setRequest( $strRequest );
           $objShipService->setResponse( $strResponse );
        } else {
            $objShipService->shipConfirmRequest( $shipConfirmMsg );
            Shipper_Component::addLogEntry( 'Ups', 'RETURN_CONFIRM', 
                  $objShipService->getResponseCode() ,    
                  $objShipService->getRequest(), 
                  $objShipService->getResponse(), 
                  $objLabelOptions->getOrderId(), $hash );
       }
       
       if ( $objShipService->getResponseCode() == 0) {
           // if shipping labe error occured
           if ( Develop_Mode::isLocal() || Develop_Mode::is( 'shippinglabel' ) ) {
               Develop_Debug::dump ( Develop_Debug::formattedXml( $objShipService->getRequest() ) );
               echo '<hr />';
               Develop_Debug::dump ( Develop_Debug::formattedXml( $objShipService->getResponse() ) );
           }
           throw new Shipper_Label_Exception( $objShipService->getErrorText() );
       }
       // Develop_Debug::dump ( Develop_Debug::formattedXml( $objShipService->getResponse() ) );

       // REQUEST #2 : ship accept message
       $shipAcceptMsg = new Shipper_Company_Ups_Message_ShipAccept('1.0');
       $shipAcceptMsg->setNodes(
            new Shipper_Company_Ups_Node_Request( 
                    Shipper_Company_Ups_Node_Request::ActionShipAccept, 
                    '', '1' ), 
            new Shipper_Company_Ups_Node_ShipmentDigest( 
                    $objShipService->getShipmentDigest() )
       );

       // show response for debug
       $hash = $shipAcceptMsg->getHash();     
       $objLogEntry = Shipper_Component::getLogEntryByHash( 'Ups', $hash ); 
        /**
         * Changed by Sergey Palutin
         */
        $strRequest = '';
        $strResponse = '';
        if ( is_object( $objLogEntry ) ) {
            $strRequest = $objLogEntry->getRequest();
            $strResponse = $objLogEntry->getResponse();
        }
        if(!empty($strResponse)){
           $objShipService->setRequest( $strRequest );
           $objShipService->setResponse( $strResponse );
        } else {
            $objShipService->shipAcceptRequest( $shipAcceptMsg );
            Shipper_Component::addLogEntry( 'Ups', 'RETURN_ACCEPT', 
                  $objShipService->getResponseCode() ,    
                  $objShipService->getRequest(), 
                  $objShipService->getResponse(), 
                  $objLabelOptions->getOrderId(), $hash );
       }
       if ( $objShipService->getOrderTracking() == '' ) {
            if ( Develop_Mode::is( 'shippinglabel' ) ) {
                Develop_Debug::dump ( Develop_Debug::formattedXml( $objShipService->getRequest() ) );
                echo '<hr />';
                Develop_Debug::dump ( Develop_Debug::formattedXml( $objShipService->getResponse() ) );
            }
            throw new Shipper_Label_Exception( 'Retrun tracking was not set' );
       }       
       
      // echo '<div>'
      //   .' Old Tracking: ' . $objShipperLabel->getTracking()
      //   .' New Tracking: ' . $objShipService->getOrderTracking()
      //  .' </div>';
            
       $tblLabel = new Shipper_Label_Table();
       $objLabel = $tblLabel->createRow();
       $objLabel->shl_order_id = $objLabelOptions->getOrderId();
       $objLabel->shl_dt_printed = date( 'Y-m-d H:i:s' );
       $objLabel->shl_method = $arrCodes[ $objLabelOptions->getCode() ] ;
       $objLabel->shl_weight = $objLabelOptions->getWeight();
       $objLabel->shl_charge = $objShipService->getTotalCharges(); 
       $objLabel->shl_surcharge = $objShipService->getOptionsCharges();
       $objLabel->shl_tracking = $objShipService->getOrderTracking();
       //$objLabel->shl_notified = 0;
       $objLabel->shl_is_return = 1;
       $objLabel->shl_test_mode = $objShipService->isTest();
       
       $objLabel->shl_user = Shipper_Component::getUserName();
       $objLabel->setDestinationAddress( $objShipperAddressTo );
       $objLabel->setShipperAccount( $objShipperAccount ); 
       $objLabel->setShipperLocation( $objShipperLocation );
       $objLabel->shl_company = 'Ups'; 

       
       $gifLabel = $objShipService->getLabelCode();
       $strGifFile = $objLabel->getGifFile();
       $file = new Common_File( $strGifFile );
       $file->save( (string)$gifLabel ); 
       Shipper_Label::rotateFile( $strGifFile );
       
       $objLabel->createPdfFromGif( 4*72, 6*72 );
       return $objLabel;            
    }
    
    /**
     * send request to get address candidates
     */
    public function requestAddressValidation( $objShipperAccount, $objAddress )
    {
       $objService = $this->initService( $objShipperAccount,
            'Shipper_Company_Ups_Service_AddressValidation' );
       
       $objMessage = new Shipper_Company_Ups_Message_AddressValidationRequest();
       $nodeAddress = new Shipper_Company_Ups_Node_AddressKeyFormat( $objAddress );
            
       
       $objMessage->setNodes( $nodeAddress, 
            Shipper_Company_Ups_Message_AddressValidationRequest::OptionAddressValidation );
       
       // Develop_Debug::dumpDie( $objMessage->toXml() );
            
       // cache request here to avoid double responses
       $hash = $objMessage->getHash();
       /**
        * @var Shipper_Log $objLogEntry
        */
       $objLogEntry = Shipper_Component::getLogEntryByHash( 'Ups', $hash );
       /**
        * Changed by Sergey Palutin
        */
       $strResponse = '';
       if ( is_object( $objLogEntry ) ) {
           $strResponse = $objLogEntry->getResponse();

       }

       if ($strResponse != '') {
           $objService->setResponse( $strResponse);
           if ( is_object( $objLogEntry ) ) {
               #$objLogEntry->delete();
           }
       }
       else {
            $objService->addressValidationRequest( $objMessage );
            Shipper_Component::addLogEntry( 'Ups', 'ADDRESS_VALIDATION', 
                  $objService->getErrorCode() ,    
                  $objService->getRequest(), 
                  $objService->getResponse(), 
                  $orderId, $hash );
       }
       
       $tblValidation = Shipper_Validation::Table();
       
       
       if ($objService->getErrorCode() == 0 && !$objService->isNoCandidates() ) {
            $arrCandidates = $objService->getAddressCanidates();
            #echo '<div>Candidates: '.count( $arrCandidates ).'</div>';
            foreach ($arrCandidates as $i => $arrSuggestion) {
                $row = $tblValidation->createRow();
                $row->shv_company = 'Ups';
                $row->shv_order = $orderId;
                $row->shv_number = $i + 1;
                
                // initial information
                $row->shv1_zip = $objAddress->getZip();
                $row->shv1_country = $objAddress->getCountry();
                $row->shv1_county = '';
                $row->shv1_state = $objAddress->getState();
                $row->shv1_city = $objAddress->getCity();
                $row->shv1_addr1 = $objAddress->getAddressLine1();
                $row->shv1_addr2 = $objAddress->getAddressLine2();
                $row->shv1_addr3 = $objAddress->getAddressLine3();
                
                // validated information
                $row->shv2_zip = $arrSuggestion['PostcodePrimaryLow'];
                $row->shv2_country = $arrSuggestion['CountryCode'];
                $row->shv2_county = '';
                $row->shv2_state = (isset( $arrSuggestion['PoliticalDivision1']) ? $arrSuggestion['PoliticalDivision1'] : '');
                $row->shv2_city  = (isset( $arrSuggestion['PoliticalDivision2']) ? $arrSuggestion['PoliticalDivision2'] : '');
                $row->shv2_addr1 = (isset( $arrSuggestion['AddressLine1']) ? $arrSuggestion['AddressLine1'] : '');
                $row->shv2_addr2 = (isset( $arrSuggestion['AddressLine2']) ? $arrSuggestion['AddressLine2'] : '');
                $row->shv2_addr3 = (isset( $arrSuggestion['AddressLine3']) ? $arrSuggestion['AddressLine3'] : '');
                $row->shv2_zipx4 = (isset( $arrSuggestion['PostcodeExtendedLow']) ? $arrSuggestion['PostcodeExtendedLow'] : '');
                $row->save();
            }
       }
       return $objService;
    }
    /**
     * @param Shipper_Account $objShipperAccount
     * @param Shipper_Location $objShipperLocation
     * @param Shipper_Address $objShipperAddressTo
     * @param Shipper_Company_Ups_RateOption $objRateOption
     * @return float
     * @author Sergey Palutin
     */
    public function requestShippingRate(
        Shipper_Account $objShipperAccount,
        Shipper_Location $objShipperLocation,
        Shipper_Address $objShipperAddressTo,
        Shipper_Company_Ups_RateOption $objRateOption
    ) 
    {

        $objRateRequest = new Shipper_Company_Ups_Message_RateRequest('1.0');


        $nodeShipmentServiceOptions = new Shipper_Company_Ups_Node_ShipmentServiceOption();
        if ( $objRateOption->getSaturdayDelivery() ) {
            $nodeShipmentServiceOptions->setSaturdayDelivery();
        }

        $fltInsuredValue = $objRateOption->getInsurance();
        $objRateRequest->setNodes(
            new Shipper_Company_Ups_Node_Request('Rate', 'Rating and Service', 'Rate'),
            new Shipper_Company_Ups_Node_Pickup($objRateOption->getPickup()),
            new Shipper_Company_Ups_Node_Address_Shipper($objShipperLocation, $objShipperAccount),
            new Shipper_Company_Ups_Node_Address_ShipTo(
                $objShipperAddressTo,
                $objRateOption->getReceiverName(),
                $objRateOption->getReceiverPhone(),
                $objRateOption->getReceiverName()
            ),
            new Shipper_Company_Ups_Node_Address_ShipFrom($objShipperLocation),
            new Shipper_Company_Ups_Node_Service($objRateOption->getCode()),
            new Shipper_Company_Ups_Node_PaymentInformation($objShipperAccount->getProperty( 'Ups', 'Account Number')),
            new Shipper_Company_Ups_Node_Package('02', 'LBS', $objRateOption->getWeight(),
            (($fltInsuredValue <= 0) ? null :
              new Shipper_Company_Ups_Node_PackageServiceOption( '', 'EVS', 'USD', $fltInsuredValue ))),
            $nodeShipmentServiceOptions
        );

        #Develop_Debug::dumpDie($objRateRequest->toXml());
        /**
         * @var Shipper_Company_Ups_Service_Rate $objService;
         */
        $objService = $this->initService( $objShipperAccount,
             'Shipper_Company_Ups_Service_Rate' );
        
        $hash = $objRateRequest->getHash();     
        $objLogEntry = Shipper_Component::getLogEntryByHash( 'Ups', $hash );
        /**
         * Changed by Sergey Palutin
         */
        $strRequest = '';
        $strResponse = '';
        if ( is_object( $objLogEntry ) ) {
            $strRequest = $objLogEntry->getRequest();
            $strResponse = $objLogEntry->getResponse();
            #echo '[cashed!!!] ';
             #
             #$objService->setResponse(  );
        }
        if (!empty($strResponse)) {
            $objService->setRequest($strRequest);
            $objService->setResponse($strResponse);
        }
        else {
             #echo '[new requested] ';
             $objService->sendRateRequest($objRateRequest);
             Shipper_Component::addLogEntry( 'Ups', 'CALCULATE_RATE',
                   $objService->getResponseCode() ,
                   $objService->getRequest(),
                   $objService->getResponse(),
                   $objRateOption->getOrderId(), $hash );
        }
        return $objService->getShipmentRateValue();
    }
}