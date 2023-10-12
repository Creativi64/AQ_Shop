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

class WizardException extends Exception {
	
	// Define error levels
	const ERROR_LEVEL_DEBUG 	= 1;
	const ERROR_LEVEL_WARNING 	= 2;
	const ERROR_LEVEL_FATAL 	= 3;
	
	/**
	 * Current error level
	 * @var null|int
	 */
	protected $_errorLevel = null;
	
	/**
	 * Class constructor
	 * @param string $message
	 * @param string $code
	 */
	public function __construct ($message = null, $code = null) {
		$this->_errorLevel = $code;
		parent::__construct($message);
	}
	
	/**
	 * Returns error level
	 * @return Ambigous <NULL, number>
	 */
	public function getErrorLevel() {
		return $this->_errorLevel;
	}
}