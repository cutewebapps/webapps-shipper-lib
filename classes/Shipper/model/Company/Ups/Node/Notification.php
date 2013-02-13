<?php
/*
Example
<Notification>
    <NotificationCode>7</NotificationCode>
    <EMailMessage>
        <EMailAddress>email@ups.com</EMailAddress>
        <UndeliverableEMailAddress>email@ups.com</UndeliverableEMailAddress>
    </EMailMessage>
</Notification>
*/
final class Shipper_Company_Ups_Node_Notification extends Shipper_Company_Ups_Node
{
    private $notificationCode;
    private $emailAddress;
    private $undelivEmail;
    private $memo;
    private $subjectCode;
    private $emailFrom;
    public function __construct( $in_notificationCode, $in_email, $in_memo='', 
                $in_emailFrom='', $in_subjectCode='', $in_undelivEmail='')
    {
        $this->notificationCode = $in_notificationCode;
        $this->emailAddress = $in_email;
        $this->undelivEmail = $in_undelivEmail;
        $this->memo = $in_memo;
        $this->subjectCode = $in_subjectCode;
        $this->emailFrom = $in_emailFrom;
    }
    public function toXml()
    {
        $retValue = '<Notification>'."\n";
        $retValue .= '<NotificationCode>'.$this->notificationCode.'</NotificationCode>'."\n";
        if($this->emailFrom != '' && $this->emailAddress != '')
        {
            $retValue .= '<EMailMessage>
                <EMailAddress>'.$this->emailAddress.'</EMailAddress>
                <FromName>'.$this->emailFrom.'</FromName>'."\n";
            if($this->memo != '')
                $retValue .= '<Memo>'.$this->memo.'</Memo>'."\n";
            if($this->subjectCode != '')
                $retValue .= '<SubjectCode>'.$this->subjectCode.'</SubjectCode>'."\n";
            if($this->undelivEmail != '')
                $retValue .= ' <UndeliverableEMailAddress>'.$this->undelivEmail.'</UndeliverableEMailAddress>'."\n";
            $retValue .= '</EMailMessage>'."\n";
        }
        return $retValue.'</Notification>'."\n";
    }
}