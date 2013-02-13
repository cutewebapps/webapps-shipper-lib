<?php

class Shipper_Company_Dhl implements Shipper_Company
{
    public function getName() 
    {
        return 'Dhl';
    }
    public function getServices() 
    {
        return array(
           // 'Shipping Label',
        ); 
    }
    public function getAccountFields()  
    {
        return array(
          'shacc_accountid'   => 'Account ID',
          'shacc_pass'        => 'Pass Phrase',
        );      
    }
    /*
     * we shall discover direct tracking link in future!
     */
    public function getTrackingLink( $strTracking ) 
    {
        // original link is still unknown
        return 'http://trackthepack.com/track/'.$strTracking;
    }
    
    public function getPackageEstimateCost( $objShipperCalc )
    {
        return 6.95;
    }
    
}