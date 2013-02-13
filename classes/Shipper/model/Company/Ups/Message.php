<?php

/**
 * Message is XML document to exchange with UPS
 */
class Shipper_Company_Ups_Message 
{
    
    private $xmlVersion;
    private $encoding;
    
    protected $xmlWriter;
    protected $xmlReader;
    
    public function __construct($in_xmlVersion=false, $in_encoding=false)
    {
        $this->xmlReader = null;
        $this->xmlWriter = new XMLWriter();
        $this->xmlWriter->openMemory();
        if($in_encoding === false)
        {
            $this->encoding = false;
            if($in_xmlVersion === false)
            {
                $this->xmlVersion = false;
                $this->xmlWriter->startDocument('1.0');
            }
            else
            {
                $this->xmlWriter->startDocument($in_xmlVersion);
            }
        }
        else
        {
            $this->encoding = $in_encoding;
            if($in_xmlVersion === false)
            {
                throw new Exception('Argument Exception! Message::__construct');
            }
            else
            {
                $this->xmlVersion = $in_xmlVersion;
                $this->xmlWriter->startDocument($in_xmlVersion, $in_encoding);
            }
        }
    }
    public function setXmlVersion($in_xmlVersion)
    {
        if($in_xmlVersion == '')
        {
            $this->xmlVersion = '1.0';
        }
        else
        {
            $this->xmlVersion = $in_xmlVersion;
        }
    }
    public function setXmlEncoding($in_encoding)
    {
        if($in_encoding == '')
        {
            $this->encoding = '';
        }
        else
        {
            $this->encoding = $in_encoding;
        }
    }
    public function parseXML($in_xmlString)
    {
        if(empty($in_xmlString))
        {
            throw new Exception('Argument NULL Exception! Message::parseXML');
        }
        if(is_null($this->xmlReader))
        {
            $this->xmlReader = new XMLReader();
            $this->xmlReader->XML($in_xmlString);
        }
    }
    public function isValidResponse()
    {
        if(!is_null($this->xmlReader))
        {
            return $this->xmlReader->isValid();
        }
        return false;
    }
    public function toXML()
    {
        $retValue = '';
        $this->xmlWriter->endDocument();
        $retValue = $this->xmlWriter->outputMemory();
        return $retValue;
    }
    
    public function getHash()
    {
        $strOut = $this->toXML();
        return sha1( $strOut );
    }

}
