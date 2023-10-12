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

global $xtMinify;

$pathes = [
    '/css/Template.css',

    '/components/eonasdan-bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min.css',

    '/components/bootstrap-select/dist/css/bootstrap-select.css',

    '/components/lightgallery/dist/css/lightgallery.min.css',

    '/components/lightgallery/dist/css/lg-transitions.min.css',

    '/components/OwlCarousel/owl-carousel/owl.carousel.min.css',

    '/components/OwlCarousel/owl-carousel/owl.transitions.min.css',

    '/components/OwlCarousel/owl-carousel/owl.theme.min.css',

    '/components/slideshow/slideshow.min.css',

    'node_modules/@sweetalert2/theme-minimal/minimal.min.css'
];

$sort_order = 0;
foreach ($pathes as $path)
{
    $web_path = file_exists(_SRV_WEBROOT . _SRV_WEB_TEMPLATES . _STORE_TEMPLATE . $path) ?
        _SRV_WEB_TEMPLATES . _STORE_TEMPLATE  . $path :
        _SRV_WEB_TEMPLATES . _SYSTEM_TEMPLATE . $path;
    $xtMinify->add_resource($web_path, $sort_order, 'header');
    $sort_order += 2;
}
?>

<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">

<link rel="preload" href="templates/xt_responsive/fonts/PT-Sans/ptsans_regular/PTS55F-webfont.woff" as="font" type="font/woff" crossorigin>
<link rel="preload" href="templates/xt_responsive/fonts/PT-Sans/ptsans_bold/PTS75F-webfont.woff" as="font" type="font/woff" crossorigin>
<link rel="preload" href="templates/xt_responsive/components/fontawesome/fonts/fontawesome-webfont.woff2?v=4.7.0" as="font" type="font/woff2" crossorigin>
<link rel="preload" href="templates/xt_responsive/components/bootstrap/dist/fonts/glyphicons-halflings-regular.woff2" as="font" type="font/woff2" crossorigin>


