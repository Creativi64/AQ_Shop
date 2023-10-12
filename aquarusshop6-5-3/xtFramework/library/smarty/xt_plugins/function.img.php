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

/**
 * Return image tag or path
 *
 * $param may contain the following keys:
 * - type: t|m|mi|w|p (explode by "_)
 * - img: image path
 * - plg: plugin path
 * - subdir: subdirectory path
 * - class: class="{class}"
 * - alt: alt="{alt}"
 * - title: titl="{title}"
 * - itemprop: boolean (if true add itemprop="image")
 * - width: image width
 * - height: image height
 * - path_only: boolean (if true return only path)
 * @global type $language
 * @global type $template 
 * @global type $mediaImages
 * @param array $params
 * @param type $smarty 
 * @version 1.0.1 add itemprop,title,alt
 */
function smarty_function_img($params, & $smarty) {
    return image::getImgUrlOrTag($params, $smarty);
}
