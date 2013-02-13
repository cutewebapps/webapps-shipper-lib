<?php
/**
 * 
 * class for inheritance of shipper companies
 * expected some abstractions here
 */
interface Shipper_Company
{
    /**
     * @return string name of a shipping company
     */
    public function getName();    
    /**
     * @return array of avalable services string
     */
    public function getServices();
    /**
     * @return array of account properties
     */
	public function getAccountFields();    
	/**
	 * @return tracking url from stracking code
	 * @param string $strTracking
	 */
    public function getTrackingLink( $strTracking );
    /**
     * @return float estimate package cost
     * @param Shipper_Calc $objShipperCalc
     */
    public function getPackageEstimateCost( $objShipperCalc );
}