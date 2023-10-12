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

function _filterText($data, $type='full'){
    global $link_params;

    if (!isset($link_params['edit_id'])) { // not for edit page
        $_search = array("'", '"');
        $_replace = array("&acute;", '&quot;');
        $data = str_replace($_search,$_replace, $data);
    }
		if($type=='full'){
			$data = addslashes($data);
		}

		$data = preg_replace("/\n/","\\n",$data);
		$data = preg_replace("/\r/","\\r",$data);
		$data = preg_replace("/\t/","\\t",$data);

		// f?r den admin, falls ein user script tags verwendet: (ak/mb)
		$data = preg_replace ('/<\/script>/i', '<\\/script>', $data);

	return $data;
}

function remove_utf8_bom($text)
{
    $bom = pack('H*','EFBBBF');
    $text = preg_replace("/^$bom/", '', $text);
    return $text;
}
