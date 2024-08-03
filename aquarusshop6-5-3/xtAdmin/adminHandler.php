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

include_once '../xtFramework/admin/main.php';

global $xtc_acl, $xtPlugin, $logHandler;

if (!$xtc_acl->isLoggedIn()) {
    die('login required');
}

if (CSRF_PROTECTION!='false')
{
    $xtc_acl->checkAdminKey();
}


if( isset($_GET['load_section']) && (!isset($_GET['parentNode']))   ){

    foreach($_SESSION as $key =>$value){
        if(str_starts_with($key, "filter")){ //field name starts with 'filter'
            unset($_SESSION[$key]);
        }
    }
}


if(array_value($_REQUEST,'filter_form')){

    // new style from xt5
    $filter_key = 'filters_'.$_REQUEST['filter'];

    unset($_SESSION[$filter_key]);

    foreach ($_REQUEST as $key => $value)
    {
        if ( ($_REQUEST['doReset']==0 && strpos($key, "filter_") === 0) || $key == 'filter')
        {
            $_SESSION[$filter_key][$key] = $value;
        }
    }

    if(isset($_SESSION[$filter_key]['filter']))
        Echo '({"success":true})'; else Echo '({"success":false})';
    exit();
}


global $adminHandler_expected_content_type;
$adminHandler_expected_content_type = 'json';

include_once (_SRV_WEBROOT_ADMIN.'page_includes.php');
PhpExt_Javascript::sendContentType();

$link_params = array();

if($_GET)
    $link_params = array_merge($link_params, $_GET);

if($_POST)
    $link_params = array_merge($link_params, $_POST);


unset($_POST['start']);
unset($_POST['limit']);


if (isset($_GET['load_section'])) {
    $section = $_GET['load_section'];
    $param ='/[^a-zA-Z0-9_-]/';
    $section=preg_replace($param,'',$section);
}
elseif (isset($_POST['load_section'])) {
    $section = $_POST['load_section'];
    $param ='/[^a-zA-Z0-9_-]/';
    $section=preg_replace($param,'',$section);
} elseif (isset($_GET['keepalive']) && $_GET['keepalive'] =='true') {

    ($plugin_code = $xtPlugin->PluginCode('adminHandler.php:pinger')) ? eval($plugin_code) : false;

    // notification popup
    $objReturn = $logHandler->getLastPopupNotification();
    if (is_object($objReturn) && $objReturn->success == false) {

        header('Content-Type: application/json; charset='._SYSTEM_CHARSET);
        echo json_encode($objReturn);
        die;
    }

    $obj = new stdClass();
    $obj->success = true;
    header('Content-Type: application/json; charset='._SYSTEM_CHARSET);
    echo json_encode($obj);
    die;

}
else {
    $obj = new stdClass();
    $obj->error = "404";

    header('Content-Type: application/json; charset='._SYSTEM_CHARSET);
    echo json_encode($obj);

    die;
}

$form_grid = new ExtAdminHandler($section, $link_params);

reset($_REQUEST);
foreach($_REQUEST as $get_key => $get_val){
    if(preg_match('/multiFlag_/', $get_key)){

        $tmp_get_data = explode('_', $get_key);

        $obj = $form_grid->multiClassData($link_params['m_ids'], $tmp_get_data[1], '_'.$tmp_get_data[1], $get_val);

        header('Content-Type: application/json; charset='._SYSTEM_CHARSET);
        echo json_encode($obj);
        die;
    }

    ($plugin_code = $xtPlugin->PluginCode('adminHandler.php:_multiClass')) ? eval($plugin_code) : false;
}


if ((isset($_REQUEST['get_selected']) or isset($_REQUEST['get_select_flag'])) && ($link_params['new_data'] == '' && $link_params['edit_data'] == '')) {
    $obj = $form_grid->multiClassData($link_params['m_ids'], 'select', '_set');


    header('Content-Type: application/json; charset='._SYSTEM_CHARSET);
    echo json_encode($obj);
    die;
}

if (isset($_GET['new'])) {
    $form_grid->setClassNew();
}

