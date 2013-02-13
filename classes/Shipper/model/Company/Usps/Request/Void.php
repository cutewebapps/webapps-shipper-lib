<?php

class Shipper_Company_Usps_Request_Void extends Shipper_Company_Usps_Request
{
/**
        $xml .= "<RefundRequest>";
        $xml .= "<AccountID>".$this->AccountID."</AccountID>";
        $xml .= "<PassPhrase>".$this->PassPhrase."</PassPhrase>";
        $xml .= $testRequest;
        $xml .= "<RefundList>";
        $xml .= "<PICNumber>".$this->PicNumber."</PICNumber>";
        $xml .= "</RefundList>";
        $xml .= "</RefundRequest>";
        
* 
* @param mixed $in_testRequest
* @param mixed $in_picNumber
* @return Shipper_Usps_Request_VoidRequest
*/
    public function __construct(
        $bTestRequest , $in_picNumber='9122148008600123456781',
        Shipper_Account $objAccount )
    {
        parent::__construct();
        
        $this->Test = $bTestRequest;
        $this->PicNumber = $in_picNumber;
        $this->Account = $objAccount;
        
    }
    public function getXmlInput() {
        $xml = '';
        $xml .= '<RefundRequest>'."\n";
        $xml .= '<AccountID>'.$this->Account->getProperty( 'Usps', 'Account ID' ).'</AccountID>'."\n";
        $xml .= '<PassPhrase>'.$this->Account->getProperty( 'Usps', 'Pass Phrase' ).'</PassPhrase>'."\n";
        $xml .= ($this->Test) ? '<Test>Y</Test>' : '';
        $xml .= '<RefundList>'."\n";
        $xml .= "\t".'<PICNumber>'.$this->PicNumber.'</PICNumber>'."\n";
        $xml .= '</RefundList>'."\n";
        $xml .= '</RefundRequest>'."\n";
        return $xml;
    }
    public function toSoapArray()
    {
    	return array('XMLInput' => $this->getXmlInput() );
    }
}