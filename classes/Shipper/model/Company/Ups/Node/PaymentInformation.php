<?php
/*
<PaymentInformation>
      <Prepaid>
        <BillShipper>
              <AccountNumber>Ship Number</AccountNumber>
        </BillShipper>
      </Prepaid>
</PaymentInformation>
*/
final class Shipper_Company_Ups_Node_PaymentInformation extends Shipper_Company_Ups_Node
{
    private $accountNumber;
    public function __construct($in_accountNumber)
    {
        $this->accountNumber = $in_accountNumber;
    }
    public function toXml()
    {
        return '<PaymentInformation>
        <Prepaid>
            <BillShipper>
                <AccountNumber>'.$this->accountNumber.'</AccountNumber>
            </BillShipper>
        </Prepaid>
        </PaymentInformation>';
    }
}