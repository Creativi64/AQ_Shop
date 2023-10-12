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

global $category, $xtPlugin;
if (!isset($params['position']) || empty($params['position'])) {
    throw new Exception("Missing parameter 'position' for box '{$params['name']}'.");
}

$currentParentId = 0;
$currentParentData = array();
$currentData = array();
$maxLevel = 3;

($plugin_code = $xtPlugin->PluginCode(basename(__FILE__).':top')) ? eval($plugin_code) : false;

switch ($params['position']) {
    case 'navbar':
        $currentData = $category->getCategoryBox(0, true, $maxLevel);
        break;
    case 'sidebar':
        if(isset($params['category_id']) && (!empty($params['category_id']) || $params['category_id']==0))
        {
            $currentParentId = $params['category_id'];
            $currentParentData = $category->buildData(array(
                'categories_id' => $currentParentId,
            ));
        }
        else if (($currentParentId = (isset($category->level[1]) && (int)$category->level[1] != 0) ? (int)$category->level[1] : 0) != 0) {
            $currentParentData = $category->buildData(array(
                'categories_id' => $currentParentId,
            ));
        }
        $nested = false;
        if($params['nested']==1) $nested = true;
        $currentData = $category->getCategoryBox($currentParentId, $nested, $maxLevel);
        if (empty($currentData)) {
            $currentParentId = 0;
            $currentParentData = array();
            $currentData = $category->getCategoryBox();
        }
        break;
}

// active fix for categories level 2+ for xt:C v4.2
if (!empty($currentData)) {
    foreach ($currentData as $k => $i) {
        if (in_array($i['categories_id'], $category->level)) {
            $currentData[$k]['active'] = 1;
        }
    }
}

// template data
$tpl_data = array(
    '_categories' => $currentData,
    'current_parent_id' => $currentParentId,
    'current_parent_data' => $currentParentData,
    '_deepest_level_display' => $category->deepest_level_display,
    'params' => $params,
);

($plugin_code = $xtPlugin->PluginCode(basename(__FILE__).':bottom')) ? eval($plugin_code) : false;
