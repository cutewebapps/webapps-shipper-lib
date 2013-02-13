<?php
/*
 * 
    static function getCountryByIsoCode($strIso)
    {
        if(!empty($strIso)) {
            return db()->getrow(
                ' SELECT iso, name, printable_name, iso3, numcode ' .
                ' FROM iso_country_codes' . 
                ' WHERE iso=' . dbv($strIso), 
                0, 
                true);
        }
        return null;
    }
 * 
 */
class Shipper_Company_Usps_CountryName {
    protected $strCountryName = '';
    
    public function __construct( $strCode ) {
        $this->strCountryName = '';
        $strCode = strtoupper( $strCode );
        $matches = array( 
            'CA' => 'Canada',
            'AU' => 'Australia',
            'IE' => 'Ireland',
            'NZ' => 'New Zealand',
            'ZA' => 'South Africa',
            'UK' => 'United Kingdom',
            'US' => 'United States',
            'USA' => 'United States',
        );
        if ( isset( $matches[ $strCode ] ) ) $this->strCountryName = $matches[ $strCode ];
       
    }
    
    public function __toString() {
        return $this->strCountryName;
    }
} 
