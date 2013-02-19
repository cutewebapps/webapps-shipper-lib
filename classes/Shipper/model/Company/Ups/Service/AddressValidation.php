<?php

final class Shipper_Company_Ups_Service_AddressValidation extends Shipper_Company_Ups_Service {

    const addressValidationRemoteAddressDebug = 'https://wwwcie.ups.com/ups.app/xml/XAV';
    const addressValidationRemoteAddress = 'https://onlinetools.ups.com/ups.app/xml/XAV';

    public function addressValidationRequest(Shipper_Company_Ups_Message_AddressValidationRequest $in_msg) {
        $this->initChannel(self::addressValidationRemoteAddress);
        $test = new Shipper_Company_Ups_Message_AccessRequest('1.0');

        $test->setCredentials($this->userId, $this->password, $this->licenseNumber);
        $this->sendRequest($test->toXML() . $in_msg->toXml());
    }

    public function getErrorMessage() {
        $items = $this->xmlReader->xpath('/AddressValidationResponse/Response/Error');
        if (count($items) > 0) {
            return $items[0]->ErrorDescription;
        } else {
            return '';
        }
    }

    public function getErrorCode() {
        $items = $this->xmlReader->xpath('/AddressValidationResponse/Response/Error');
        if (count($items) > 0) {
            return $items[0]->ErrorCode;
        } else {
            return 0;
        }
    }

