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
 
class ExceptionItem
{

  /**
   * 
   * @var int $errorCode
   * @access public
   */
  public $errorCode;

  /**
   * 
   * @var string $errorMessage
   * @access public
   */
  public $errorMessage;

  /**
   * 
   * @var string $errorType
   * @access public
   */
  public $errorType;

  /**
   * 
   * @param int $errorCode
   * @param string $errorMessage
   * @param string $errorType
   * @access public
   */
  public function __construct($errorCode, $errorMessage, $errorType)
  {
    $this->errorCode = $errorCode;
    $this->errorMessage = $errorMessage;
    $this->errorType = $errorType;
  }

}
