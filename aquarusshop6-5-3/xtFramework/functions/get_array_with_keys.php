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

/**
 * Returns given array with key from subarray
 * @param unknown $inputArray
 * @param unknown $key
 */
if (!function_exists('get_array_with_keys')) {
	function get_array_with_keys($inputArray, $key) {
		$tmp = array();
		foreach ($inputArray as $array) {
			$tmp[$array[$key]] = $array;
		}
	
		return $tmp;
	}
}

if (!function_exists('get_key_value_array')) {
	function get_key_value_array($inputArray, $key, $value) {
		$tmp = array();
		foreach ($inputArray as $array) {
			$tmp[$array[$key]] = $array[$value];
		}

		return $tmp;
	}
}