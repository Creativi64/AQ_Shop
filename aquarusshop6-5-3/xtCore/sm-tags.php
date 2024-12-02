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

global $page, $p_info, $price, $system_status, $currency, $htmlPurifier, $xtLink, $xtPlugin;

/**
 *   social media tags
 */

$shop_url_path = $xtLink->_index();
if (substr($shop_url_path, -1) != '/') $shop_url_path .= '/';


$imgParams = [
    'type' => 'm_info',
    'img' => "",
    'path_only' => true,
    'force_full_path' => true,
    'return' => true
];



$type = 'website';
$url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
$title = constant('_STORE_NAME') ?: constant('_STORE_STORE_CLAIM');
$desc = constant('_STORE_STORE_CLAIM') ?: constant('_STORE_NAME');
$images = [];
$og_properties_add = [];

// initial
$og_properties = [
    'type' => $type,
    'url' => $url,
    'title' => $title,
    'description' => $htmlPurifier->purify($desc),
    'image' => ''
];

if($page->page_name == 'index')
{

}
else if($page->page_name == 'categorie')
{
    /** @var $category category */
    $title = $category->data["categories_name"];
    $desc = $category->data["categories_description"];

    if(!empty($category->data["categories_image"]))
    {
        $imgParams["img"] = $category->data["categories_image"];
        $imgParams["type"] = "m_listingTop";
        $img_info = image::getImgUrlOrTag($imgParams);
        $images[] = [
            "name" => $category->data["categories_name"],
            "url" => $img_info,
            "thumbnail" => [
                "url" => ''
            ]
        ];
    }
}
else if($page->page_name == 'content')
{
    /** @var $shop_content_data array */
    $title = $shop_content_data["content_title"];

    if(!empty($shop_content_data["content_image"]))
    {
        $imgParams["img"] = $shop_content_data["content_image"];
        $imgParams["type"] = "m_org";
        $img_info = image::getImgUrlOrTag($imgParams);
        $images[] = [
            "name" => $shop_content_data["content_title"],
            "url" => $img_info,
            "thumbnail" => [
                "url" => ''
            ]
        ];
    }
}
else if($page->page_name == 'product')
{
    // type
    $type = 'og:product';

    // titel
    $title = $p_info->data["products_name"];

    // beschreibung
    $desc = trim(strip_tags($p_info->data["products_short_description"]));
    if(!empty($desc))
        $desc = trim($p_info->data["products_short_description"]);
    else
        $desc = trim(strip_tags($p_info->data["products_description"]));

    // bilder
    $xt_images[] = $p_info->data["products_image"];
    if (!empty($p_info->data["more_images"]))
    {
        foreach ($p_info->data["more_images"] as $v)
        {
            $xt_images[] = $v['file'];
        }
    }

    foreach ($xt_images as $v)
    {
        if ($v != 'product:noimage.gif' && !empty($v))
        {
            $imgParams["img"] = $v;
            $imgParams["type"] = "m_info";
            $img_info = image::getImgUrlOrTag($imgParams);
            $imgParams["type"] = "m_thumb";
            $img_thumb = image::getImgUrlOrTag($imgParams);
            $images[] = [
                "name" => $p_info->data["products_name"],
                "url" => $img_info,
                "thumbnail" => [
                    "url" => $img_thumb
                ]
            ];
            break; // zZ nur ein bild
        }
    }
    $og_properties_add = [
        'product:price:currency' => $currency->default_currency,
        'product:price:amount' =>  round($p_info->data["products_price"]["plain"], 2)
    ];
}

if(empty($images))
{
    $images[] = [
        "name" => constant('_STORE_STORE_CLAIM') ?: constant('_STORE_NAME'),
        "url" => $shop_url_path.'media/logo/'.constant('_STORE_LOGO'),
        "thumbnail" => [
            "url" => ''
        ]
    ];
}


// überschreiben
$og_properties = [
    'type' => $type,
    'url' => $url,
    'title' => $title,
    'description' => $htmlPurifier->purify($desc),
    'image' => count($images) ? $images[0]['url'] : ''
];

// ergänzen
$og_properties = array_merge($og_properties, $og_properties_add);

($plugin_code = $xtPlugin->PluginCode('sm-tags.php:og-tags')) ? eval($plugin_code) : false;


echo "\n";
foreach($og_properties as $prop => $val)
{
    echo "<meta property=\"og:{$prop}\" content=\"{$val}\">\n";
}

$tw_properties = [
    'card' => 'summary_large_image',
    'url' => $url,
    'title' => $title,
    'description' => $htmlPurifier->purify($desc),
    'image' => count($images) ? $images[0]['url'] : ''
];

($plugin_code = $xtPlugin->PluginCode('sm-tags.php:twitter-tags')) ? eval($plugin_code) : false;

echo "\n";
foreach($tw_properties as $prop => $val)
{
    $val = str_replace('"','""',$val);
    $val = json_encode($val, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);
    echo '<meta property="twitter:'.$prop.'" content='.$val.'>'."\n";
}
echo "\n";


