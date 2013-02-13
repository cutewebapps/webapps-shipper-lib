<?php
final class Shipper_Company_Usps_Value_SortType extends Shipper_Company_Usps_Value{
    const BMC = 0;
    const FIVEDIGIT = 1;
    const MIXEDBMC = 2;
    const NONPRESORTED = 3;
    const PRESORTED = 4;
    const SCF = 5;
    const SINGLEPIECE = 6;
    const THREEDIGIT = 7;
    protected $values = array(
        0 => 'BMC',
        1 => 'FiveDigit',
        2 => 'MixedBMC',
        3 => 'Nonpresorted',
        4 => 'Presorted',
        5 => 'SCF',
        6 => 'SinglePiece',
        7 => 'ThreeDigit',
    );
	public function __construct($in_value)
	{
        if ($in_value >= 0 && $in_value <= 7) {
            $this->value = $in_value;
        } else {
            throw new Shipper_Exception( __CLASS__.': Invalid parameter passed.');
        }
	}
	public function getValue()
	{
		return $this->values[$this->value];
	}
}
