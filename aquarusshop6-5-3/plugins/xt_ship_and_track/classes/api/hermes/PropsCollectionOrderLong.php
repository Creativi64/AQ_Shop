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
 
class PropsCollectionOrderLong
{

  /**
   * 
   * @var string $moreThan2ccm
   * @access public
   */
  public $moreThan2ccm;

  /**
   * 
   * @var float $volume
   * @access public
   */
  public $volume;

  /**
   * 
   * @var HermesAddress $collectionAdress
   * @access public
   */
  public $collectionAdress;

  /**
   * 
   * @var string $timeframe
   * @access public
   */
  public $timeframe;

  /**
   * 
   * @var string $collectionType
   * @access public
   */
  public $collectionType;

  /**
   * 
   * @var int $numberOfParcels
   * @access public
   */
  public $numberOfParcels;

  /**
   * 
   * @var dateTime $collectionDate
   * @access public
   */
  public $collectionDate;

  /**
   * 
   * @param string $moreThan2ccm
   * @param float $volume
   * @param HermesAddress $collectionAdress
   * @param string $timeframe
   * @param string $collectionType
   * @param int $numberOfParcels
   * @param dateTime $collectionDate
   * @access public
   */
  public function __construct($moreThan2ccm, $volume, $collectionAdress, $timeframe, $collectionType, $numberOfParcels, $collectionDate)
  {
    $this->moreThan2ccm = $moreThan2ccm;
    $this->volume = $volume;
    $this->collectionAdress = $collectionAdress;
    $this->timeframe = $timeframe;
    $this->collectionType = $collectionType;
    $this->numberOfParcels = $numberOfParcels;
    $this->collectionDate = $collectionDate;
  }

}
