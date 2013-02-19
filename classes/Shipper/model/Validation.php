<?php


/**
 * Table for storage of all addresses with address validation
 *
 **/
class Shipper_Validation_Table extends DBx_Table
{
/**
 * database table name
 */
    protected $_name='shipper_validation';
/**
 * database table primary key
 */
    protected $_primary='shv_id';
}

/**
 * A controller that deals with currency rendering.
 **/
class Shipper_Validation_List extends DBx_Table_Rowset
{
}

/**
 * Class for filtering restrictions
 **/
class Shipper_Validation_Form_Filter extends App_Form_Filter
{
    public function createElements()
    {
        $this->allowFiltering( array( ) );
    }
}

/**
 * Class for editing restrictions
**/
class Shipper_Validation_Form_Edit extends App_Form_Edit
{
    public function createElements()
    {
        $this->allowEditing(array(  ) );
    }
}


/**
 * Row of validation response in a table
 */
class Shipper_Validation extends DBx_Table_Row
{
    /**
     * trigger for adding validation record
     */
    protected function _insert() 
    {
        if ( !isset( $this->shv_dt ) || $this->shv_dt = '0000-00-00 00:00:00') 
            $this->shv_dt = date( 'Y-m-d H:i:s');
        parent::_insert();
    }

    /**
     * Getting suggestion HTML
     * @return string
     */
    public function getSuggestionHtml() 
    {
        $arrResult = array();
        if ( $this->shv2_addr1 )   $arrResult[] = $this->shv2_addr1;
        if ( $this->shv2_addr2 )   $arrResult[] = $this->shv2_addr2;
        if ( $this->shv2_addr3 )   $arrResult[] = $this->shv2_addr3;
        if ( $this->shv2_city )    $arrResult[] = $this->shv2_city;
        if ( $this->shv2_state )   $arrResult[] = $this->shv2_state;
        if ( $this->shv2_country ) $arrResult[] = $this->shv2_country;
        if ( $this->shv2_zip )     $arrResult[] = $this->shv2_zip;
        return implode( ', ', $arrResult);
    }
  

}
