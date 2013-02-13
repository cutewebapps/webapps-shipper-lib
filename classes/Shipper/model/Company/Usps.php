<?php
/*

Downloadable Pricing files:
http://www.usps.com/prices/downloadable-pricing-files.htm

Zone charts:
http://postcalc.usps.gov/Zonecharts/

*/

class Shipper_Company_Usps implements Shipper_Company
{
    public function getName() 
    {
        return 'USPS';
    }
    public function getServices() 
    {
        return array(
           'Shipping Label',
           'Void Label',
           'Return Label',
           'Change Password',
           'Recredit',
           'Transaction List',
            'Shipping Rate'
        ); 
    }
    public function getAccountFields()    
    {
        return array(
          'shacc_requesterid' => 'Requester ID',
          'shacc_accountid'   => 'Account ID',
          'shacc_pass'        => 'Pass Phrase',
        );
    }
    
    /** returns detected method identifier by its name **/
    public function getMethodByName( $strName )
    {
        //return method code from name
        $strName = strtolower( str_replace( ' ', '', 
               str_replace( 'usps', '', strtolower( $strName) ) ));
        switch ( $strName ) {
            case 'express':
                return Shipper_Company_Usps_Value_MailClass::EXPRESS;
            case 'first': case 'firstclass': case 'firstclassmail':
                return  Shipper_Company_Usps_Value_MailClass::FIRST;   
            case 'librarymail': case 'library';
                return  Shipper_Company_Usps_Value_MailClass::LIBRARYMAIL;
            case 'mediamail': case 'media':
                return  Shipper_Company_Usps_Value_MailClass::MEDIAMAIL;
            case 'parcelpost': case 'parcel':
                return  Shipper_Company_Usps_Value_MailClass::PARCELPOST;
            case 'parcelselect':
                return  Shipper_Company_Usps_Value_MailClass::PARCELSELECT;
            case 'priority': case 'prioritymail':
                return  Shipper_Company_Usps_Value_MailClass::PRIORITY;
            case 'standardmail': case 'standard':
                return  Shipper_Company_Usps_Value_MailClass::STANDARDMAIL;
            case 'express': case 'expressmail': 
            case 'emi': case 'expressmailinternational':
                return  Shipper_Company_Usps_Value_MailClass::EXPRESSMAILINTERNATIONAL;
            case 'firstclassmailinternational': case 'fcmi':
                return  Shipper_Company_Usps_Value_MailClass::FIRSTCLASSMAILINTERNATIONAL;
            case 'international': case 'pmi' : case 'prioritymailintermational':
                return  Shipper_Company_Usps_Value_MailClass::PRIORITYMAILINTERNATIONAL;
        }
        throw new Shipper_Exception( 'Invalid USPS mail class '.$strName );
    } 
    
    /*
     * we shall discover direct USPS tracking link in future!
     */
    public function getTrackingLink( $strTracking ) 
    {
        // 'http://www.usps.com/shipping/trackandconfirm.htm' is not a permalink!
        return 'http://trackthepack.com/track/'.$strTracking;
    }
    
    public function getPackageEstimateCost( $objShipperCalc )
    {
        return 6.95;
    }
    
    
    private function _initService( Shipper_Account $objShipperAccount,
            $className = 'Shipper_Company_Usps_Service_Label' ) {
        if ( !$objShipperAccount->isValid() )
            throw new Shipper_Exception( 'Shipping Account is not configured' );
        $bTestMode = $objShipperAccount->isTestMode() || 
                Develop_Mode::isLocal() || Develop_Mode::isDev(); 
                
        $labelService = new $className (
            $objShipperAccount->getProperty( 'Usps', 'Requester ID'), 
            $objShipperAccount->getProperty( 'Usps', 'Account ID'), 
            $objShipperAccount->getProperty( 'Usps', 'Pass Phrase'), 
            $bTestMode );

        return $labelService;    
    }
    
