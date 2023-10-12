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

$display_output = false;

if(isset($page->page_action) && $page->page_action != ''){

	$callback_module = $page->page_action;
	$callback_class = 'plugins/'.$callback_module.'/callback/class.callback.php';

	if (file_exists($callback_class)) {

		include $callback_class; // won't fail, just a warning if file not exists
		$class = 'callback_'.$callback_module;

		if (class_exists($class)) // check class was loaded
        {
            $callback = new $class;
            if (method_exists($callback, 'process')) // check if class 'is' a callback with process function
            {
                global $xtPlugin;

                ($plugin_code = $xtPlugin->PluginCode('page_callback:pre_process')) ? eval($plugin_code) : false;
                $callback->process();
                ($plugin_code = $xtPlugin->PluginCode('page_callback:post_process')) ? eval($plugin_code) : false;
            }
        }

        die();
	}
}
