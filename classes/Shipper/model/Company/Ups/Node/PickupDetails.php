<?php
/*
Example
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
            <PhoneDialPlanNumber>111666</PhoneDialPlanNumber>
            <PhoneLineNumber>7777</PhoneLineNumber>
            <PhoneExtension>9119</PhoneExtension>
        </StructuredPhoneNumber>
    </ContactInfo>
</PickupDetails>
*/

final class PickupDetailsNode extends Node
{
    private $pickupDate;
    private $earliestReady;
    private $latestReady;
    private $roomId;
    private $floorId;
    private $location;
    private $contactInfo;
    public function __construct($in_pickupDate, $in_earliestReady, $in_latestReady, $in_roomId, $in_floorId, $in_location, ContactInfoNode $in_contactInfo)
    {
        $this->pickupDate = $in_pickupDate;
        $this->earliestReady = $in_earliestReady;
        $this->latestReady = $in_latestReady;
        $this->roomId = $in_roomId;
        $this->floorId = $in_floorId;
        $this->location = $in_location;
        $this->contactInfo = $in_contactInfo;
    }
    public function toXml()
    {
        $retValue = '<PickupDetails>';
        $retValue .= '<PickupDate>'.$this->pickupDate.'</PickupDate>
        <EarliestTimeReady>'.$this->earliestReady.'</EarliestTimeReady>
        <LatestTimeReady>'.$this->latestReady.'</LatestTimeReady>
        <SuiteRoomID>'.$this->roomId.'</SuiteRoomID>
        <FloorID>'.$this->floorId.'</FloorID>
        <Location>'.$this->location.'</Location>'.
        $this->contactInfo->toXml();
        return $retValue.'</PickupDetails>';
    }
}