<?php
class Shipper_Company_Ups_Service_Rate extends Shipper_Company_Ups_Service 
{
    const remoteAddress = 'https://wwwcie.ups.com/ups.app/xml/Rate';
    const ROOT_ELEMENT = 'RatingServiceSelectionResponse';

    /**
     * @param Shipper_Company_Ups_Message_RateRequest $in_msg
     * @return void
     */
    public function sendRateRequest(Shipper_Company_Ups_Message_RateRequest $in_msg)
    {
        $this->initChannel(self::remoteAddress);
        $test = new Shipper_Company_Ups_Message_AccessRequest('1.0');
        $test->setCredentials($this->userId, $this->password, $this->licenseNumber);
        $this->sendRequest($test->toXML().$in_msg->toXml());
//        $this->getResponseCode();
    }
    public function getCustomerContext()
    {
        $items = $this->xmlReader->xpath('/' . self::ROOT_ELEMENT . '/Response/TransactionReference/CustomerContext');
        foreach( $items as $key => $value ) return $value;
    }

    public function getResponseCode() {
        return $this->getResponseCodeInternal(self::ROOT_ELEMENT);
    }

    public function getRatedPackages()
    {
        
    }

    public function getShipmentRateValue() {
        $items = $this->xmlReader->xpath('/' . self::ROOT_ELEMENT . '/RatedShipment/TotalCharges/MonetaryValue');
        if (empty($items)) {
            return null;
        }
        return floatval($items[0]);
    }
}
