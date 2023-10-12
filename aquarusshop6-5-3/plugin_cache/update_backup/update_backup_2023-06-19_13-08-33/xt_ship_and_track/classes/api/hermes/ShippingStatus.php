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
 
class ShippingStatus
{

  /**
   * 
   * @var string $shippingId
   * @access public
   */
  public $shippingId;

  /**
   * 
   * @var string $statusText
   * @access public
   */
  public $statusText;

  /**
   * 
   * @var dateTime $statusDateTime
   * @access public
   */
  public $statusDateTime;

  /**
   * 
   * @param string $shippingId
   * @param string $statusText
   * @param dateTime $statusDateTime
   * @access public
   */
  public function __construct($shippingId, $statusText, $statusDateTime)
  {
    $this->shippingId = $shippingId;
    $this->statusText = $statusText;
    $this->statusDateTime = $statusDateTime;
  }

}
