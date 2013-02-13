<?php
/*
Example
<ShipmentServiceOptions>
    <OnCallAir>
        <Schedule> 
            <PickupDay>02</PickupDay>
            <Method>02</Method>
        </Schedule>
    </OnCallAir>
</ShipmentServiceOptions>


    <ShipmentServiceOptions>
      <OnCallAir>
        <PickupDetails>    
            <PickupDate>20071210</PickupDate>
            <EarliestTimeReady>0800</EarliestTimeReady>
            <LatestTimeReady>1800</LatestTimeReady>
            <SuiteRoomID>100</SuiteRoomID>
            <FloorID>2</FloorID>
            <Location>Location</Location>
            <ContactInfo>
                <Name>Contact Info Name</Name>
                <StructuredPhoneNumber>     
                <PhoneCountryCode></PhoneCountryCode>
                    <PhoneDialPlanNumber>111666</PhoneDialPlanNumber>
                    <PhoneLineNumber>7777</PhoneLineNumber>
                    <PhoneExtension>9119</PhoneExtension>
                </StructuredPhoneNumber>
            </ContactInfo>
        </PickupDetails>
      </OnCallAir> 
      <Notification>
        <NotificationCode>6</NotificationCode>
        <EMailMessage>
            <EMailAddress>email@ups.com</EMailAddress>
            <FromName>From Name</FromName>
            <Memo>Memo</Memo>
            <SubjectCode>03</SubjectCode>
        </EMailMessage>
      </Notification>
        <Notification>
        <NotificationCode>7</NotificationCode>
        <EMailMessage>
            <EMailAddress>email@ups.com</EMailAddress>
            <UndeliverableEMailAddress>email@ups.com</UndeliverableEMailAddress>
        </EMailMessage>
      </Notification>
      <SaturdayPickup>0</SaturdayPickup>
      <SaturdayDelivery>0</SaturdayDelivery>
    </ShipmentServiceOptions>
*/
final class Shipper_Company_Ups_Node_ShipmentServiceOption extends Shipper_Company_Ups_Node
{
    private $pickupDay;
    private $method;
    private $onCallAir;
    private $notifications;
    private $saturdayDelivery;
    private $returnService;
    public function __construct(
            $in_pickupDay='', $in_method='', 
            Shipper_Company_Ups_Node_PickupDetails $in_oncallAir = null )
    {
        $this->pickupDay = $in_pickupDay;
        $this->method = $in_method;
        $this->onCallAir = $in_oncallAir;
        $this->notifications = array();
        $this->saturdayDelivery = false;
        $this->returnService = false;
    }
    public function setEmailNotification( $strEmail, $strServiceCode = '03' ) {
        $this->notifications[] = new Shipper_Company_Ups_Node_Notification (
            '6', $strEmail, 'QVN Ship Notification', 'Service', '03');
    }
    
    public function setReturnService($in_flag)
    {
        $this->returnService = $in_flag;
    }
    public function setSaturdayDelivery()
    {
        $this->saturdayDelivery = true;
    }
    public function toXml()
    {
        if($this->returnService)
            return '';

        $retValue = '<ShipmentServiceOptions>';
        if($this->saturdayDelivery)
            $retValue .= '<SaturdayDelivery>1</SaturdayDelivery>';

        if( is_array($this->notifications))
            foreach($this->notifications as $key=>$value)
                $retValue .= $value->toXml();

        if(!is_null($this->onCallAir))
        {
            $retValue .= '<OnCallAir>'."\n"
                .$this->onCallAir->toXml().            
            '</OnCallAir>'."\n";
        }
        else
        {
            $retValue .= '<OnCallAir>
                <Schedule>
                    <PickupDay>'.$this->pickupDay.'</PickupDay>
                    <Method>'.$this->method.'</Method>
                </Schedule>
            </OnCallAir>'."\n";
        }
        return $retValue.'</ShipmentServiceOptions>'."\n";
    }
}