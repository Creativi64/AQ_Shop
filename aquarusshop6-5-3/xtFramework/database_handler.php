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

include_once _SRV_WEBROOT._SRV_WEB_FRAMEWORK.'library/vendor/adodb/adodb-php/adodb.inc.php';

include_once _SRV_WEBROOT._SRV_WEB_FRAMEWORK.'library/vendor/adodb/adodb-php/adodb-pager.inc.php';
include_once _SRV_WEBROOT._SRV_WEB_FRAMEWORK.'library/adodb-xt/xtcommerce-pager.inc.php';
include_once _SRV_WEBROOT._SRV_WEB_FRAMEWORK.'library/adodb-xt/xtcommerce-errorhandler.inc.php';


include_once _SRV_WEBROOT.'conf/debug.php';
include_once _SRV_WEBROOT.'conf/config_sessions.php';

$ADODB_CACHE_DIR = _SRV_WEBROOT._CACHE_DIR_ADODB;
$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;



// use connection before session start for plugin _pre_includes
/** @var ADOConnection $db */
$db = ADONewConnection('mysqli');
$db->setFetchMode(ADODB_FETCH_ASSOC);

$db->debug = false;
if ($db->debug===true) {
    $dbLogger=new db_logger();
}
//$db->debug = 99;
if(defined('_SYSTEM_DATABASE_HOST'))
{
$db->Connect(_SYSTEM_DATABASE_HOST, _SYSTEM_DATABASE_USER, _SYSTEM_DATABASE_PWD, _SYSTEM_DATABASE_DATABASE);
    if (_SYSTEM_SQLLOG == 'true')
    {
        $db->LogSQL();
    }
    $db->cacheSecs = _CACHETIME_ADODB;
    $db->Execute("SET NAMES "._SYSTEM_DB_CHARSET);
    $db->Execute("SET CHARACTER_SET_CLIENT="._SYSTEM_DB_CHARSET);
    $db->Execute("SET CHARACTER_SET_RESULTS="._SYSTEM_DB_CHARSET);
    $db->Execute("SET SQL_BIG_SELECTS = 1");
    $db->Execute("SET SESSION sql_mode='NO_ENGINE_SUBSTITUTION'");


    //--------------------------------------------------------------------------------------
    // Loading Config Tab
    //--------------------------------------------------------------------------------------

        _buildDefine($db, TABLE_CONFIGURATION);


    //--------------------------------------------------------------------------------------
    // Loading Plugins
    //--------------------------------------------------------------------------------------

    include _SRV_WEBROOT._SRV_WEB_FRAMEWORK.'plugin_handler.php';

    if (file_exists(_SRV_WEBROOT._SRV_WEB_FRAMEWORK.'session/'.SESSION_HANDLER.'.php')) {
        require_once _SRV_WEBROOT._SRV_WEB_FRAMEWORK.'session/'.SESSION_HANDLER.'.php';
    }

    ini_set( 'session.cookie_path', SESSION_COOKIE_PATH );
    ini_set( 'session.cookie_httponly', SESSION_COOKIE_HTTPONLY );
    ini_set( 'session.cookie_secure', SESSION_COOKIE_SECURE );
    ini_set( 'session.cookie_samesite', SESSION_COOKIE_SAMESITE );

    $SessName = 'x'.substr(md5($_SERVER['HTTP_USER_AGENT']),0,5);

    if (isset ($_POST[$SessName]))
    {
        //$_GET[$SessName] = $_POST[$SessName];
        unset ($_POST[$SessName]);
    }

    if (isset($_GET[$SessName]))
    {
        //session_id($_GET[$SessName]);
        unset($_GET[$SessName]);
    }

    global $xtPlugin;
    if(empty($agent_check)) $agent_check = new agent_check();
    $use_session = !$agent_check->isBot();

    ($plugin_code = $xtPlugin->PluginCode('database_handler.php:start_session')) ? eval($plugin_code) : false;
    if($use_session)
    {
        session_name($SessName);
        session_start();
    }
    else
    {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
        if (session_id()) session_destroy();
    }

}
