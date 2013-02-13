<?php
class Shipper_Company_Ups_Service
{
    
	protected $userId;
	protected $licenseNumber;
	protected $password;
	private $channel;
	
	private $accessRequestXml;
	private $requestXml;
	private $responseXml;
	
	protected $xmlWriter;
	protected $xmlReader;
	
	protected $isTest;
	protected $objLabelOptions = null;
	
	public function __construct($in_userId, $in_licenseNumber, $in_password, $bIsTest = true )
	{
		if(empty($in_userId) || empty($in_licenseNumber) || empty($in_password))
		{
			throw new Exception('Argument NULL Exception! Service::__construct');
		}
		$this->userId = $in_userId;
		$this->licenseNumber = $in_licenseNumber;
		$this->password = $in_password;
		
		$this->xmlWriter = null;
		$this->xmlReader = null;
		$this->channel = null;
		$this->accessRequestXml = null;
		
		$this->requestXml = '';
		$this->responseXml = '';
		$this->isTest = $bIsTest;
	}
	public function setLabelOptions( $objLabelOptions ) 
	{
	    // $objLabelOptions should be in an instance of Shipper_Company_Ups_LabelOption
	    $this->objLabelOptions = $objLabelOptions;
	}
	
	
	private function initXmlWriter()
	{
		$this->xmlWriter = new XMLWriter();
	}
	protected function initChannel($in_remoteEndPoint)
	{
		if(empty($in_remoteEndPoint))
		{
			throw new Exception('Argument NULL Exception! Service::initChannel');
		}
		$this->channel = curl_init();
		curl_setopt($this->channel, CURLOPT_URL, $in_remoteEndPoint);
		curl_setopt($this->channel, CURLOPT_VERBOSE, 0 );
		curl_setopt($this->channel, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($this->channel, CURLOPT_SSL_VERIFYHOST, FALSE);
		curl_setopt($this->channel, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($this->channel, CURLOPT_POST, 1);
	}
	private function getAccessRequestXml()
	{
		if(is_null($this->xmlWriter))
		{
			$this->initXmlWriter();
		}
		if(is_null($this->accessRequestXml))
		{
			$this->xmlWriter->openMemory();
			$this->xmlWriter->startDocument('1.0');
			$this->xmlWriter->startElement('AccessRequest');
			$this->xmlWriter->writeAttribute('xml:lang', 'en-US');
			$this->xmlWriter->writeElement('AccessLicenseNumber', $this->licenseNumber);
			$this->xmlWriter->writeElement('UserId', $this->userId);
			$this->xmlWriter->writeElement('Password', $this->password);
			$this->xmlWriter->endElement();
			$this->xmlWriter->endDocument();
			$this->accessRequestXml = $this->xmlWriter->outputMemory();
		}
		return $this->accessRequestXml;
	}
	protected function sendRequest($in_requestXml)
	{
		if(empty($in_requestXml))
		{
			throw new Exception('Argument NULL Exception! Service::sendRequest');
		}
		$this->requestXml = $in_requestXml;
		curl_setopt($this->channel, CURLOPT_POSTFIELDS, $this->requestXml);
		$this->responseXml = curl_exec($this->channel);
		if($this->responseXml != '')
		{
            $this->xmlReader =  new SimpleXMLElement($this->responseXml);
		}
		else
		{
			throw new Shipper_Exception('Internal Exception! Service::sendRequest');
		}
	}
	protected function getResponseValue( $strService, $strPath, $strDefault = '' ) {
	    $items = $this->xmlReader->xpath('/'.$strService.$strPath);
        if ( is_array( $items ) ) 
            foreach( $items as $key => $value ) return (string)$value;
        else
            if ( $items != '' ) return (string)$items;
        return $strDefault;
	}
	
    protected function getResponseCodeInternal($service)
    {
        return  $this->getResponseValue( $service, '/Response/ResponseStatusCode');
    }
    protected function getErrorTextInternal($service)
    {
        $items = $this->xmlReader->xpath('/'.$service.'/Response/Error/ErrorDescription');
        foreach( $items as $key => $value ) return $value;
        return 'Unknown Error Occured';
    }
    public function setRequest ($in_requestXml)
    {
        $this->requestXml = $in_requestXml;
    }
    public function setResponse ($in_requestXml)
    {
        $this->responseXml = $in_requestXml;
        if ($in_requestXml != '') {
            $this->xmlReader = new SimpleXMLElement( $in_requestXml);
        } else {
            throw new Exception( 'Internal Exception! Service::setResponse');
        }
    }
	public function getRequest()
	{
		return $this->requestXml;
	}
	public function getResponse()
	{
		return $this->responseXml;
	}
	public function isTest()
	{
	    return $this->isTest;
	}

    /**
     * @return SimpleXMLElement | null
     */
    public function getXmlReader() {
        return $this->xmlReader;
    }
}