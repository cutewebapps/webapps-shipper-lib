<?php
/*
<ContactInfo>
<Name>Contact Info Name</Name>
    <StructuredPhoneNumber>     
        <PhoneDialPlanNumber>111666</PhoneDialPlanNumber>
        <PhoneLineNumber>7777</PhoneLineNumber>
        <PhoneExtension>9119</PhoneExtension>
    </StructuredPhoneNumber>
</ContactInfo>
*/
final class ContactInfoNode extends Node
{
    private $name;
    private $phoneDialNimber;
    private $phoneLineNumber;
    private $phoneExtension;
    public function __construct($in_name, $in_phoneDialNumber, $in_phoneLineNumber, $in_phoneExtension)
    {
        $this->name = $in_name;
        $this->phoneDialNimber = $in_phoneDialNumber;
        $this->phoneExtension = $in_phoneExtension;
        $this->phoneLineNumber = $in_phoneLineNumber;
    }
    public function toXml()
    {
        $retValue = '<ContactInfo>';
        $retValue .= '<Name>'.$this->name.'</Name>
        <StructuredPhoneNumber>
            <PhoneDialPlanNumber>'.$this->phoneDialNimber.'</PhoneDialPlanNumber>
            <PhoneLineNumber>'.$this->phoneLineNumber.'</PhoneLineNumber>
            <PhoneExtension>'.$this->phoneExtension.'</PhoneExtension>
        </StructuredPhoneNumber>';
        return $retValue.'</ContactInfo>';
    }
}