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

global $db, $current_category_id, $xtLink;

$ns = new nested_set();
$ns->setPosition('getCategoryListing');
$ns->setFilter('Language');

if (USER_POSITION =='store') {
    $ns->setFilter('Seo');
}

$ns->setSQL_SORT("c.sort_order");
$ns->setSQL_WHERE(" and c.start_page_category = 1 ");
$tree = $ns->getTree(false);

$cats = array();
foreach ($tree as &$cat)
{
    if ($cat['category_custom_link']==1) // custom_link not a category
    {
        $url = $ns->buildCustomLinkURL($cat);
        $cat['categories_link'] = $url;
    }
    else {
        $link_array = array('page'=> 'categorie',
            'type'=> 'category',
            'name'=>$cat['categories_name'],
            'text'=>$cat['categories_name'],
            'id'=>$cat['categories_id'],
            'seo_url'=>$cat['url_text'],
        );

        $cat['categories_link'] = $xtLink->_link($link_array);
    }

    $cats[] = $cat;
}

$tpl_data = array('_categories'=> count($cats) ? $cats : false);
