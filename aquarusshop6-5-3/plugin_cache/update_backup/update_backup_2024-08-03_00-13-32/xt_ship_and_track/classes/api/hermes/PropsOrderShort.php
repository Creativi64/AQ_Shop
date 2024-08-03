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

class PropsOrderShort
{

  /**
   * 
   * @var string $orderNo
   * @access public
   */
  public $orderNo;

  /**
   * 
   * @var string $shippingId
   * @access public
   */
  public $shippingId;

  /**
   * 
   * @var dateTime $creationDate
   * @access public
   */
  public $creationDate;

  /**
   * 
   * @var string $parcelClass
   * @access public
   */
  public $parcelClass;

  /**
   * 
   * @var string $status_text
   * @access public
   */
  public $status_text;

  /**
   * 
   * @var int $status
   * @access public
   */
  public $status;

  /**
   * 
   * @var string $firstname
   * @access public
   */
  public $firstname;

  /**
   * 
   * @var string $lastname
   * @access public
   */
  public $lastname;

  /**
   * 
   * @var string $postcode
   * @access public
   */
  public $postcode;

  /**
   * 
   * @var string $city
   * @access public
   */
  public $city;

  /**
   * 
   * @var string $countryCode
   * @access public
   */
  public $countryCode;

  /**
   * 
   * @var int $bulkGoodsServiceAmount
   * @access public
   */
  public $bulkGoodsServiceAmount;

  /**
   * 
   * @param string $orderNo
   * @param string $shippingId
   * @param dateTime $creationDate
   * @param string $parcelClass
   * @param string $status_text
   * @param int $status
   * @param string $firstname
   * @param string $lastname
   * @param string $postcode
   * @param string $city
   * @param string $countryCode
   * @param int $bulkGoodsServiceAmount
   * @access public
   */
  public function __construct($orderNo, $shippingId, $creationDate, $parcelClass, $status_text, $status, $firstname, $lastname, $postcode, $city, $countryCode, $bulkGoodsServiceAmount)
  {
    $this->orderNo = $orderNo;
    $this->shippingId = $shippingId;
    $this->creationDate = $creationDate;
    $this->parcelClass = $parcelClass;
    $this->status_text = $status_text;
    $this->status = $status;
    $this->firstname = $firstname;
    $this->lastname = $lastname;
    $this->postcode = $postcode;
    $this->city = $city;
    $this->countryCode = $countryCode;
    $this->bulkGoodsServiceAmount = $bulkGoodsServiceAmount;
  }

}
