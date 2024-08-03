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
 
class DeliveryDestination
{

  /**
   * 
   * @var string $exclusions
   * @access public
   */
  public $exclusions;

  /**
   * 
   * @var string $countryCode
   * @access public
   */
  public $countryCode;

  /**
   * 
     * @var float $weightMinKg
     * @access public
     */
    public $weightMinKg;

    /**
     *
     * @var float $weigthMaxKg
     * @access public
     */
    public $weigthMaxKg;

  /**
   * 
   * @param string $exclusions
   * @param string $countryCode
   * @access public
   */
  public function __construct($exclusions, $countryCode, $weightMinKg, $weigthMaxKg)
  {
    $this->exclusions = $exclusions;
    $this->countryCode = $countryCode;
    $this->weightMinKg = $weightMinKg;
    $this->weigthMaxKg = $weigthMaxKg;
  }

}
