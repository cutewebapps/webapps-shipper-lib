<?php

class Shipper_Company_Fedex implements Shipper_Company
{
    public function getName() 
    {
        return 'FedEx';
    }
    public function getServices() 
    {
        return array(
            'Shipping Label',
        ); 
    }  
    public function getAccountFields()  
    {
        return array(
          'shacc_key'       => 'FedEx Key',
          'shacc_pass'      => 'FedEx Password',
          'shacc_accountid' => 'FedEx Account Number',
          'shacc_meter'     => 'FedEx Meter Number',
        );      
    }
    public function getTrackingLink( $strTracking ) 
    {
        return 'http://www.fedex.com/Tracking?ascend_header=1&'
            .'clienttype=dotcom&mi=n&cntry_code=us&language=english&tracknumbers='.$strTracking;
    }
    
    public function getPackageEstimateCost( $objShipperCalc )
    {
        return 6.95;
    }
}