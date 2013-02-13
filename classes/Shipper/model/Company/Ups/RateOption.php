<?php
/**
 * @class: Shipper_Company_Ups_RateOption
 * @author: sergey.palutin
 * @since: 19.10.2010 21:41:21 EET
 */
class Shipper_Company_Ups_RateOption extends Shipper_Company_Ups_LabelOption{
    protected $strPickup = '';

    /**
     * @param string $strPickup
     * @return void
     */
    public function setPickup($strPickup)
    {
        $this->strPickup = (string)$strPickup;
    }

    /**
     * @return string
     */
    public function getPickup()
    {
        return $this->strPickup;
    }

}
