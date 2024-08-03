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
 
class PropsCollectionOrder
{

  /**
   * 
   * @var dateTime $collectionDate
   * @access public
   */
  public $collectionDate;

  /**
   * 
   * @var int $numberOfParcelsClass_L
   * @access public
   */
  public $numberOfParcelsClass_L;

  /**
   * 
   * @var int $numberOfParcelsClass_M
   * @access public
   */
  public $numberOfParcelsClass_M;

  /**
   * 
   * @var int $numberOfParcelsClass_S
   * @access public
   */
  public $numberOfParcelsClass_S;

  /**
   * 
   * @var int $numberOfParcelsClass_XL
   * @access public
   */
  public $numberOfParcelsClass_XL;

  /**
   * 
   * @var int $numberOfParcelsClass_XLwithBulkGoods
   * @access public
   */
  public $numberOfParcelsClass_XLwithBulkGoods;

  /**
   * 
   * @var int $numberOfParcelsClass_XS
   * @access public
   */
  public $numberOfParcelsClass_XS;

  /**
   * 
   * @param dateTime $collectionDate
   * @param int $numberOfParcelsClass_L
   * @param int $numberOfParcelsClass_M
   * @param int $numberOfParcelsClass_S
   * @param int $numberOfParcelsClass_XL
   * @param int $numberOfParcelsClass_XLwithBulkGoods
   * @param int $numberOfParcelsClass_XS
   * @access public
   */
  public function __construct($collectionDate, $numberOfParcelsClass_L, $numberOfParcelsClass_M, $numberOfParcelsClass_S, $numberOfParcelsClass_XL, $numberOfParcelsClass_XLwithBulkGoods, $numberOfParcelsClass_XS)
  {
    $this->collectionDate = $collectionDate;
    $this->numberOfParcelsClass_L = $numberOfParcelsClass_L;
    $this->numberOfParcelsClass_M = $numberOfParcelsClass_M;
    $this->numberOfParcelsClass_S = $numberOfParcelsClass_S;
    $this->numberOfParcelsClass_XL = $numberOfParcelsClass_XL;
    $this->numberOfParcelsClass_XLwithBulkGoods = $numberOfParcelsClass_XLwithBulkGoods;
    $this->numberOfParcelsClass_XS = $numberOfParcelsClass_XS;
  }

}
