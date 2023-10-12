<?php
/*
 #########################################################################
 #                       xt:Commerce Shopsoftware
 # ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 #
 # Copyright 2007-2018 xt:Commerce International Ltd. All Rights Reserved.
 # This file may not be redistributed in whole or significant part.
 # Content of this file is Protected By International Copyright Laws.
 #
 # ~~~~~~ xt:Commerce Shopsoftware IS NOT FREE SOFTWARE ~~~~~~~
 #
 # http://www.xt-commerce.com
 #
 # ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 #
 # @copyright xt:Commerce International Ltd., www.xt-commerce.com
 #
 # ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 #
 # xt:Commerce International Ltd., Kafkasou 9, Aglantzia, CY-2112 Nicosia
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