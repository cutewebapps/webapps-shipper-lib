<?php
final class Shipper_Company_Usps_Value_MailpieceShape extends Shipper_Company_Usps_Value{
    const CARD = 0;
    const LETTER = 1;
    const FLAT = 2;
    const PARCEL = 3;
    const LARGEPARCEL = 4;
    const IRREGULARPARCEL = 5;
    const OVERSIZEDPARCEL = 6;
    const FLATRATEENVELOPE = 7;
    const FLATRATEPADDEDENVELOPE = 8;
    const SMALLFLATRATEBOX = 9;
    const MEDIUMFLATRATEBOX = 10;
    const LARGEFLATRATEBOX = 11;
    protected $values = array(
        0 => 'Card',
        1 => 'Letter',
        2 => 'Flat',
        3 => 'Parcel',
        4 => 'LargeParcel',
        5 => 'IrregularParcel',
        6 => 'OversizedParcel',
        7 => 'FlatRateEnvelope',
        8 => 'FlatRatePaddedEnvelope',
        9 => 'SmallFlatRateBox',
        10 => 'MediumFlatRateBox',
        11 => 'LargeFlatRateBox',
    );
    public function __construct($in_value)
    {
        if ($in_value >= 0 && $in_value <= 11) {
            $this->value = $in_value;
        } else {
            throw new Shipper_Exception(
            __CLASS__ . ': Invalid parameter passed.');
        }
    }
    public function getValue()
    {
        return $this->values[$this->value];
    }
}
