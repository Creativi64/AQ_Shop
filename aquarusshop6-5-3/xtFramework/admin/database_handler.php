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

function xt_adodb_mysqli_factory($driver)
{
    if ($driver !== 'mysqli') return false;

    return new xt_adodb_mysqli($driver);
}

global $ADODB_THROW_EXCEPTIONS, $ADODB_NEWCONNECTION;
$ADODB_THROW_EXCEPTIONS = false;
$ADODB_NEWCONNECTION = 'xt_adodb_mysqli_factory';

include _SRV_WEBROOT._SRV_WEB_FRAMEWORK.'library/adodb-xt/xtcommerce-errorhandler.inc.php';
//include _SRV_WEBROOT._SRV_WEB_FRAMEWORK.'library/adodb-xt/adodb-session2-xt.php';

include_once _SRV_WEBROOT._SRV_WEB_FRAMEWORK.'library/vendor/adodb/adodb-php/adodb-pager.inc.php';
include _SRV_WEBROOT._SRV_WEB_FRAMEWORK.'library/adodb-xt/xtcommerce-pager.inc.php';

include _SRV_WEBROOT._SRV_WEB_FRAMEWORK.'library/vendor/adodb/adodb-php/adodb-active-record.inc.php';

include _SRV_WEBROOT.'conf/debug.php';
include _SRV_WEBROOT.'conf/config_sessions.php';

$ADODB_CACHE_DIR = _SRV_WEBROOT._CACHE_DIR_ADODB;
$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;

$session_name = substr(md5($_SERVER['HTTP_USER_AGENT']),0,5);

if (isset($_GET['sess_name']) && isset($_GET['sess_id'])) {   // TODO add security token for auth
	$param ='/[^a-zA-Z0-9_-]/';
	$session_name=preg_replace($param,'',$_GET['sess_name']);
	$session_name = substr($session_name,2,5);
	session_id($_GET['sess_id']);
}

if (file_exists(_SRV_WEBROOT._SRV_WEB_FRAMEWORK.'session/'.SESSION_HANDLER.'.php')) {
    require_once _SRV_WEBROOT._SRV_WEB_FRAMEWORK.'session/'.SESSION_HANDLER.'.php';
}

session_name('ax'.$session_name);
session_start();


$db = ADONewConnection('mysqli');
$db->Connect(_SYSTEM_DATABASE_HOST, _SYSTEM_DATABASE_USER, _SYSTEM_DATABASE_PWD, _SYSTEM_DATABASE_DATABASE);
$db->setFetchMode(ADODB_FETCH_ASSOC);
if (_SYSTEM_SQLLOG=='true') $db->LogSQL();
$db->cacheSecs = 0; // cache 24 hours 3600*24
$db->Execute("SET NAMES '"._SYSTEM_DB_CHARSET."'");
$db->Execute("SET CHARACTER_SET_CLIENT="._SYSTEM_DB_CHARSET);
$db->Execute("SET CHARACTER_SET_RESULTS="._SYSTEM_DB_CHARSET);
$db->Execute("SET SQL_BIG_SELECTS = 1");
$db->Execute("SET SESSION sql_mode='NO_ENGINE_SUBSTITUTION'");

ADOdb_Active_Record::SetDatabaseAdapter($db);
