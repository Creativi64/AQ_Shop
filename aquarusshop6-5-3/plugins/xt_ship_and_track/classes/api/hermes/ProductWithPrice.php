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

class ProductWithPrice
{

  /**
   * 
   * @var int $netPriceEurcent
   * @access public
   */
  public $netPriceEurcent;

  /**
   * 
   * @var dateTime $validAt
   * @access public
   */
  public $validAt;

  /**
   * 
   * @var ProductInfo $productInfo
   * @access public
   */
  public $productInfo;

  /**
   * 
   * @param int $netPriceEurcent
   * @param dateTime $validAt
   * @param ProductInfo $productInfo
   * @access public
   */
  public function __construct($netPriceEurcent, $validAt, $productInfo)
  {
    $this->netPriceEurcent = $netPriceEurcent;
    $this->validAt = $validAt;
    $this->productInfo = $productInfo;
  }

}