    /**
     * send request to get shipping label
     * 
     * @return Shipping_Label object, ready to be saved
     */
    public function requestShippingLabel(  
            Shipper_Account $objShipperAccount,
            Shipper_Location $objShipperLocation, 
            Shipper_Address $objShipperAddressTo, 
            $objLabelOptions,
            $strOutputFormat = 'ZPL' ) 
    {
        if ( $strOutputFormat != 'ZPL' ) 
            throw new Shipper_Label_Exception( 'Only ZPL format opf labels is currently supported');
        $labelService = $this->_initService( $objShipperAccount );
        
        $bTestMode = $objShipperAccount->isTestMode() || 
                Develop_Mode::isLocal() || Develop_Mode::isDev(); 
                
        $imageFormat = Shipper_Company_Usps_Value_ImageFormat::ZPLII;
        
        $labelType = $objShipperAddressTo->isInternational()
                ? Shipper_Company_Usps_Value_LabelType::INTERNATIONAL 
                : Shipper_Company_Usps_Value_LabelType::DEFAULT_LABEL_TYPE;

        $switchTestMode = new Shipper_Company_Usps_Value_Switch( intval($bTestMode) );
                
        $labelRequest = new Shipper_Company_Usps_Request_Label(
            (string)$switchTestMode, $labelType, $imageFormat );
        
        $labelRequest->MailClass = new Shipper_Company_Usps_Value_MailClass( 
            $objLabelOptions->getCode() );
        $labelRequest->DateAdvance = '0';
        $labelRequest->WeightOz = ceil( $objLabelOptions->getWeight()*16 );
        $labelRequest->CostCenter = '0';
        $labelRequest->PartnerCustomerID = '12345ABCD';
        $labelRequest->PartnerTransactionID = '12345ABCD';
        $labelRequest->Description = 'Order #'
            . $objLabelOptions->getOrderId() . ' Label';
               
        $labelRequest->Services = array(
            'CertifiedMail' => new Shipper_Company_Usps_Value_Switch(
                $objLabelOptions->isCertifiedEmail()  
                ? Shipper_Company_Usps_Value_Switch::ON 
                : Shipper_Company_Usps_Value_Switch::OFF
            ), 
            'DeliveryConfirmation' => new Shipper_Company_Usps_Value_Switch(
                $objLabelOptions->isDeliveryConfirmation()  
                ? Shipper_Company_Usps_Value_Switch::ON 
                : Shipper_Company_Usps_Value_Switch::OFF
            ), 
            'ElectronicReturnReceipt' => new Shipper_Company_Usps_Value_Switch(
                 $objLabelOptions->isReturnReceipt()  
                ? Shipper_Company_Usps_Value_Switch::ON 
                : Shipper_Company_Usps_Value_Switch::OFF
            ), 
            'InsuredMail' => new Shipper_Company_Usps_Value_Switch(
                $objLabelOptions->isInsured()
                ? Shipper_Company_Usps_Value_Switch::ON 
                : Shipper_Company_Usps_Value_Switch::OFF
            ), 
            'SignatureConfirmation' => new Shipper_Company_Usps_Value_Switch( 
                $objLabelOptions->isSignatureRequired() 
                ? Shipper_Company_Usps_Value_Switch::ON 
                : Shipper_Company_Usps_Value_Switch::OFF
            ) 
        );

        // Setting shipper & destination properties
        $labelRequest->setOrigin( $objShipperLocation );
        $labelRequest->setDestination( $objShipperAddressTo, $objLabelOptions );
        $labelRequest->setZeroCustoms();
        $labelRequest->Value = '1';
        $labelRequest->ImageFormat = new Shipper_Company_Usps_Value_ImageFormat( $imageFormat );
        
        
        $hash = $labelRequest->getHash();
        $objLogEntry = Shipper_Component::getLogEntryByHash( 'Usps', $hash ); 
        if ( 0 && is_object( $objLogEntry ) ) {
             $labelService->setResponse( unserialize( $objLogEntry->getResponse() ));
        } else {
             $labelService->getPostageLabel( $labelRequest ); // soap request itself
                     
             Shipper_Component::addLogEntry( 'Usps', 'GET_POSTAGE_LABEL', 
                  $labelService->getStatus(),    
                  $labelRequest->toXml(), 
                  serialize( $labelService->getResponseObject() ), 
                  $objLabelOptions->getOrderId(), $hash );
        }
        
        if ( Develop_Mode::is( 'shippinglabel' ) ) {
           Develop_Debug::dump( $labelRequest->toSoapArray() );
           Develop_Debug::dumpDie( $labelService->getResponseObject() );
        }
        
        if ( $labelService->getStatus() == 0 ) {
            
            $zplLabel = $labelService->getLabelCode();
            $objZpl = new Zpl_Writer($zplLabel);
            $objZpl->replaceEndOfLabel();
            $objZpl->setFieldOrigin(50, 1185);
            $objZpl->setFont(0, 42, 42);    
            $strShopName = Shipper_Component::getInstance()->getConfig()->ShopName; // $objLabelOptions->getShopName();
            
            $objZpl->putFieldData( $strShopName. ' Order Number: ' . $objLabelOptions->getOrderId());    
            $objZpl->barcodeEan8(300, 1285, $objLabelOptions->getOrderId());
            $objZpl->endLabel();
            $zplLabel = (string)$objZpl;
            
            $file = new Common_File( Zend_Registry::get('AppFolder') 
                . '/out/shipping_labels/' . $labelService->getTrackingNumber().'.zpl');
            $file->save( (string)$zplLabel );
            
            $tblLabel = new Shipper_Label_Table();
            $objLabel = $tblLabel->createRow();  
            $objLabel->shl_order_id = $objLabelOptions->getOrderId();
            $objLabel->shl_dt_printed = date( 'Y-m-d H:i:s' );
            $objLabel->shl_method = (string)( new Shipper_Company_Usps_Value_MailClass( $objLabelOptions->getCode() ));
            $objLabel->shl_weight = $objLabelOptions->getWeight();
            $objLabel->shl_charge = $labelService->getFinalPostage();   
            $objLabel->shl_surcharge = 0; 
            $objLabel->shl_tracking = $labelService->getTrackingNumber();
            //$objLabel->shl_notified = 0;
            $objLabel->shl_test_mode = $bTestMode;
            $objLabel->shl_user = Shipper_Component::getUserName();
            $objLabel->setDestinationAddress( $objShipperAddressTo );
            $objLabel->setShipperAccount( $objShipperAccount ); 
            $objLabel->setShipperLocation( $objShipperLocation );
            $objLabel->shl_company = 'Usps';
            $objLabel->shl_is_return = 0;
            
            // do this only if auto-recredit option is enabled!!!
            
            $floatPostageBalance = $labelService->getPostageBalance();
            $intTransactionId = $labelService->getTransactionID();
            if ($intTransactionId != 0 && $floatPostageBalance < 10){

                $fltSum = 250;
                $serviceRecredit = new Shipper_Company_Usps_Service_Recredit (
                    $objShipperAccount->getProperty( 'Usps', 'Requester ID'), 
                    $objShipperAccount->getProperty( 'Usps', 'Account ID'), 
                    $objShipperAccount->getProperty( 'Usps', 'Pass Phrase') );
                $serviceRecredit->getRecredit( $fltSum );
            }
                    
        } else {
            throw new Shipper_Label_Exception( $labelService->getErrorMessage() );
        }
        return $objLabel;
    }

