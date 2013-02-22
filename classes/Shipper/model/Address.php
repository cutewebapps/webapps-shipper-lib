<?php
/**
 * Class for address abstraction - common for all shipping methods
 */
class Shipper_Address
{

    public $strAddr1 = '';
    public $strAddr2 = '';
    public $strAddr3 = '';
    
    public $strCounty = '';
    public $strCity = '';
    public $strState = '';
    public $strCountry = 'US';
    public $strZip = '';

    public function __toString()
    {
        $arrResult = array();
        if ($this->strAddr1 != '') {
            $arrResult[] = $this->strAddr1;
        }
        if ($this->strAddr2 != '') {
            $arrResult[] = $this->strAddr2;
        }
        if ($this->strAddr3 != '') {
            $arrResult[] = $this->strAddr3;
        }
        
        if ($this->strCity != '') {
            $arrResult[] = $this->strCity;
        }
        if ($this->strState != '') {
            $arrResult[] = $this->strState;
        }
        if ($this->strCountry != '') {
            $arrResult[] = $this->strCountry;
        }
        if ($this->strZip != '') {
            $arrResult[] = $this->strZip;
        }
        return implode(' ', $arrResult);
    }

    /**
     * @return string
     */
    public function getAddressLine1()
    {
        return $this->strAddr1;
    }

    /**
     * @return string
     */
    public function getAddressLine2()
    {
        return $this->strAddr2;
    }
    /**
     * @return string
     */
    public function getAddressLine3()
    {
        return $this->strAddr3;
    }

    /**
     * @return string
     */
    public function getAddressLine()
    {
        return trim($this->getAddressLine1() . ' ' . $this->getAddressLine2().' '.$this->getAddressLine3() );
    }

    /**
     * @return array of strings
     */
    public function getAddressLines()
    {
        $strLine = trim($this->getAddressLine1());
        foreach ($this->_getUnitDesignators() as $strFull => $strBrief) {
            if (preg_match('/^(.+)\W(' . $strFull . '.*)$/simU', $strLine, $arrMatch)) {
                return array($this->_smartTrim($arrMatch[1]),
                    $this->_smartTrim($arrMatch[2]));
            }
            if (preg_match('/^(.+)\W(' . $strBrief . '.*)$/simU', $strLine, $arrMatch)) {
                return array($this->_smartTrim($arrMatch[1]),
                    $this->_smartTrim($arrMatch[2]));
            }
        }
        return array($this->_smartTrim($strLine));
    }

    /**
     * 
     * @param string $strLine
     * @return string
     */
    private function _smartTrim($strLine)
    {
        return trim(rtrim(rtrim(trim($strLine), ','), '.'));
    }

    /**
     * @return string
     */
    public function getCity()
    {
        return $this->strCity;
    }

    /**
     * @return string
     */
    public function getState()
    {
        return $this->strState;
    }

    /**
     * @return string
     */
    public function getZip()
    {
        return $this->strZip;
    }

    /**
     * @return string
     */
    public function getZip5()
    {
        if ($this->isDomestic()) {
            return substr($this->strZip, 0, 5);
        }
        else
        {
            return $this->strZip;
        }
    }

    /**
     * @return string
     */
    public function getZipExt()
    {
        if (!$this->isDomestic()) {
            return '';
        }

        $nPos = strpos($this->strZip, '-');
        if ($nPos !== false) {
            return substr($this->strZip, $nPos + 1);
        } else
            return '';
    }

    /**
     * @return string
     */
    public function isDomestic()
    {
        return ($this->strCountry == 'US');
    }

    /**
     * @return string
     */
    public function isCanada()
    {
        return ($this->strCountry == 'CA');
    }

    /**
     * @return string
     */
    public function isInternational()
    {
        return (!$this->isDomestic() && !$this->isCanada());
    }

    /**
     * modified byu Sergey Palutin
     * APO/FPO AE: 09xxx
     * APO/FPO AA: 304xx
     * APO/FPO AP: 962xx - 966xx
     * @return bool
     */
    public function isMilitary()
    {
        $strZip2 = substr($this->strZip, 0, 2);
        $strZip3 = substr($this->strZip, 0, 3);
        return ($strZip3 == '340' || $strZip2 == '09' || ($strZip3 >= '962' && $strZip3 <= '966'));
    }

    /**
     * @return string
     */
    public function isContinental()
    {
        if ($this->strState == 'AK' || $this->strState == 'HI') {
            return false;
        }

        $strZip5 = $this->getZip5();
        if (($strZip5 >= 99501 && $strZip5 <= 99950) ||
                ($strZip5 >= 96701 && $strZip5 <= 96898)) {
            return false;
        }

        return true;
    }