if (isset($_GET['get_data']) || isset($_POST['get_data'])) {
    $obj = $form_grid->getClassData();
    //header('Content-Type: text/html; charset=iso-8859-1');
    header('Content-Type: application/json; charset='._SYSTEM_CHARSET);
    echo json_encode($obj);
    die;
}
if (isset($_GET['get_singledata'])) {
    $obj = $form_grid->getSingleData($_GET['get_singledata']);
    header('Content-Type: application/json; charset='._SYSTEM_CHARSET);
    echo json_encode($obj);
    die;
}
// set edit id
if (isset($_GET['edit_id'])) {
    $form_grid->setEdit($_GET['edit_id']);
}
if (isset($_GET['settings'])) {
    $adminHandler_expected_content_type = 'html';
    try
    {
        echo $form_grid->getSettings(); // ? was/wofür ist das
    }
    catch (Exception $e)
    {
        error_log($e->getMessage().PHP_EOL.$e->getTraceAsString());
        echo '<script type="text/javascript" charset="utf-8">alert("Exception: '.$e->getMessage().'")</script>';
    }
    die;
}
if (isset($_GET['getimages'])) {
    $obj = $form_grid->getImages();
    header('Content-Type: application/json; charset='._SYSTEM_CHARSET);
    echo json_encode($obj);
    die;
}
if (isset($_GET['getfiles'])) {
    $obj = $form_grid->getFiles();
    header('Content-Type: application/json; charset='._SYSTEM_CHARSET);
    echo json_encode($obj);
    die;
}
if (isset($_GET['upload'])) {
    $obj = $form_grid->Upload();
    header('Content-Type: application/json; charset='._SYSTEM_CHARSET);
    echo json_encode($obj);
    die;
}
// save data
if ($_POST && isset($_GET['save'])) {
    $obj = $form_grid->saveClassData($_POST);
    header('Content-Type: application/json; charset='._SYSTEM_CHARSET);
    echo json_encode($obj);
    //Echo '({"data":'.json_encode($_POST).',"success": true, "waitMsg": "SAVED.."})';
    die;
}
if ($link_params && (isset($_GET['save_all'])) || array_value($link_params,'new_data', '') != '' || array_value($link_params, 'edit_data', '') != '') {
    $obj = $form_grid->saveClassAllData($link_params);
    header('Content-Type: application/json; charset='._SYSTEM_CHARSET);
    echo json_encode($obj);
    die;
}


if (isset($_REQUEST['plugin']) &&  $_REQUEST['plugin'] == 'order_edit')
{
    $form_grid = new order_edit_ExtAdminHandler($form_grid);

    // zusätzliches js erzeugen
    if ($form_grid->code == 'order_edit_products' && $_REQUEST['pg'] !== 'removeOrderItem' && $_REQUEST['pg'] !== 'updateOrderItem')
    {
        //order_edit_controller::hook_adminHandler_bottom($form_grid);
    }
    elseif ($form_grid->code === 'order_edit_add_products' && $_REQUEST['pg'] !== 'addOrderItem' && $_REQUEST['pg'] !== 'calculateGraduatedPrice')
    {
        //order_edit_controller::hook_adminHandler_bottom($form_grid);
    }
}

if (in_array($form_grid->code, array('order_edit_add_products', 'order_edit_products'), TRUE)
    && ! in_array($_REQUEST['pg'], array('addOrderItem', 'updateOrderItem', 'removeOrderItem', 'calculateGraduatedPrice'), TRUE)
)
{
    echo("<script type='text/javascript'>\n orders_id = ". $_REQUEST['orders_id'].";\n" .$js. ";</script>");
}

($plugin_code = $xtPlugin->PluginCode('adminHandler.php:_bottom')) ? eval($plugin_code) : false;

// display javascript

$adminHandler_expected_content_type = 'html';
try
{
    echo $form_grid->display();
}
catch (Exception $e)
{
    error_log($e->getMessage().PHP_EOL.$e->getTraceAsString());
    echo '<script type="text/javascript" charset="utf-8">alert("Exception: '.$e->getMessage().'")</script>';
}
