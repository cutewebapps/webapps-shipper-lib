<?php

/**
 * Message is XML document to exchange with UPS
 */
class Shipper_Company_Ups_Message 
{
    /**
     * @var string
     */
    private $xmlVersion = '1.0';
    /**
     * @var string
     */
    private $encoding = 'ut8-8';
    /**
     * @var XMLWriter
     */
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
    /**
     * 
     * @param string $in_xmlVersion
     * @return Shipper_Company_Ups_Message
     */
    public function setXmlVersion($in_xmlVersion = '1.0')
    {
        if($in_xmlVersion == '')
        {
            $this->xmlVersion = '1.0';
        }
        else
        {
            $this->xmlVersion = $in_xmlVersion;
        }
        return $this;
    }
    /**
     * @param string $in_encoding
     * @return Shipper_Company_Ups_Message
     */
    public function setXmlEncoding($in_encoding = 'utf-8')
    {
        if($in_encoding == '')
        {
            $this->encoding = '';
        }
        else
        {
            $this->encoding = $in_encoding;
        }
        return $this;
    }
    /**
     * 
     * @param string $in_xmlString
     * @throws Exception
     * @return Shipper_Company_Ups_Message
     */
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
        return $this;
    }
    /**
     * 
     * @return boolean
     */
    public function isValidResponse()
    {
        if(!is_null($this->xmlReader))
        {
            return $this->xmlReader->isValid();
        }
        return false;
    }
    /**
     * @return string
     */
    public function toXML()
    {
        $this->xmlWriter->endDocument();
        return $this->xmlWriter->outputMemory();
    }
    /**
     * get hash of current XML request
     * @return string
     */
    public function getHash()
    {
        $strOut = $this->toXML();
        return sha1( $strOut );
    }

}
