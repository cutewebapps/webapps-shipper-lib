<?php
class Shipper_Company_Ups_Message_AccessRequest extends Shipper_Company_Ups_Message
{
	public function setCredentials($in_userId, $in_password, $in_licenseNumber)
	{
		if(empty($in_userId) || empty($in_licenseNumber) || empty($in_password))
		{
			throw new Shipper_Exception('Argument NULL Exception! AccessRequest::setCredentials');
		}
		$this->xmlWriter->startElement('AccessRequest');
		$this->xmlWriter->writeAttribute('xml:lang', 'en-US');
		$this->xmlWriter->writeElement('AccessLicenseNumber', $in_licenseNumber);
		$this->xmlWriter->writeElement('UserId', $in_userId);
		$this->xmlWriter->writeElement('Password', $in_password);
		$this->xmlWriter->endElement();
	}
}
