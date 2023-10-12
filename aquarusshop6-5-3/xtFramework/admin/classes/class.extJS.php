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


class extJS {

    protected $TMP_JS_String;
    protected $JS_String;

	function __construct () {

	}
	function _add_Method_JS_Funktion ($name, $parameters = '', $position = 0) {
		$string = '.'.$name;
		$string .= '('.$parameters.')';
		$this->_setTMP_JS_String($string, $position);
	}

	function _call_Method_JS_Function ($name, $position = 0) {
		$string = $name;
		$this->_setTMP_JS_String($string, $position);
	}
	function _setTMP_JS_String ($string, $position = 0) {
		$this->TMP_JS_String[$position] .= $string;

	}
	function _getTMP_JS_String($position = 0) {
		$string = $this->TMP_JS_String[$position];
		$this->TMP_JS_String[$position] = '';
		return $string;
	}
	function _setJS_String($string) {
		$this->JS_String .= $string;
	}
	function _getJS_String() {
		return '<script type="text/javascript" charset="utf-8">'.$this->JS_String.'</script>';
	}

	function setVar ($name, $value = '') {
		$string = '
		var '.$name;
		if ($value)
			$string .= ' = '.$value.'';
		return $string.'
		';
	}
	function setVarValue ($name, $value = '') {
		$string = '
		'.$name;
			$string .= ' = '.$value.'';
		return $string.'
		';
	}

	function WindowLocation ($location = '') {
		$string = 'window.location = "'.$location.'"';
		return $string;
	}

	function jsFunction ($name, $content, $parameters = '') {
		$string = ' function '.$name.'('.$parameters.') {
				  '.$content.' } ';
		return $string;
	}
	function parseArrayToOptions($array) {
        $string = [];
		if (is_array($array))
		foreach ($array as $key => $val)
			$string[] = $key . ':' .$val;
		return implode(', ', $string);
	}

	function parseOptionsToArray ($string) {
		$array = array();
		$data = preg_split('/[,:]/', $string);
		for ($i = 0; $i < count($data); $i=$i+2)
			$array[$data[$i]] = $data[($i+1)];
		return $array;
	}

	function mergeOptionsStrings ($string_default_options, $string_new_options) {
		return array_merge($this->parseOptionsToArray($string_default_options),$this->parseOptionsToArray($string_new_options));
	}
}
