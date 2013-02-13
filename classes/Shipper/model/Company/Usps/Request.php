<?php

class Shipper_Company_Usps_Request
{
/**
* put your comment there...
* 
* @var mixed
*/
    protected $data;
   
    public function __construct()
    {
        $this->data = array();
    }
    public function __set($in_name, $in_value)
    {
        $this->data[$in_name] = $in_value;
    }
    public function __get($in_name)
    {
        if(array_key_exists($in_name, $this->data))
        {
            return $this->data[$in_name];
        }
        return null;
    }
    public function __isset($in_name)
    {
        return array_key_exists($in_name, $this->data);
    }
    public function __unset($in_name)
    {
        unset($this->data[$in_name]);
    }
    public function toXML()
    {
        $retValue = '';
        if(!empty($this->data))
        {
            foreach($this->data as $key=>$value)
            {
                if(is_array($value))
                {
                    if(!empty($value))
                    {
                        $retValue .= '<'.$key.' ';
                        foreach($value as $key_=>$value_)
                        {
                            $retValue .= $key_.'="'.$value_.'" ';
                        }
                        $retValue .= ' />';
                    }
                    else
                    {
                        $retValue .= '<'.$key.' />';
                    }
                }
                else
                {
                    $retValue .= '<'.$key.'>'.$value.'</'.$key.'>'."\n";
                }
            }
        }
        return $retValue;
    }
    public function toSoapArray()
    {
        return $this->data;
    }
    public function getHash()
    {
        #print_r($this->data);
        #Develop_Debug::dumpDie($this->data);
        return sha1( serialize( $this->data ));
    }

    /**
     * @param string $strRootElement
     * @return array
     * transferred here by Sergey Palutin
     */
    protected function makeSoapArray($strRootElement) {
        $retValue = array();
        foreach($this->data as $key=>$value)
        {
            if($value instanceof Shipper_Company_Usps_Value)
            {
                $retValue[$key] = $value->getValue();
            }
            elseif(is_array($value))
            {
                $temp = array();
                foreach($value as $key_=>$value_)
                {
                    if($value_ instanceof Shipper_Company_Usps_Value)
                    {
                        $temp[$key_] = $value_->getValue();
                    }
                    else
                    {
                        $temp[$key_] = $value_;
                    }
                }
                $retValue[$key] = $temp;
            }
            else
            {
                $retValue[$key] = $value;
            }
        }
        return array($strRootElement=>$retValue);
        
    }
}
