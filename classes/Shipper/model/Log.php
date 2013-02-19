<?php

/**
  * table for storage of shipper logs
  * related classes: Shipper_LogCtrl
  */
class Shipper_Log_Table extends DBx_Table
{
/**
 * database table name
 * @var string
 */
    protected $_name='shipper_log';
/**
 * database table primary key
 * @var string
 */
    protected $_primary='shrec_id';
    
    /**
     * 
     * @param string $strCompany
     * @param string $strHash
     * @param string $strAccountName
     * @return Shipper_Log
     */
    public function fetchByHash( $strCompany, $strHash, $strAccountName = 'default')
    {
        $select = $this->select()
                ->where( 'shrec_company = ?', $strCompany )
                ->where( 'shrec_request_hash = ?', $strHash )
                ->where( 'shrec_account_name = ?', $strAccountName )
                ;
        return $this->fetchRow( $select );
    }

    /**
     * @return Shipper_Log
     */
    public function add( $strCompany, $strAction, $nResult,    
                         $textRequest, $textResponse, $strOrderId = '', 
                        $strHash = '', $strAccountName = 'default' )
    {
        $tbl = new Shipper_Log_Table();
        
        $objEntry = $tbl->createRow();
        $objEntry->shrec_company = ucfirst( strtolower( $strCompany ));
        $objEntry->shrec_dt = date('Y-m-d H:i:s');
        $objEntry->shrec_action    = $strAction;
        $objEntry->shrec_order_id  = $strOrderId;
        $objEntry->shrec_result    = intval($nResult);
        $objEntry->shrec_account_name = $strAccountName;
        
        if ( $strHash == '' ) $strHash = sha1( $textRequest );
        $objEntry->shrec_request_hash = $strHash;
        $objEntry->shrec_ip     = isset( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : '';
        
        
        $strDir = App_Application::getInstance()->getConfig()->shipper->log_dir;
        if ( $strDir ) {
            if (!is_dir( $strDir )) { mkdir( $strDir ); chmod( $strDir, 0777 ); }
            $strDir .= '/'.date( 'Ymd' );
            if (!is_dir( $strDir )) { mkdir( $strDir ); chmod( $strDir, 0777 ); }
            $strDir .= '/'.strtolower( $strCompany );
            if (!is_dir( $strDir )) { mkdir( $strDir ); chmod( $strDir, 0777 ); }

            $strDt = date('YmdHis') . '_' . mt_rand(1000, 9999);
            $objEntry->shrec_request_file =  $strDt."-request.xml";

            $f = fopen( $strDir.'/'.$objEntry->shrec_request_file, 'wb');
            if ($f) {
                fwrite($f, $textRequest);
                fclose($f);
            }
            $objEntry->shrec_response_file = $strDt ."-response.xml";
            $f = fopen( $strDir.'/'.$objEntry->shrec_response_file, 'wb');
            if ($f) {
                fwrite($f, $textResponse);
                fclose($f);
            }
        }
        $objEntry->save();
        
        return $objEntry;
    }

}
/**
 * class of the rowset
 */
class Shipper_Log_List extends DBx_Table_Rowset
{
}

/**
 * class for extending form filtration
 */
class Shipper_Log_Form_Filter extends App_Form_Filter
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
class Shipper_Log_Form_Edit extends App_Form_Edit
{
    /**
     * specify elements that could be edited with standard controller
     * @return void
     */
    public function createElements()
    {
        $this->allowEditing(array(  ) );
    }
}

/**
 * class of the database row
 */
class Shipper_Log extends DBx_Table_Row
{
    /** 
      * Get class name - for php 5.2 compatibility
      * @return string 
      */
    public static function getClassName() { return 'Shipper_Log'; }
    /** 
      * Get table class object 
      * @return string 
      */
    public static function TableClass() { return self::getClassName().'_Table'; }
    /** 
      *  Get table class instance
      *  @return Shipper_Log_Table 
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
    public function getFileDir()
    {
        $strDir = App_Application::getInstance()->getConfig()->shipper->log_dir;
        $strDir .= '/'.date( 'Ymd',  strtotime( $this->shrec_dt ));
        $strDir .= '/'.strtolower( $this->getCompany() );
        return $strDir;
    }
    
    /**
     * "Ups" or "Usps"
     * @return string
     */
    public function getCompany()
    {
        return $this->shrec_company;
    }
    /**
     * @return datetime
     */
    public function getDate()
    {
        return $this->shrec_dt;
    }
    /**
     * @return string
     */
    public function getAction()
    {
        return $this->shrec_action;
    }
    /**
     * @return string
     */
    public function getOrderId()
    {
        return $this->shrec_order_id;
    }
    /**
     * @return int
     */
    public function getResult() 
    {
        return $this->shrec_result;
    }
    
    /**
     * @return string
     */
    public function getRequestFile()
    {
        if ( !empty( $this->shrec_request_file ) )
            return $this->getFileDir().'/'.$this->shrec_request_file;
        return '';
    }
    /**
     * 
     * @param string $strContents
     * @param int $nMaxLineLength
     * @return string
     */
    protected function _getHtml( $strContents, $nMaxLineLength = 80 ) 
    {
        $strReturn = '';
        if ( substr( $strContents, 0, 1) == '<' )
            $strReturn = Sys_Debug::formattedXml( $strContents );
        else {
            ob_start();
            print_r( unserialize( $strContents  ));
            $strReturn = ob_get_contents();
            ob_end_clean();
        }
        $arrLines = array();
        foreach( explode("\n", $strReturn ) as $nI => $strLine ) {
            if ( strlen( $strLine ) > $nMaxLineLength ) {
                $arrLines[] = chunk_split( $strLine, $nMaxLineLength ); 
            } else $arrLines[] = $strLine;
        }
        return htmlspecialchars( str_replace( "\n\n", '', implode( "\n", $arrLines )), ENT_QUOTES );
    }
    /**
     * @return string
     */
    public function getResponseFile()
    {
        if ( !empty( $this->shrec_request_file ) )
            return $this->getFileDir().'/'.$this->shrec_response_file;
        return '';
    }
    
    /**
     * @return string
     */
    public function getResponse()
    {
        $strFileName = $this->getResponseFile();
        if ( $strFileName && file_exists( $strFileName) ) 
            return file_get_contents( $strFileName );
        
        return '';
    }

    /**
     * get response from cache file
     * @return string
     */
    public function getRequest()
    {
        $strFileName = $this->getRequestFile();
        if ( $strFileName && file_exists( $strFileName) ) 
            return file_get_contents( $strFileName );
        return '';
    }
    
    /**
     * get response from table as 
     * @return string
     */
    public function getResponseHtml()
    {
        return $this->_getHtml( $this->getResponse () );
    }
    /**
     * 
     * @return string
     */
    public function getRequestHtml() 
    {
        return $this->_getHtml( $this->getRequest() );
    }
    

}
