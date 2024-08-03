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
 
include 'rssreader.php';

$rss = new rssreader ( true, 3600 );

$news = $rss->getNewsFeed ();
$newp = $rss->getNewPluginsFeed ( 5 );
$topp = $rss->getTopPluginsFeed ( 5 );

?>
<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css" integrity="sha384-DyZ88mC6Up2uqS4h/KRgHuoeGwBcD4Ng9SiP4dIRy0EXTlnuz47vAwmeGwVChigm" crossorigin="anonymous"/>
<link rel="stylesheet" type="text/css" href="css/admin.css" />
<link rel="stylesheet" type="text/css" href="css/icons.css" />
<style type="text/css">

.error, .warning, .success, .info {
	font-family: arial, tahoma, helvetica, sans-serif;
	border: 0 none;
	-webkit-border-radius: 5px;
	-moz-border-radius: 5px;
	border-radius: 5px;
	-webkit-box-shadow: inset 0 0 10px rgba(0, 0, 0, 0.15);
	-moz-box-shadow: inset 0 0 10px rgba(0, 0, 0, 0.15);
	box-shadow: inset 0 0 10px rgba(0, 0, 0, 0.15);
	padding: 10px;
	margin: 5px 10px 5px 10px;
}

.error:last-of-type, .warning, .success, .info {
    margin-bottom: 15px;
}

.error {
	font-family: arial, tahoma, helvetica, sans-serif;
	background: #f25d44;
	background: -moz-linear-gradient(#f5a285, #f25d44);
	background: -o-linear-gradient(#f5a285, #f25d44);
	background: -webkit-gradient(linear, 0% 0%, 0% 100%, from(#f5a285),
		to(#f25d44));
	background: -webkit-linear-gradient(#f5a285, #f25d44);
}

.success {
	font-family: arial, tahoma, helvetica, sans-serif;
	background: #bbd680;
	background: -moz-linear-gradient(#d9efa7, #bbd680);
	background: -o-linear-gradient(#d9efa7, #bbd680);
	background: -webkit-gradient(linear, 0% 0%, 0% 100%, from(#d9efa7),
		to(#bbd680));
	background: -webkit-linear-gradient(#d9efa7, #bbd680);
}

.wizard {
	width: 100%;
	background: #ccc;
}

.active {
	background: green;
	color: #FFFFFF !important;
}

.active:after {
  border-left: 30px solid green !important;
}

.notactive {
	background: #EBEBEB;
}

.wizard span {
    padding: 20px 12px 20px;
    position: relative;
    display: inline-block;
    text-decoration: none;
    min-width: 32.9%;
    margin-left: 0.25%;
    text-align: center;
    text-decoration: none;
    font-size: 14px;
    color: #707070;
    text-transform: uppercase;
}

.wizard span:first-child {
        margin-left: 0;
    }
    
    
.wizard:not(.left-arrow) span:before {
    width: 0;
    height: 0;
    border-top: 30px inset transparent;
    border-bottom: 30px inset transparent;
    border-left: 30px solid #ccc;
    position: absolute;
    content: "";
    top: 0;
    left: 0;
}

.wizard:not(.left-arrow) span:after {
    width: 0;
    height: 0;
    border-top: 30px inset transparent;
    border-bottom: 30px inset transparent;
    border-left: 30px solid #EBEBEB;
    position: absolute;
    content: "";
    top: 0;
    right: -30px;
    z-index: 2;
}
 
.wizard span:first-child:before,
.wizard span:last-child:after {
    border: none;
}

.green {
color: green;
}

.yellow {
color: yellow;
}

</style>
<!--
    update check
    !-->
<div id="infowrap">
<?php

$json_response = versionCheck ();

if (function_exists ( "ioncube_file_info" )){
	$license_info = ioncube_file_info ();
	
	if ($license_info && isset ( $license_info ["FILE_EXPIRY"] )) {
		$today = strtotime ( "now" );
		$severn_days = 86000 * 7;
		
		if (($license_info ["FILE_EXPIRY"] - $today) <= $severn_days && $license_info ["FILE_EXPIRY"] >= $today) {
			echo '<div class="error">' . sprintf ( 'Developer license is currently used in this installation. License expires:' . " %s", date ( "d M Y", $license_info ["FILE_EXPIRY"] ) ) . "</div>";
		}
	}
}

$recommended_min_version = '7.3';
$php_version = phpversion();
if(version_compare(phpversion(), $recommended_min_version, '<'))
{
    echo '<div class="error">' . sprintf ( TEXT_RECOMMENDED_PHP_VERSION, $php_version ) . '</div>';
}

if (array_value($json_response,'message')!='') {
	echo '<div class="error">Updatecheck: ' . $json_response['message'].'</div>';
} else {
    if (array_value($json_response ,'core_not_actual') == 1) {
         $target_version = $json_response['actual_version'];
        echo '<div class="error">' . sprintf ( TEXT_UPDATE_YES, _SYSTEM_VERSION, $target_version );
        if (array_value($json_response ,'update_link') != '') {
            echo ' <a href="' . $json_response ['update_link'] . '" target="_blank">Link</a>';
        }
        echo '</div>';
    } else {
        echo '<div class="success">' . sprintf ( TEXT_UPDATE_NO, _SYSTEM_VERSION ) . '</div>';
    }

    if (is_array($json_response['plugins']) && count ( $json_response['plugins'] ) > 0) {
        echo "<br>";
        foreach ( $json_response['plugins'] as $key => $val ) {
            echo '<div class="error">' . TEXT_UPDATE_PLUGIN_LOCAL_VERSION . '<br>' . $val['code'] . ':' .  ' ' . TEXT_PLUGINVERSION_AVAILABLE . ' ' . $val['version'];

            if (isset ( $val['url'] )) {
                echo '<a href="' . $val['url'] . '" target="_blank"> [Link]</a>';
            }
            if (isset ( $val['type'] ) && $val['type']=='auto') {
                echo TEXT_AUTOMATIC_UPDATE_SUPPORTED;
            }
            echo '</div>';
        }
    }
}

?>
</div>
<?php

$template = new Template();
$template->_setTemplate('__xtAdmin');
$tpl_data = array('news'=>$news,'top_plugins'=>$topp,'new_plugins'=>$newp,'store_handler'=>$store_handler);
$tpl = 'dashboard.html';
$page_data = $template->getTemplate('smarty', '/'._SRV_WEB_CORE.'pages/'.$tpl, $tpl_data);
echo $page_data;
?>