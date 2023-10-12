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

global $page, $brotkrumen, $xtPlugin, $db;


// breadcrump ld immer, auch auf startseite
$breadcrump_ld = [
    "@context" => "https://schema.org/",
    "@type" => "BreadcrumbList",
    "name" => "Breadcrump navigation",
    "itemListElement" => []
];
foreach($brotkrumen->krumen as $k => $li)
{
    $breadcrump_ld["itemListElement"][] = [
        "@type" => "ListItem",
        "position" => $k + 1,
        "name" => $li['name'],
        "item" => $li['url']

    ];
}
echo PHP_EOL.'<script type="application/ld+json">'. PHP_EOL.json_encode($breadcrump_ld, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES). PHP_EOL.'</script>'. PHP_EOL;

// Artikelseiten
if($page->page_name == 'product')
{
    global $page, $p_info, $price, $system_status, $currency;

    /**
     *  Artikel
     */
    $product_ld = [
        "@context" => "https://schema.org/",
        "@type" => "Product",
        "name" => $p_info->data["products_name"],
        "description" => trim( $p_info->data["products_short_description"]),
        "sku" => $p_info->data["products_model"]
    ];

    //  Artikel > Beschreibung
    $desc = trim(strip_tags($p_info->data["products_short_description"]));
    if(!empty($desc))
        $desc = trim($p_info->data["products_short_description"]);
    else
        $desc = trim(strip_tags($p_info->data["products_description"]));

    if(!empty($desc))
        $product_ld["description"] =$desc;

    //  Artikel > gtin/ean, mpn
    // fbo in meinen tests hat google immer nur die spezifische gtin erkannt also zb gtin8
    // wir suchen erst nach der spezifischen und fallen dann auf einfache gtin zurück
    $gtin_lengths = [8,12,13,14];
    $gtin = trim($p_info->data["products_ean"]);
    if(!empty($gtin))
    {
        if(in_array(strlen($gtin), $gtin_lengths))
            $product_ld["gtin".strlen($gtin)] = $gtin;
        else
            $product_ld["gtin"] = $gtin;
    }

    $mpn = trim($p_info->data["products_mpn"]);
    if(!empty($mpn))
    {
        $product_ld["mpn"] = $mpn;
    }

    //  Artikel > Marke
    if (!empty($p_info->data["manufacturers_id"]))
    {
        $name = $db->CacheGetOne("SELECT manufacturers_name FROM " . TABLE_MANUFACTURERS . " WHERE manufacturers_id=?", [$p_info->data["manufacturers_id"]]);
        $product_ld["brand"] = [
            "@type" => "Organization",
            "name" => $name
        ];
    }

    //  Artikel > Bilder
    $xt_images[] = $p_info->data["products_image"];
    if(!empty($p_info->data["more_images"]))
    {
        foreach($p_info->data["more_images"] as $v)
            $xt_images[] = $v['file'];
    }
    $imgParams = [
        'type' => 'm_info',
        'img' => "",
        'path_only' => true,
        'return' => true
    ];
    $images = [];
    foreach($xt_images as $v)
    {
        if ($v != 'product:noimage.gif' && !empty($v))
        {
            $imgParams["img"] = $v;
            $imgParams["type"] = "m_info";
            $img_info = image::getImgUrlOrTag($imgParams);
            $imgParams["type"] = "m_thumb";
            $img_thumb = image::getImgUrlOrTag($imgParams);
            $images[] = [
                "@type" => "ImageObject",
                "name" => $p_info->data["products_name"],
                "url" => $img_info,
                "thumbnail" => [
                    //"@type" => "ImageObject",
                    //"name" => $p_info->data["products_name"],
                    "url" => $img_thumb
                ]
            ];
        }
    }
    if (count($images))
        $product_ld["image"] = $images;
    
    /**
     *   Artikel > Angebot
     */
    $cheapest_price = $p_info->data["products_price"]["plain"];
    if(!empty($p_info->data['products_price']['data']['CHEAPEST_PRICE'])
        && $p_info->data['products_price']['data']['CHEAPEST_PRICE'] < $p_info->data["products_price"]["plain"])
        $cheapest_price = $p_info->data['products_price']['data']['CHEAPEST_PRICE'];

        $product_ld["offers"] = [
        "@type" => "Offer",
        "url" => $p_info->data["products_link"],
        "priceCurrency" => $currency->default_currency,
        "price" => round($cheapest_price, 2)
      ];

    //  Artikel > Angebot > gültig bis
    $priceValidUntil = new DateTime();
    $priceValidUntil->add(new DateInterval('P2Y'));
    try
    {
        if(isset($p_info->data["products_price"]["date_expired"]))
        {
            $priceValidUntil = DateTime::createFromFormat("Y-m-d h:i:s", $p_info->data["products_price"]["date_expired"]);
        }
        if(is_object($priceValidUntil))
        {
            $priceValidUntil = $priceValidUntil->format("Y-m-d");
            $product_ld["offers"]["priceValidUntil"] = $priceValidUntil;
        }
    }
    catch(Exception $e){}


    //  Artikel > Angebot > Verkäufer
    $seller = trim(constant("_STORE_SHOPOWNER_COMPANY"));
    if(!empty($seller))
    {
        $product_ld["offers"]["seller"] = [
            "@type" => "Organization",
            "name" => $seller
        ];
    }

    //  Artikel > Angebot > Verfügbarkeit, Zustand
    $condition = !empty($p_info->data["products_condition"]) ?
        "https://schema.org/".$p_info->data["products_condition"] : constant("_STORE_DEFAULT_PRODUCT_CONDITION");
    if(!empty($condition))
    {
        $product_ld["offers"]["itemCondition"] = $condition;
    }
    $availability = !empty($p_info->data["mapping_schema_org"]) ?
        "https://schema.org/".$p_info->data["mapping_schema_org"] : "";
    if(!empty($availability))
    {
        $product_ld["offers"]["availability"] = $availability;
    }


    /**
     *  Artikel > Rating / Review
     */
    $bestRating = 5;
    $worstRating = 1;

    if(!array_key_exists('xt_reviews', $xtPlugin->active_modules) && $p_info->data["products_rating_count"] > 0
        && $p_info->data["products_average_rating"] > 0 && $p_info->data["products_average_rating"] >= $worstRating)
    {
        $product_ld["aggregateRating"] = [
            "@type" => "AggregateRating",
            "ratingValue" => $p_info->data["products_average_rating"],
            "ratingCount" => $p_info->data["products_rating_count"],
            "bestRating" => $bestRating,
            "worstRating" => $worstRating
        ];
    }
    else if(array_key_exists('xt_reviews', $xtPlugin->active_modules) && $p_info->data["products_rating_count"] > 0 )
    {
        $xt_reviews = new xt_reviews();

        $min_rating = defined('XT_REVIEWS_EXPORT_MIN_RATING') && !empty(XT_REVIEWS_EXPORT_MIN_RATING) ? XT_REVIEWS_EXPORT_MIN_RATING : 1;
        $per_page   = defined('XT_REVIEWS_EXPORT_PER_PAGE') && !empty(XT_REVIEWS_EXPORT_PER_PAGE) ? XT_REVIEWS_EXPORT_PER_PAGE : 10;

        $rating_sum = $xt_reviews->getReviewsSum($p_info->data["products_id"]);
        $rating_percent = $xt_reviews->getStars($p_info->data["products_id"]);
        $review_data = $xt_reviews->getReviewsListing($p_info->data["products_id"], true, $per_page, $min_rating);

        if($rating_percent > 0)
        {
            $average_rating = $bestRating * $rating_percent / 100;
            if($average_rating >= $worstRating)
            {
                $product_ld["aggregateRating"] = [
                    "@type" => "AggregateRating",
                    "ratingValue" => $average_rating,
                    "ratingCount" => $p_info->data["products_rating_count"],
                    "bestRating" => $bestRating,
                    "worstRating" => $worstRating
                ];
            }
        }

        //  Artikel > Review
        $product_ld["review"] = [];

        foreach ($review_data['module_content'] as $review)
        {
            $review_date = new DateTime($review['review_date']);
            $review_ld = [
                '@type'=> 'Review',
                'reviewRating'=> [
                    '@type'=> 'Rating',
                    'ratingValue'=> $bestRating / 100 * $review['review_rating']
                ],
                'reviewBody'=> $review['review_text'],
                'datePublished' => $review_date->format('Y-m-d')
            ];
            $author_name = 'Anonym';
            if($review['review_editor_guest'] == false)
            {
                $author_name_parts = explode(' ',$review['review_editor']);
                $author_name_1 = $author_name_parts[0][0].'.';
                array_shift($author_name_parts);
                array_unshift($author_name_parts, $author_name_1);
                $author_name = implode(' ', $author_name_parts);
            }
            $review_ld['author'] = [
                '@type'=> 'Person',
                'name'=> mb_convert_encoding($author_name, 'UTF-8', 'UTF-8')
            ];
            $product_ld["review"][] = $review_ld;
        }
    }

    ($plugin_code = $xtPlugin->PluginCode('json-ld.php:product_bottom')) ? eval($plugin_code) : false;

    echo PHP_EOL.'<script type="application/ld+json">'. PHP_EOL.json_encode($product_ld, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES). PHP_EOL.'</script>'. PHP_EOL;
}

($plugin_code = $xtPlugin->PluginCode('json-ld.php:bottom')) ? eval($plugin_code) : false;

