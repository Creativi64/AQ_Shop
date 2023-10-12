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

defined('_VALID_CALL') or die('Direct Access is not allowed.');


class timer {
	var $timer_start, $timer_stop, $timer_total;
	
	/**
	 * Start Timer
	 *
	 */
	function _start() {
		$this->timer_start = microtime();
	}
	
	/**
	 * Stop Timer
	 *
	 * @return decimal time in ms 
	 */
	function _stop() {
		$this->timer_stop = microtime();

		$time_start = explode(' ', $this->timer_start);
		$time_end = explode(' ', $this->timer_stop);

		$this->timer_total = number_format(($time_end[1] + $time_end[0] - ($time_start[1] + $time_start[0])), 3);

		return $this->timer_total;
	}
}