<?php

/**
  * table for storage of ...
  * related classes: Shipper_AccountCtrl
  */
class Shipper_Account_Table extends DBx_Table
{
/**
 * database table name
 * @var string
 */
    protected $_name='shipper_account';
/**
 * database table primary key
 * @var string
 */
    protected $_primary='shacc_id';
    
    /**
     * @retrun Shipper_Account
     */
    public function fromConfig( $strName )
    {
        $objConfig = App_Application::getInstance()->getConfig()->shipper;
        
        foreach( $objConfig->account as $strKey => $objShipperAccount ) {
            if ( $strKey == $strName )  {
                $objAccount = Shipper_Account::Table()->createRow();
                
                $arrProps = array( 'name', 'company',
                    'requesterid' , 'accountid' , 'pass', 
                    'partnerid', 'licence', 'key', 'meter', 'test_mode', 'insurance' );
                foreach ( $arrProps as $strProperty ) {
                    $strField = 'shacc_' . $strProperty; 
                    $objAccount->$strField = $objShipperAccount->$strProperty;
                }
                // no need to save a record
                return  $objAccount;
            }
        }
        return null;
    }
}
/**
 * class of the rowset
 */
class Shipper_Account_List extends DBx_Table_Rowset
{
}

/**
 * class for extending form filtration
 */
class Shipper_Account_Form_Filter extends App_Form_Filter
{
    /**
     * specify elements that could be filtered with standard controller
     * @return void
     */
    public function createElements()
    {
        $this->allowFiltering( array( ) );
    }
}

/**
 * class for extending editing procedures
 */
class Shipper_Account_Form_Edit extends App_Form_Edit
{
    /**
     * specify elements that could be edited with standard controller
     * @return void
     */
    public function createElements()
    {
        $this->allowEditing(array(  'shacc_name', 'shacc_company', 'shacc_enabled', 'shacc_is_default', 
            'shacc_test_mode', 'shacc_insurance', 'shacc_requesterid', 'shacc_accountid', 'shacc_pass', 
            'shacc_partnerid', 'shacc_licence', 'shacc_key', 'shacc_meter', 'shacc_notification' ) );
    }
}

/**
 * class of the database row
 */
class Shipper_Account extends DBx_Table_Row
{
    /** 
      * Get class name - for php 5.2 compatibility
      * @return string 
      */
    public static function getClassName() { return 'Shipper_Account'; }
    /** 
      * Get table class object 
      * @return string 
      */
    public static function TableClass() { return self::getClassName().'_Table'; }
    /** 
      *  Get table class instance
      *  @return Shipper_Account_Table 
      */
    public static function Table() { $strClass = self::TableClass();  return new $strClass; }
    /** 
      * get table name 
      * @return string 
      */
    public static function TableName() { return self::Table()->getTableName(); }
    /** 
      * get class name for the specified form ("Filter" or "Edit")
      * @return string 
      */
    public static function FormClass( $name ) { return self::getClassName().'_Form_'.$name; }
    /** 
      * get class instance for the specified form ("Filter" or "Edit")
      *  @return mixed 
      */
    public static function Form( $name ) { $strClass = self::getClassName().'_Form_'.$name; return new $strClass; }
    
    /**
     * @return string
     */
    public function getName()
    {
        return $this->shacc_name;
    }
    /**
     * @return string
     */
    public function getShippingCompany()
    {
        return $this->shacc_company;
    }
    
    public function getNotificationEmails()
    {
        $strNotification = str_replace( ',', ';', $this->shacc_notification);
        
        $arrNotification = explode( ';', $strNotification );
        $arrResult = array();
        foreach( $arrNotification as $strEmail ) if ( trim( $strEmail ) != '' )
            $arrResult [] = trim( $strEmail );
        return $arrResult;
    }
    public function setShippingCompany( $strCompany )
    {
        $this->shacc_company = $strCompany;
    } 
    /**
     * 
     * @param array of string or string $mails
     */
    public function setNotificationEmails( $mails )
    {
        if ( is_array( $mails )) $this->shacc_notification = implode( ';', $mails );
        else $this->shacc_notification = $mails;
    }
    
