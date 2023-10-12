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

if (!$xtc_acl->isLoggedIn()) {
    header('location: login.php');
    exit;
}

	header('Content-Type: text/xml');
	$xml = file_get_contents('https://www.xt-commerce.com/blog/feed');
	$breakLine  = array("\r\n", "\n", "\r");
	$xml = str_replace($breakLine, '', $xml);
	$xml = str_replace('<content:encoded>', '<content>', $xml);
	$xml = str_replace('</content:encoded>', '</content>', $xml);
	$xml = str_replace('</dc:creator>', '</author>', $xml);
	echo str_replace('<dc:creator', '<author', $xml);
	return;
?>