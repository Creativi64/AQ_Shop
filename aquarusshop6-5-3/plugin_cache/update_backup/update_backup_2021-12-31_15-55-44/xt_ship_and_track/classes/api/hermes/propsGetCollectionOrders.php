<?php
/*
 #########################################################################
 #                       xt:Commerce Shopsoftware
 # ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 #
 # Copyright 2007-2018 xt:Commerce International Ltd. All Rights Reserved.
 # This file may not be redistributed in whole or significant part.
 # Content of this file is Protected By International Copyright Laws.
 #
 # ~~~~~~ xt:Commerce Shopsoftware IS NOT FREE SOFTWARE ~~~~~~~
 #
 # http://www.xt-commerce.com
 #
 # ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 #
 # @copyright xt:Commerce International Ltd., www.xt-commerce.com
 #
 # ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 #
 # xt:Commerce International Ltd., Kafkasou 9, Aglantzia, CY-2112 Nicosia
 #
 # office@xt-commerce.com
 #
 #########################################################################
 */

class propsGetCollectionOrders
{

  /**
   * 
   * @var dateTime $collectionDateFrom
   * @access public
   */
  public $collectionDateFrom;

  /**
   * 
   * @var dateTime $collectionDateTo
   * @access public
   */
  public $collectionDateTo;

  /**
   * 
   * @var boolean $onlyMoreThan2ccm
   * @access public
   */
  public $onlyMoreThan2ccm;

  /**
   * 
   * @param dateTime $collectionDateFrom
   * @param dateTime $collectionDateTo
   * @param boolean $onlyMoreThan2ccm
   * @access public
   */
  public function __construct($collectionDateFrom, $collectionDateTo, $onlyMoreThan2ccm)
  {
    $this->collectionDateFrom = $collectionDateFrom;
    $this->collectionDateTo = $collectionDateTo;
    $this->onlyMoreThan2ccm = $onlyMoreThan2ccm;
  }

}