    /**
     * whether account is int test mode
     * @return boolean 
     */
    public function isTestMode()
    {
        return intval( $this->shacc_test_mode );
    }
    /**
     * get Class
     * @return string
     */
    public function getCompanyClassName()
    {
        $strCompany =  strtolower( $this->getShippingCompany() );
        switch( $strCompany ) {
            case 'ups':
            case 'usps':
            case 'fedex':
            case 'dhl':
                return  'Shipper_Company_'.ucfirst( $strCompany );
        }
        // otherwise we don't know what class is assigned
        return '';
    }
    /**
     * whether shipper account record is valid
     * @return boolean 
     */
    public function isValid() 
    {
        if ( !$this->getShippingCompany() ) {
            return false;
        }
        
        $strCompanyClass = $this->getCompanyClassName();
        if ( !$strCompanyClass ) {
            return false;
        }
        
        $objCompany = new $strCompanyClass;
        $arrFields = $objCompany->getAccountFields();
        foreach ($arrFields as $strDbField => $strProperty) {
            if ( !isset( $this->$strDbField ) || empty( $this->$strDbField ) ) {
                return false;
            }
        }
        return true;
    }
    
    /**
     * @return property value in dependence of shipping company 
     * @param string $strCompany
     * @param string $strPropertyName
     * @throws Shipper_Exception
     */
    public function getProperty( $strCompany, $strPropertyName )
    {
        $strCompanyClass = $this->getCompanyClassName($strCompany);
        $objCompany = new $strCompanyClass;
        $arrFields = $objCompany->getAccountFields();
        foreach ($arrFields as $strDbField => $strProperty) {
            if (strtolower($strProperty) == strtolower($strPropertyName)) {
                return $this->$strDbField;
            }
        }
        throw new Shipper_Exception( 'Property '.$strPropertyName.' is not specified for '
            . $strCompany.' shipping company' );
        return false;
    }
    /**
     * sets property value in dependence of shipping company 
     * @param string $strCompany - shipper company name
     * @param string $strPropertyName - property name
     * @param string $strValue - new value
     * @throws Shipper_Exception
     */   
    public function setProperty( $strCompany, $strPropertyName, $strValue )
    {
        $strCompanyClass = $this->getCompanyClassName($strCompany);
        $objCompany = new $strCompanyClass;
        $arrFields = $objCompany->getAccountFields();
        foreach ($arrFields as $strDbField => $strProperty) {
            if (strtolower($strProperty) == strtolower($strPropertyName)) {
                $this->$strDbField = $strValue;
                return true;
            }
        }
        throw new Shipper_Exception( 'Property '.$strPropertyName.' is not specified for '
            . $strCompany.' shipping company' );
        return false;  
    }
    
    protected function _removeDefaults( $strCompany, $strException )
    {
        $this->getTable()->update( array(
                'shacc_is_default' => 0
        ), 'shacc_company=\''.$this->shacc_company.'\' AND shacc_id<>'.$this->shacc_id );
    }
    
    protected function _update()
    {
        if ( ! $this->shacc_enabled ) $this->shacc_is_default = 0;
        parent::_update();
    }
    
    protected function _postUpdate()
    {
        if ( $this->shacc_is_default && $this->shacc_enabled ) {
            // recalculate default for the shipping company
            $this->_removeDefaults( $this->shacc_company, $this->shacc_id );
        }
        parent::_postUpdate();
    }
    
    protected function _insert()
    {
        if ( ! $this->shacc_enabled ) $this->shacc_is_default = 0;
        parent::_insert();
    }
    
    protected function _postInsert()
    {
        parent::_postInsert();
        if ( $this->shacc_is_default && $this->shacc_enabled ) {
            $this->_removeDefaults( $this->shacc_company, $this->shacc_id );
        }
    }
}
