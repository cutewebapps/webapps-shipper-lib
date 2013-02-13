<?php

/*
Example:
<Request>
    <TransactionReference>
        <CustomerContext>Rating and Service</CustomerContext>
        <XpciVersion>1.0</XpciVersion>
    </TransactionReference>
    <RequestAction>Rate</RequestAction>
    <RequestOption>Rate</RequestOption>
</Request>
*/

final class Shipper_Company_Ups_Node_Request extends Shipper_Company_Ups_Node
{
    const ActionRate         = 'Rate';
    const ActionShipConfirm  = 'ShipConfirm';
    const ActionShipAccept   = 'ShipAccept';
    
    private $requestOption;
    private $context;
    private $action;
    
    public function __construct( $in_action, $in_context='', $in_requestOption='')
    {
        if ($in_context != '' && strlen($in_context) > 512) {
            $this->context = substr($in_context, 0, 512);
        } else {
            $this->context = $in_context;
        }
        $this->action = $in_action;
        if ($in_requestOption != '') {
            $this->requestOption = $in_requestOption;
        }
    }
    
    public function toXml()
    {
        $retValue = '';
        $retValue = '
<Request>
    <TransactionReference>
        <CustomerContext>'.$this->context.'</CustomerContext>
    </TransactionReference>
    <RequestAction>'.$this->action.'</RequestAction>
    <RequestOption>'.$this->requestOption.'</RequestOption>
</Request>
';
        return $retValue;
    }
}