    /**
     * @return string
     */
    public function isPoBox()
    {
        $strAddress = strtolower(trim($this->strAddr1 . ' ' . $this->strAddr2));
        $strAddress = str_replace('p.', 'p', $strAddress);
        $strAddress = str_replace('o.', 'o', $strAddress);
        if (preg_match('/\spo\s+box\s/simU', $strAddress)) {
            return true;
        }
        if (preg_match('/\spo\s+box$/simU', $strAddress)) {
            return true;
        }
        if (preg_match('/^po\s*box\s/sumU', $strAddress)) {
            return true;
        }
        if (preg_match('/\spo$/simU', $strAddress)) {
            return true;
        }
        if (preg_match('/\spo\s*\d+$/simU', $strAddress)) {
            return true;
        }
        if (preg_match('/^po\s*\d+$/simU', $strAddress)) {
            return true;
        }
        return false;
    }

    /**
     * @return string
     */
    public function isXarea($strCompany)
    {
        if ($this->isDomestic()) {
            return Shipper_Calc_Xarea::check($this->getZip(), $strCompany);
        }
        return false;
    }

    /**
     * @return string
     */
    public function setZip($strZip)
    {
        $this->strZip = $strZip;
    }

    /**
     * @return string
     */
    public function getCountry()
    {
        return $this->strCountry;
    }

    /**
     * @return string
     */
    public function setCountry($strCountry)
    {
        $str2 = substr($strCountry, 0, 2);
        if ($str2 == '') {
            $str2 = 'US';
        }
        $this->strCountry = $str2;
    }

    /**
     * @return string
     */
    public function getWhitePagesLink()
    {
        if ($this->isDomestic()) {
            return 'http://www.whitepages.com/maps/' . $this->getState();
        }
        return '';
    }

    /**
     * @return string
     */
    public function getMapQuestLink()
    {
        return 'http://www.mapquest.com/maps?city='
                . urlencode($this->getCity())
                . '&amp;state=' . urlencode($this->getState())
                . '&amp;address=' . urlencode($this->getAddressLine())
                . '&amp;zipcode=' . urlencode($this->getZip5())
                . '&amp;country=' . urlencode($this->getCountry())
                . '&amp;geocode=ZIP';
    }

    /**
     * @return string
     */
    private function _getUnitDesignators()
    {
        return array(
            'APARTMENT' => 'APT',
            'BASEMENT' => 'BSMT',
            'BUILDING' => 'BLDG',
            'DEPARTMENT' => 'DEPT',
            'FLOOR' => 'FL',
            'FRONT' => 'FRNT',
            'HANGAR' => 'HNGR',
            'LOBBY' => 'LBBY',
            'LOT' => 'LOT',
            'LOWER' => 'LOWR',
            'OFFICE' => 'OFC',
            'PENTHOUSE' => 'PH',
            'PIER' => 'PIER',
            'REAR' => 'REAR',
            'ROOM' => 'RM',
            'SIDE' => 'SIDE',
            'SLIP' => 'SLIP',
            'SPACE' => 'SPC',
            'STOP' => 'STOP',
            'SUITE' => 'STE',
            'TRAILER' => 'TRLR',
            'UNIT' => 'UNIT',
            'UPPER' => 'UPPR',
            '#' => '#',
        );
    }

    public function __construct($strZip, $strCountry = 'US', $strState = '', $strCity = '', $strAddr1 = '', $strAddr2 = '',$strAddr3 = '')
    {
        $this->setZip($strZip);
        $this->setCountry($strCountry);
        $this->strState = $strState;
        $this->strCity = $strCity;
        $this->strAddr1 = $strAddr1;
        $this->strAddr2 = $strAddr2;
        $this->strAddr3 = $strAddr3;
    }

    /**
     * checkes that main address fields are not empty
     * @return boolean
     */
    public function isValid()
    {
        if ( trim( $this->strAddr1 ) == "" || 
             trim( $this->strCity ) == "" || 
             trim( $this->strZip ) == "" || 
             trim( $this->strState ) == "" || 
             trim( $this->strCountry ) == "" ) {
            return false;
        }
        return true;
    }
    /**
     * whether ZIP is matching number of character
     * @return boolean
     */
    public function isZipValid()
    {
        if ($this->isDomestic()) {
            return preg_match( "/^\d\d\d\d\d(\-\d\d\d\d)?$/", $this->strZip );
        } else if ( $this->isCanada() ){
            return preg_match( "/^\w\w\w\s*\w\w\w$/", $this->strZip );
        }
        
        // we dont know anything on other countries
        return (trim( $this->strZip ) != "" );
    }
}
