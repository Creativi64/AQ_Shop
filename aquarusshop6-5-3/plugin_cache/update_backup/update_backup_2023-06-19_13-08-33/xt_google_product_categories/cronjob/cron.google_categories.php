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

if(!class_exists('cron_google_categories'))
{
    class cron_google_categories
    {

        public function _run($params)
        {
            global $xtPlugin;

            if (isset($xtPlugin->active_modules['xt_google_product_categories']))
            {
                require_once _SRV_WEBROOT . _SRV_WEB_PLUGINS . 'xt_google_product_categories/classes/class.google_categories_import.php';
                
                try
                {
                    $import = new google_categories_import($params);
                    $r = $import->_run_cron();
                    return $r;
                } catch (Exception $e)
                {
                    return false;
                }
            }
            return true;
        }

    }
}