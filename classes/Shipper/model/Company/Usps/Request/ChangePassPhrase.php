<?php

class Shipper_Company_Usps_Request_ChangePassPhrase extends Shipper_Company_Usps_Request
{
    public function __construct($strNewPassPhrase)
    {
        if(!empty($strNewPassPhrase)) {
            $this->NewPassPhrase = $strNewPassPhrase;
        } else {
            throw new Shipper_Exception('Argument empty exception! $strNewPassPhrase is empty.');
        }
    }
}