    public function isAmbiguousAddress() {
        $items = $this->xmlReader->xpath('/AddressValidationResponse/AmbiguousAddressIndicator');
        if (count($items) > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function getAddressCanidates() {
        $items = $this->xmlReader->xpath('/AddressValidationResponse/AddressKeyFormat');
        if (count($items) > 0) {
            $retValue = array();
            foreach ($items as $key => $value) {
                $addressLines = $value->xpath('/AddressLine');
                $retValue[] = array(
                    'AddressLine1' => (string) $value->AddressLine[0],
                    'AddressLine2' => (string) $value->AddressLine[1],
                    'AddressLine3' => (string) $value->AddressLine[2],
                    'Region' => (string) $value->Region,
                    'PoliticalDivision2' => (string) $value->PoliticalDivision2,
                    'PoliticalDivision1' => (string) $value->PoliticalDivision1,
                    'PostcodePrimaryLow' => (string) $value->PostcodePrimaryLow,
                    'PostcodeExtendedLow' => (string) $value->PostcodeExtendedLow,
                    'CountryCode' => (string) $value->CountryCode);
            }
            return $retValue;
        }
        return array();
    }
    
    /**
     * Whether we have exact match with a given address
     * @param Shipper_Address $objAddr
     * @return boolean
     */
    public function isExactMatch( Shipper_Address $objAddr )
    {
        
        // if ( !$this->isVal() ) return false;
        $arrCandidates = $this->getAddressCanidates();
        
        foreach ( $arrCandidates as $arrProperties ) {
            $bMatch = true;
            $arrNoMatching = array();
            if ( strtolower( trim( $arrProperties['AddressLine1' ]) )
                    != strtolower( trim( $objAddr->getAddressLine1() ))) {
                $arrNoMatching[] = 'AddressLine1';
                $bMatch = false;
            }
            if ( strtolower( trim( $arrProperties['AddressLine2' ]) )
                    != strtolower( trim( $objAddr->getAddressLine2() ))) {
                $arrNoMatching[] = 'AddressLine2';
                $bMatch = false;
            }
            if ( strtolower( trim( $arrProperties['AddressLine3' ]) )
                    != strtolower( trim( $objAddr->getAddressLine3() ))) {
                $arrNoMatching[] = 'AddressLine3';
                $bMatch = false;
            }

            if ( strtolower( trim( $arrProperties['PoliticalDivision1' ]) )
                    != strtolower( trim( $objAddr->getState() )))   {
                $arrNoMatching[] = 'PoliticalDivision1';
                $bMatch = false;
            }
            if ( strtolower( trim( $arrProperties['PoliticalDivision2' ]) )
                    != strtolower( trim( $objAddr->getCity() ))) {
                $arrNoMatching[] = 'PoliticalDivision2';
                $bMatch = false;
            }
            if ( strtolower( trim( $arrProperties['CountryCode' ]) )
                    != strtolower( trim( $objAddr->getCountry() ))) {
                $arrNoMatching[] = 'CountryCode';
                $bMatch = false;
            }

            $strAddrZip = strtolower( trim( $objAddr->getZip() ));
            $strZipLow = ( strtolower( trim( $arrProperties['PostcodePrimaryLow' ] )));
            $strZipExt = ( strtolower( trim( $arrProperties['PostcodeExtendedLow' ] )));
            if ( $strZipLow != $strAddrZip && $strZipLow.'-'.$strZipExt != $strAddrZip ) {
                $arrNoMatching[] = 'Zip';
                $bMatch = false;
            }
            
            //Sys_Debug::dump( $arrProperties );
            //Sys_Debug::dump( $arrNoMatching );
            
            
            if ( $bMatch ) return true;
        }
        return false;
    }

    /**
     * @return string
     */
    public function getValidatedAddressLine1() {
        $lines = $this->xmlReader->xpath('/AddressValidationResponse/AddressKeyFormat/AddressLine');
        if (isset($lines[0]))
            return (string) $lines[0];
        return '';
    }

    /**
     * @return string
     */
    public function getValidatedAddressLine2() {
        $lines = $this->xmlReader->xpath('/AddressValidationResponse/AddressKeyFormat/AddressLine');
        if (isset($lines[1]))
            return (string) $lines[1];
        return '';
    }

    /**
     * @return string
     */
    public function getValidatedAddressLine3() {
        $lines = $this->xmlReader->xpath('/AddressValidationResponse/AddressKeyFormat/AddressLine');
        if (isset($lines[2]))
            return (string) $lines[2];
        return '';
    }
    /**
     * @return string
     */
    public function getValidatedCity() {
        $validatedAddress = $this->xmlReader->xpath('/AddressValidationResponse/AddressKeyFormat');
        $validatedAddress = $validatedAddress[0];
        return (string) $validatedAddress->PoliticalDivision2;
    }

    /**
     * @return string
     */
    public function getValidatedState() {
        $validatedAddress = $this->xmlReader->xpath('/AddressValidationResponse/AddressKeyFormat');
        $validatedAddress = $validatedAddress[0];
        return (string) $validatedAddress->PoliticalDivision1;
    }
    /**
     * @return string
     */
    public function getValidatedZip() {
        $validatedAddress = $this->xmlReader->xpath('/AddressValidationResponse/AddressKeyFormat');
        $validatedAddress = $validatedAddress[0];
        return (string) $validatedAddress->PostcodePrimaryLow;
    }

    /**
     * @return string
     */
    public function getValidatedPostalLow() {
        $validatedAddress = $this->xmlReader->xpath('/AddressValidationResponse/AddressKeyFormat');
        $validatedAddress = $validatedAddress[0];
        return (string) $validatedAddress->PostcodeExtendedLow;
    }
    /**
     * @return string
     */
    public function getValidatedCountry() {
        $validatedAddress = $this->xmlReader->xpath('/AddressValidationResponse/AddressKeyFormat');
        $validatedAddress = $validatedAddress[0];
        return (string) $validatedAddress->CountryCode;
    }

    /**
     * @return string
     */
    public function isNoCandidates() {
        $items = $this->xmlReader->xpath('/AddressValidationResponse/NoCandidatesIndicator');
        if (count($items) > 0)
            return true;
        return false;
    }

    /**
     * @return string
     */
    public function isValidAddress() {
        $items = $this->xmlReader->xpath('/AddressValidationResponse/ValidAddressIndicator');
        if (count($items) > 0)
            return true;
        return false;
    }

}