    public function requestReturnLabel(  
            Shipper_Account $objShipperAccount,
            Shipper_Location $objShipperLocation, 
            Shipper_Address $objShipperAddressTo, 
            $objLabelOptions ) 
    {
        $labelService = $this->_initService( $objShipperAccount );
        
        $bTestMode = $objShipperAccount->isTestMode() || 
                Develop_Mode::isLocal() || Develop_Mode::isDev(); 
                
        $imageFormat = Shipper_Company_Usps_Value_ImageFormat::PDF;

        $labelType = $objShipperAddressTo->isInternational()
                ? Shipper_Company_Usps_Value_LabelType::INTERNATIONAL 
                : Shipper_Company_Usps_Value_LabelType::DEFAULT_LABEL_TYPE;
                
        $labelRequest = new Shipper_Company_Usps_Request_Label(
            new Shipper_Company_Usps_Value_Switch( $bTestMode ), 
            $labelType, $imageFormat );
        
        $labelRequest->MailClass = new Shipper_Company_Usps_Value_MailClass( 
            $objLabelOptions->getCode() );
        $labelRequest->DateAdvance = '0';
        $labelRequest->WeightOz = ceil( $objLabelOptions->getWeight()*16 );
        $labelRequest->CostCenter = '0';
        $labelRequest->PartnerCustomerID = '12345ABCD';
        $labelRequest->PartnerTransactionID = '12345ABCD';
        $labelRequest->Description = 'Order #'
            . $objLabelOptions->getOrderId() . ' Return Shipping Label';
               
        $labelRequest->Services = array(
            'CertifiedMail' => new Shipper_Company_Usps_Value_Switch(
                Shipper_Company_Usps_Value_Switch::OFF
            ), 
            'DeliveryConfirmation' => new Shipper_Company_Usps_Value_Switch(
                Shipper_Company_Usps_Value_Switch::OFF
            ), 
            'ElectronicReturnReceipt' => new Shipper_Company_Usps_Value_Switch(
                Shipper_Company_Usps_Value_Switch::OFF
            ), 
            'InsuredMail' => new Shipper_Company_Usps_Value_Switch(
                Shipper_Company_Usps_Value_Switch::OFF
            ), 
            'SignatureConfirmation' => new Shipper_Company_Usps_Value_Switch( 
                Shipper_Company_Usps_Value_Switch::OFF
            ) 
        );

        // Setting shipper & destination properties
        $strPhone = $objLabelOptions->getReceiverPhone();
        $strPhone = trim( preg_replace( '@\D+@', '', $strPhone ));
        $labelRequest->setOriginAddress( $objShipperAddressTo );
        $labelRequest->FromCompany = $objLabelOptions->getReceiverName();
        $labelRequest->FromPhone = $strPhone;
        
        $labelRequest->setDestinationAddress( $objShipperLocation->getAddress() );
        $labelRequest->ToPhone = $objShipperLocation->getPhone();
        $labelRequest->ToName = $objShipperLocation->getCompanyName();
        
        $labelRequest->setZeroCustoms();
        $labelRequest->Value = '1';
        $labelRequest->ImageFormat = new Shipper_Company_Usps_Value_ImageFormat( $imageFormat );
                
        $hash = $labelRequest->getHash();
        $objLogEntry = Shipper_Component::getLogEntryByHash( 'Usps', $hash ); 
        if ( is_object( $objLogEntry ) ) {
            $mixResponse = $objLogEntry->getResponse();

        }
        if (!empty($mixResponse)){
            $labelService->setResponse( unserialize( $mixResponse ));
        }else {
             $labelService->getPostageLabel( $labelRequest ); // soap request itself
                     
             Shipper_Component::addLogEntry( 'Usps', 'GET_RETURN_LABEL', 
                  $labelService->getStatus(),    
                  $labelRequest->toXml(), 
                  serialize( $labelService->getResponseObject() ), 
                  $objLabelOptions->getOrderId(), $hash );
        }
        
        if ( Develop_Mode::is( 'shippinglabel' ) ) {
           Develop_Debug::dump( $labelRequest->toSoapArray() );
           Develop_Debug::dumpDie( $labelService->getResponseObject() );
        }
        
        if ( $labelService->getStatus() == 0 ) {
            
            $pdfLabel = $labelService->getLabelCode();
            $file = new Common_File( Zend_Registry::get('AppFolder') 
                . '/out/shipping_labels/' . $labelService->getTrackingNumber().'.pdf');
            $file->save( (string)$pdfLabel );
            
            $tblLabel = new Shipper_Label_Table();
            $objLabel = $tblLabel->createRow();  
            $objLabel->shl_order_id = $objLabelOptions->getOrderId();
            $objLabel->shl_dt_printed = date( 'Y-m-d H:i:s' );
            $objLabel->shl_method = (string)( new Shipper_Company_Usps_Value_MailClass( $objLabelOptions->getCode() ));
            $objLabel->shl_weight = $objLabelOptions->getWeight();
            $objLabel->shl_charge = $labelService->getFinalPostage();   
            $objLabel->shl_surcharge = 0; 
            $objLabel->shl_tracking = $labelService->getTrackingNumber();
            //$objLabel->shl_notified = 0;
            $objLabel->shl_test_mode = $bTestMode;
            $objLabel->shl_user = Shipper_Component::getUserName();
            $objLabel->setDestinationAddress( $objShipperAddressTo );
            $objLabel->setShipperAccount( $objShipperAccount ); 
            $objLabel->setShipperLocation( $objShipperLocation );
            $objLabel->shl_company = 'Usps';
            $objLabel->shl_is_return = 1;

        } else {
            throw new Shipper_Label_Exception( $labelService->getErrorMessage() );
        }
        return $objLabel;
    }
    /**
     * send request to void shipping label
     * @return bool whether it was approved
     */    
    public function requestVoidLabel( Shipper_Label $objLabel ) 
    { 
        $objShipperAccount = $objLabel->getShipperAccount();
        $voidService = $this->_initService( $objShipperAccount, 
            'Shipper_Company_Usps_Service_Els' );
        
        $bTestMode = $objLabel->isTestMode() || $objShipperAccount->isTestMode() || 
                Develop_Mode::isLocal() || Develop_Mode::isDev(); 
                
        if ( $objLabel->getTracking() == '9122148008600123456781' )
            $bTestMode = 1;
                
        $voidRequest = new Shipper_Company_Usps_Request_Void (
            $bTestMode, $objLabel->getTracking(), $objShipperAccount );
            
        $bResult = $voidService->getRefund( $voidRequest );
        if ( !$bResult ) throw new Shipper_Exception( $voidService->getError() );
        return $bResult;
    }

