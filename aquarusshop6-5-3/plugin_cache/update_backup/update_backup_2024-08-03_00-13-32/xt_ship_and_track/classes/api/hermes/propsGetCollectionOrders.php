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
