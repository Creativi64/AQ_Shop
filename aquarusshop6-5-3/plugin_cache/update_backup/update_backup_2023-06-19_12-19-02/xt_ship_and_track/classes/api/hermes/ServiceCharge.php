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

class ServiceCharge
{

  /**
   * 
   * @var string $displayname
   * @access public
   */
  public $displayname;

  /**
   * 
   * @var int $amountEurocent
   * @access public
   */
  public $amountEurocent;

  /**
   * 
   * @var string $currency
   * @access public
   */
  public $currency;

  /**
   * 
   * @param string $displayname
   * @param int $amountEurocent
   * @param string $currency
   * @access public
   */
  public function __construct($displayname, $amountEurocent, $currency)
  {
    $this->displayname = $displayname;
    $this->amountEurocent = $amountEurocent;
    $this->currency = $currency;
  }

}
