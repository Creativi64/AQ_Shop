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

function _buildDefine($db_handler, $tablename, $key = 'config_key', $value = 'config_value', $requirement = false, $input_arr = false)
{
    global $db;
    $tableExists = true; // perf // $db->GetOne("SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_SCHEMA='" . _SYSTEM_DATABASE_DATABASE . "' AND TABLE_NAME='" . $tablename . "' ");
    if(!$tableExists)
    {
        error_log('ERROR: table ['.$tablename.'] not found in _buildDefine' );
        return;
    }

    if ($requirement != '') {
        $requirement = " WHERE " . $requirement . "";
    }

    /** @var ADORecordSet $records */
    $records = false;
    $records_ar = false;
    if ($tablename == TABLE_LANGUAGE_CONTENT)
    {
        $cache_settings = [
            'lng' => $input_arr[0],
            'clz' => count($input_arr) == 2 ? $input_arr[1] : $input_arr[1].'^'.$input_arr[2]
        ];
        if(constant('_USE_CACHE_LANGUAGE_CONTENT') == true)
        {
            $records_ar = xt_cache::getCache('language_content', $cache_settings);
        }
        if ($records_ar == false)
        {
            $records = $db_handler->CacheExecute(_CACHETIME_LANGUAGE_CONTENT, "SELECT " . $key . ", " . $value . " FROM " . $tablename . " " . $requirement . " ", $input_arr);
            $records_ar = $records->GetArray();
            if(constant('_USE_CACHE_LANGUAGE_CONTENT') == true)
            {
                xt_cache::setCache($records_ar, 'language_content', $cache_settings);
            }
        }

    } else {
        $records = $db_handler->Execute("SELECT " . $key . ", " . $value . " FROM " . $tablename . " " . $requirement . " ");
        $records_ar = $records->GetArray();
    }

    if(is_object($records))
    {
        $records->Close();
    }

    foreach ($records_ar as $record) {
        if (!defined(strtoupper($record[$key]))) {
            if ($tablename == TABLE_LANGUAGE_CONTENT)  {
                $constValue = html_entity_decode(stripslashes($record[$value]), ENT_COMPAT, 'UTF-8');
            }
            else {
                $constValue = $record[$value];
            }
            /*
            if (USER_POSITION == 'admin'){
                $constValue = str_replace(array('"', '\''), array('&quot;', '&apos;'), $constValue);
            }
            */
            define(strtoupper($record[$key]), $constValue);
        }
    }

}

/**
 * damit wir ab php 8.1 keine fatals bekommen wegen evtl nicht definierter keys
 * passierte uns so mit TEXT_REMOVED_PAYMENT_CONFIG_FOR_STORE im wizard
 * da dieser key nur für admin definiert war/ist un dder wizard diesen key nicht kannte
 * (außerdem lädt der wizard ab 6.5.3 die admin keys)
 *
 * @param string $text_constant
 * @return mixed|string
 */
if(!function_exists('__text'))
{
    function __text($text_constant)
    {
        $text_constant = strtoupper($text_constant);
        if(!defined($text_constant) || empty(trim(constant($text_constant)))) return $text_constant;
        return constant($text_constant);
    }
}

