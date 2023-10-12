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


class chart_layout {

    function _getOrders($mamount, $mshare, $yamount, $yshare, $aamount, $ashare){
        $html = '<table cellspacing="20px" width="100%"><tr><td width="50%">'
			.$mamount.
			'</td><td width="50%">'
            .$mshare.
            '</td></tr><tr><td>'
            .$yamount.
            '</td><td>'
            .$yshare.
            '</td></tr><tr><td>'
            .$aamount.
            '</td><td>'
            .$ashare.
            '</td></tr></table>';
        return $html;
    }

    function _getCustomers($month, $mshare, $year, $yshare, $all, $ashare){
        $html = '<table cellspacing="20px" width="100%"><tr><td width="50%">'
			.$month.
			'</td><td width="50%">'
            .$mshare.
            '</td></tr><tr><td>'
            .$year.
            '</td><td>'
            .$yshare.
            '</td></tr><tr><td>'
            .$all.
            '</td><td>'
            .$ashare.
            '</td></tr></table>';
        return $html;
    }

}
?>