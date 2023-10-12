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

// before xt 5.1  require_once _SRV_WEBROOT._SRV_WEB_FRAMEWORK.'library/htmlpurifier/HTMLPurifier.standalone.php';

class filter{

	var $_purifier = null;

	function __construct()
	{
		$config = HTMLPurifier_Config::createDefault();
		$config->set('HTML.Allowed', '');
		$config->set('Cache.SerializerPath', _SRV_WEBROOT.'templates_c');
		$this->_purifier = new HTMLPurifier($config);
	}

	function _filterXSS($var, $urldecode = true)
	{
		global $xtPlugin;
        if(empty($xtPlugin))
        {
            require_once _SRV_WEBROOT._SRV_WEB_FRAMEWORK.'classes/class.hookpoint.php';
            $xtPlugin = new hookpoint();
        }
		$new_data = array();

		if (is_array($var))
		{
			$dontUrlDecodeKeys = array(
				'password',
				'customers_password_current',
				'customers_password',
				'customers_password_confirm',
			);
			$dontFilterAtAllKeys = array();
			if(isset($var['page']) && ($var) && $var['page'] == 'search')
            {
                $dontFilterAtAllKeys = array('keywords');
            }
			($plugin_code = $xtPlugin->PluginCode('class.filter.php:dontUrlDecodeKeys')) ? eval($plugin_code) : false;
			foreach ($var as $key => $element)
			{
				if(preg_match('/amp;/', $key)>0){
					$tmp_key = $key;
					unset($var[$key]);
					$key = str_replace('amp;', '', $tmp_key);
				}
				// filter key
				$cleaned_key = $this->_pageName($key);
				if(in_array($cleaned_key, $dontFilterAtAllKeys))
				{
					$val = $element;
				}
				else
				{
				$urlDecode = !in_array($cleaned_key, $dontUrlDecodeKeys);
					$val = $this->_filterXSS($element, $urlDecode);
				}
				$new_data[$cleaned_key] = $val;
			}
			return $new_data;
		}
		elseif (!is_array($var) && !is_object($var))
		{
			if ($urldecode)
			{
				$var = rawurldecode($var); // using rawdecode instead decode to allow +
                $var =$this->_purifier->purify($var);
			}

            $var = htmlspecialchars($var, ENT_COMPAT, 'UTF-8');

			//$var = $this->_nl2br($var);

			$var = preg_replace('#(<br\s?/?>)|(<[^>]+>)#i', '\\1', $var);

			$match='!<([A-Z]\w*)(?:\s* (?:\w+) \s* = \s* (?(?=["\']) (["\'])(?:.*?\2)+ | (?:[^\s]*) ) )* \s* (\s/)? >!ix';
			$var = preg_replace($match,'<\1\5>',$var);
			$var = preg_replace('#</*(applet|meta|xml|blink|link|style|script|embed|object|iframe|frame|frameset|ilayer|layer|bgsound|title|base)[^>]*>#i',"",$var);

			// we do allow ampersands
			if(preg_match('/\&amp;/', $var)>0) {
				$var = str_replace('&amp;', '&', $var);
			}

			return $var;
		} else {
			// object
		}

	}

	function _nl2br($string)
	{
		return preg_replace("/(\r\n)+|(\n|\r)+/", "<br />", $string);
	}

	function _filter($string,$type = 'all') {

		switch ($type) {
			case 'int':
				return $this->_int($string);
				break;
			case 'char':
				return $this->_char($string);
				break;
			case 'pagename':
				return $this->_pageName($string);
				break;

			case 'lng':
				$string = $this->_char($string);
				return substr($string,0,2);
				break;
			case 'cur';
				$string = $this->_char($string);
				return substr($string,0,3);
				break;

			default:
			case 'all':
				return $this->_quote($string);
				break;
		}
	}

	function _int($var) {
		return (int)$var;
	}

	function _char($var) {
		$param ='/[^a-zA-Z]/';
		$var=preg_replace($param,'',$var);
		return $var;
	}

	/**
	 * filter everythin beside a-z A-Z 0-9 and _ -
	 *
	 * @param unknown_type $var
	 * @return unknown
	 */
	function _charNum($var) {
		$param ='/[^a-zA-Z0-9_-]/';
		$var=preg_replace($param,'',$var);
		return $var;
	}

	function _pageName($var) {
		$param ='/[^a-zA-Z0-9_-]/';
		$var=preg_replace($param,'',$var);
		return $var;
	}

	function _quote($var) {

		global $db;
		$var = stripslashes($var);
		$var = mysqli_real_escape_string($db->_connectionID, $var);
		// before xt5  $var = mysql_real_escape_string($var);
		return $var;
	}
}