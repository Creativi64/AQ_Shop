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

class ListOfProductInfos
{

  /**
   * 
   * @var array $productInfoList
   * @access public
   */
  public $productInfoList;

  /**
   * 
   * @var dateTime $dated
   * @access public
   */
  public $dated;

  /**
   * 
   * @var string $urlHermesLogogram
   * @access public
   */
  public $urlHermesLogogram;

  /**
   * 
   * @var string $urlLiabilityInformations
   * @access public
   */
  public $urlLiabilityInformations;

  /**
   * 
   * @var string $urlPackagingGuidelines
   * @access public
   */
  public $urlPackagingGuidelines;

  /**
   * 
   * @var string $urlPortalB2C
   * @access public
   */
  public $urlPortalB2C;

  /**
   * 
   * @var string $urlTermsAndConditions
   * @access public
   */
  public $urlTermsAndConditions;

  /**
   * 
   * @var array $serviceChargeList
   * @access public
   */
  public $serviceChargeList;

  /**
   * 
   * @param array $productInfoList
   * @param dateTime $dated
   * @param string $urlHermesLogogram
   * @param string $urlLiabilityInformations
   * @param string $urlPackagingGuidelines
   * @param string $urlPortalB2C
   * @param string $urlTermsAndConditions
   * @param array $serviceChargeList
   * @access public
   */
  public function __construct($productInfoList, $dated, $urlHermesLogogram, $urlLiabilityInformations, $urlPackagingGuidelines, $urlPortalB2C, $urlTermsAndConditions, $serviceChargeList)
  {
    $this->productInfoList = $productInfoList;
    $this->dated = $dated;
    $this->urlHermesLogogram = $urlHermesLogogram;
    $this->urlLiabilityInformations = $urlLiabilityInformations;
    $this->urlPackagingGuidelines = $urlPackagingGuidelines;
    $this->urlPortalB2C = $urlPortalB2C;
    $this->urlTermsAndConditions = $urlTermsAndConditions;
    $this->serviceChargeList = $serviceChargeList;
  }

}
