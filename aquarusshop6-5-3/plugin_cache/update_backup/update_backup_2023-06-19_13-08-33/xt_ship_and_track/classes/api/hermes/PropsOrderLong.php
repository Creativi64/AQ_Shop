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
 
class PropsOrderLong
{

  /**
   * 
   * @var string $clientReferenceNumber
   * @access public
   */
  public $clientReferenceNumber;

  /**
   * 
   * @var string $ebayNumber
   * @access public
   */
  public $ebayNumber;

  /**
   * 
   * @var HermesAddress $shipper
   * @access public
   */
  public $shipper;

  /**
   * 
   * @var int $amountCashOnDeliveryEurocent
   * @access public
   */
  public $amountCashOnDeliveryEurocent;

  /**
   * 
   * @var int $trackingFlag
   * @access public
   */
  public $trackingFlag;

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
   * @var dateTime $printDate
   * @access public
   */
  public $printDate;

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
   * @var HermesAddress $receiver
   * @access public
   */
  public $receiver;

  /**
   * 
   * @param string $clientReferenceNumber
   * @param string $ebayNumber
   * @param HermesAddress $shipper
   * @param int $amountCashOnDeliveryEurocent
   * @param int $trackingFlag
   * @param string $orderNo
   * @param string $shippingId
   * @param dateTime $printDate
   * @param string $parcelClass
   * @param string $status_text
   * @param int $status
   * @param HermesAddress $receiver
   * @access public
   */
  public function __construct($clientReferenceNumber, $ebayNumber, $shipper, $amountCashOnDeliveryEurocent, $trackingFlag, $orderNo, $shippingId, $printDate, $parcelClass, $status_text, $status, $receiver)
  {
    $this->clientReferenceNumber = $clientReferenceNumber;
    $this->ebayNumber = $ebayNumber;
    $this->shipper = $shipper;
    $this->amountCashOnDeliveryEurocent = $amountCashOnDeliveryEurocent;
    $this->trackingFlag = $trackingFlag;
    $this->orderNo = $orderNo;
    $this->shippingId = $shippingId;
    $this->printDate = $printDate;
    $this->parcelClass = $parcelClass;
    $this->status_text = $status_text;
    $this->status = $status;
    $this->receiver = $receiver;
  }

}
