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
 
class ParcelFormat
{

  /**
   * 
   * @var string $parcelClass
   * @access public
   */
  public $parcelClass;

  /**
   * 
   * @var int $shortestPlusLongestEdgeCmMax
   * @access public
   */
  public $shortestPlusLongestEdgeCmMax;

  /**
   * 
   * @var int $shortestPlusLongestEdgeCmMin
   * @access public
   */
  public $shortestPlusLongestEdgeCmMin;

  /**
   * 
   * @var int $thridEdgeCmMax
   * @access public
   */
  public $thridEdgeCmMax;



  /**
   * 
   * @param string $parcelClass
   * @param int $shortestPlusLongestEdgeCmMax
   * @param int $shortestPlusLongestEdgeCmMin
   * @param int $thridEdgeCmMax
   * @param float $weightMinKg
   * @param float $weigthMaxKg
   * @access public
   */
  public function __construct($parcelClass, $shortestPlusLongestEdgeCmMax, $shortestPlusLongestEdgeCmMin, $thridEdgeCmMax)
  {
    $this->parcelClass = $parcelClass;
    $this->shortestPlusLongestEdgeCmMax = $shortestPlusLongestEdgeCmMax;
    $this->shortestPlusLongestEdgeCmMin = $shortestPlusLongestEdgeCmMin;
    $this->thridEdgeCmMax = $thridEdgeCmMax;
  }

}