    /**
     * send request to change password - void
     */
    public function requestChangePassword( Shipper_Account $objAccount )
    {
    }
    
    
    /**
     * send request to recredit account
     */
    public function requestRecredit( Shipper_Account $objShipperAccount, $fltAmount )
    {
        $recreditService = $this->_initService( $objShipperAccount, 
            'Shipper_Company_Usps_Service_Recredit' );
        
        $bResult = $recreditService->getRecredit( $fltAmount );
        return $bResult;
    }

    public function getShippingMethodCodes () {
        $arrOutput = array();
        $objMail = new Shipper_Company_Usps_Value_MailClass(0);
        $arrValues = $objMail->getAllValues();

        foreach ($arrValues as $key => $value) {
            //$arrOutput[$key] = trim(preg_replace('/([A-Z]){1}/', ' ${1}', $value));
            $arrOutput[$key] = $value;
        }
        return $arrOutput;
    }

    /**
     * @param Shipper_Account $objShipperAccount
     * @param Shipper_Location $objShipperLocation
     * @param Shipper_Address $objShipperAddressTo
     * @param Shipper_Company_Usps_RateOption $objRateOption
     * @return float
     * @author Sergey Palutin
     */
    public function requestShippingRate(
        Shipper_Account $objShipperAccount,
        Shipper_Location $objShipperLocation,
        Shipper_Address $objShipperAddressTo,
        Shipper_Company_Usps_RateOption $objRateOption
    )
    {

        $testData = array(
            'PostageRateRequest' => array(
                'RequesterID' => 'lsci',
                'CertifiedIntermediary' => array(
                    'AccountID' => '756978',
                    'PassPhrase' => 'productionluxotica'
                ),
                'MailClass' => 'Priority',
                'WeightOz' => '34.2',
                'Services' => array(
                    '@attributes' => array(
                        'DeliveryConfirmation' => 'TRUE',
                        'SignatureConfirmation' => 'OFF'
                    )
                ),
                'FromPostalCode' => '11217',
                'ToPostalCode' => '96768'
            )
        );
        /**
         * @var Shipper_Company_Usps_Service_Rate $objService
         */
        $objService = $this->_initService( $objShipperAccount, 'Shipper_Company_Usps_Service_Rate' );
        $bTestMode = $objShipperAccount->isTestMode() ||
                Develop_Mode::isLocal() || Develop_Mode::isDev(); 
        $switchTestMode = new Shipper_Company_Usps_Value_Switch( intval($bTestMode) );
        $objRequestRate = new Shipper_Company_Usps_Request_Rate((string)$switchTestMode);
        $objRequestRate->MailClass = new Shipper_Company_Usps_Value_MailClass(
            $objRateOption->getCode() );
        #$objRequestRate->DateAdvance = '0';
        #Develop_Debug::dumpDie($objRateOption->getWeight());
        $objRequestRate->WeightOz = ceil( $objRateOption->getWeight()*16 );
        $objRequestRate->Services = array(
                'DeliveryConfirmation' => new Shipper_Company_Usps_Value_Switch(
                    Shipper_Company_Usps_Value_Switch::OFF
                ),
                'SignatureConfirmation' => new Shipper_Company_Usps_Value_Switch(
                    Shipper_Company_Usps_Value_Switch::OFF
                ),
        );
        $objRequestRate->setOriginAddress( $objShipperAddressTo );

        $objRequestRate->setDestinationAddress( $objShipperLocation->getAddress() );

        $objRequestRate->setZeroCustoms();
        $objRequestRate->Value = '1';

        $hash = $objRequestRate->getHash();
        
        $objLogEntry = Shipper_Component::getLogEntryByHash( 'Usps', $hash );

        $strRequest = '';
        $strResponse = '';

        if ( is_object( $objLogEntry ) ) {
            $strRequest = $objLogEntry->getRequest();
            $strResponse = $objLogEntry->getResponse();
        }

        #Develop_Debug::dumpDie($strResponse);
        if (!empty($strResponse)) {
            $objService->setRequest($strRequest);
            $objService->setResponse(unserialize($strResponse));
        }else {
             #echo '[new requested] ';
             $objService->sendRateRequest($objRequestRate);
             Shipper_Component::addLogEntry( 'Usps', 'CALCULATE_RATE',
                  $objService->getStatus(),
                  $objRequestRate->toXml(),
                  serialize( $objService->getResponseObject() ),
                  $objRateOption->getOrderId(), $hash
             );
        }
        return $objService->getShipmentRateValue();
    }
}


