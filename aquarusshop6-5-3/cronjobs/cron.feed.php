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

class cron_feed {

    public function _run($params, $cron_id = false) {


        if (!isset($params['id'])) {
            return 'no id in cron parameters';
        }

        $feed_id=(int)$params['id'];

        include_once _SRV_WEBROOT.'xtFramework/classes/class.export.php';

        $export = new export($feed_id);
        $export->cronjob='internal';
        if ($export===false) return 'no feed with this id';

        // rewrite price class, rewrite currency class
        unset($customers_status);
        $customers_status = new customers_status($export->data['feed_p_customers_status']);

        if ($export->data['feed_type']=='1') {
            unset($price);
            $price = new price($customers_status->customers_status_id, $customers_status->customers_status_master,$export->data['feed_p_currency_code']);
        }
        $return=$export->_run($cron_id);
        if ($return!==true) {
            return $return;
        }

        return true;

    }

}