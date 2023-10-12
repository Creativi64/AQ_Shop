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

if (_VALID_CALL!='true') die(' Access not Allowed ');
//include '../xtFramework/admin/main.php';

//require_once(_SRV_WEBROOT._SRV_WEB_FRAMEWORK.'admin/classes/class.ext.js.php');
//require_once(_SRV_WEBROOT._SRV_WEB_FRAMEWORK.'admin/classes/class.admin_handler.php');
 
global $language;

$admin_handler = new admin_handler();
// wird zum ausloggen aufgerufen
$admin_handler->logOutUser();
// Standard Handler f�r die Navigationen
//$admin_handler->taskClock ();

// session keep alive
$admin_handler->pinger();
$admin_handler->crontrigger();
  
$admin_handler->clickHandler2();
$admin_handler->clickHandler3();
$admin_handler->clickHandler2_ssl();
$admin_handler->clickHandlerBlank();
 
    // L�dt ein Managed IFrame in den Contenbereich
$admin_handler->addITab();
    // L�dt einen Layer in den Contenbereich

$admin_handler->addTab();
$admin_handler->Tree();

  

echo $admin_handler->_getJS_String();

$add_to_url_abs = (isset($_SESSION['admin_user']['admin_key']))? '&sec='.$_SESSION['admin_user']['admin_key']: '';

echo "
<script type=\"text/javascript\">
var csrf_key = '".$_SESSION['admin_user']['admin_key']."';
var xt_language = '".$language->content_language."'; // used ie in ext-helptreenodeui.js

(function () {
    
        if ( typeof window.CustomEvent === 'function' ) return false;

        function CustomEvent ( event, params ) {
            params = params || { bubbles: false, cancelable: false, detail: undefined };
            var evt = document.createEvent( 'CustomEvent' );
            evt.initCustomEvent( event, params.bubbles, params.cancelable, params.detail );
            return evt;
        }

        CustomEvent.prototype = window.Event.prototype;

        window.CustomEvent = CustomEvent;
    })();

function restorePasswordField(elem)
{

    $(elem).prev().prop('type', 'password');
}
function initPasswordShow()
{
    $('input[type=password]').not('.show-password').each(function(){
        $(this).addClass('show-password').after('<i class=\"show-password fas fa-eye\"></i>');      
    });
    
    $('.show-password').click(function(){

        $(this).prev('input[type=password]').prop('type', 'text');
        setTimeout(restorePasswordField, 5000 , this)
     });
}

function openCustomersDetails(customersId)
    {
        try {
            addTab('adminHandler.php?load_section=customer&pg=overview&edit_id=' + customersId, '".__define('TEXT_CUSTOMER_DETAILS').' ID '."')
        }
        catch(e) {
            console.log(e);
        }
    }

function openCustomersOrders(customersId)
    {
        try {
            addTab('adminHandler.php?load_section=order&pg=overview&c_oID=' + customersId, '".__define('HEADING_ORDER').' '.__define('TEXT_CUSTOMER').'-ID '."' + customersId)
        }
        catch(e) {
            console.log(e);
        }
    }

function openCustomersAddresses(customersId)
    {
        try {
            addTab('adminHandler.php?load_section=address&pg=overview&adID=' + customersId, '".__define('HEADING_ADDRESS').' '.__define('TEXT_CUSTOMER').'-ID '."' + customersId)
        }
        catch(e) {
            console.log(e);
        }
    }
    
function resizeIframe(obj) {
    try {
        obj.style.height = obj.contentWindow.document.body.scrollHeight + 'px';
    }
    catch(e) {
        console.log(e);
    } 
}
  
</script>
";
