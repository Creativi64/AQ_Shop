<?php
/*
 #########################################################################
 #                       xt:Commerce Shopsoftware
 # ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 #
 # Copyright 2021 xt:Commerce GmbH All Rights Reserved.
 # This file may not be redistributed in whole or significant part.
 # Content of this file is Protected By International Copyright Laws.
 #
 # ~~~~~~ xt:Commerce Shopsoftware IS NOT FREE SOFTWARE ~~~~~~~
 #
 # https://www.xt-commerce.com
 #
 # ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 #
 # @copyright xt:Commerce GmbH, www.xt-commerce.com
 #
 # ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 #
 # xt:Commerce GmbH, Maximilianstrasse 9, 6020 Innsbruck
 #
 # office@xt-commerce.com
 #
 #########################################################################
 */

defined('_VALID_CALL') or die('Direct Access is not allowed.');


/**
 * vat id validation for EU countries
 * validation rules: http://www.pruefziffernberechnung.de/U/USt-IdNr.shtml#PZDK
 *
 */
class vat_id extends Ibericode\Vat\Validator{
	var $vatid,$country_code;

	function __construct()
    {
	    parent::__construct();
	}

    /**
     * check european vat id
     *
     * check by algorithm for known vat id country algorithm, perform live check for unknown
     *
     * @param string $vat_id
     * @param string $country_code
     * @param bool $force_type
     * @return true/false , -99 for live check service fault
     */
	function _check($vat_id, $country_code, $force_type = false)
    {
        $result = false;

		$this->country_code = $country_code;
        $this->country_code = strtoupper($this->country_code);

		$param ='/[^a-zA-Z0-9]/';
		$vat_id=preg_replace($param,'',$vat_id);

		$this->vatid = $vat_id;

		$this->vatid = strtoupper($this->vatid);

		// add iso code to vat id
		if (substr($this->vatid, 0, 2) != $this->country_code) {
			$this->vatid = $this->country_code.$this->vatid;
		}
		 
		// always perform simple check
        $vatNumber = strtoupper( $vat_id );
        $country = substr( $vatNumber, 0, 2 );
        if($this->country_code == $country)
        {
            $switch = _STORE_VAT_CHECK_TYPE;
            if($force_type != false) $switch = $force_type;

            switch($switch){
                case 'complex':
                case 'format':
                case 'simple':
                    $result = $this->validateVatNumberFormat($vat_id);
                    break;
                case 'live':
                    try
                    {
                        $result = $this->validateVatNumber($vat_id);
                    }
                    catch(\Ibericode\Vat\Vies\ViesException $ex)
                    {
                        $result = -99;
                        error_log(__CLASS__. ' vat check failed for ['.$vat_id.']'. '  Error: '.$ex->getMessage());
                    }
                    break;
                default:
                    $result = $this->validateVatNumberFormat($vat_id);
            }
        }

        return $result;
	}
}
