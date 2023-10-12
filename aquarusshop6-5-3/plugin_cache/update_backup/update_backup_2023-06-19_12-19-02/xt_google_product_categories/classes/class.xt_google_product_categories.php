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

require_once _SRV_WEBROOT._SRV_WEB_PLUGINS.'xt_google_product_categories/classes/class.google_categories_import.php';

class google_product_categories extends google_categories_import {
    
    public function getCategories(){
        global $db, $language; // language here refers to backend language

        $query = '';
        $arr = array($language->content_language);
        if ($_POST['query'] && strlen($_POST['query'])>2)
        {
            $q = $db->escape($_POST['query']);
            $query = " AND `category_path` like '%$q%' ";
        }
        $dbData = $db->GetArray("SELECT * FROM ".TABLE_GOOGLE_CATEGORIES." where language = ? $query order by `sort_order`", $arr);

        foreach($dbData as $d){
            $id = self::formatName($d['google_category_id'],$d['category_path']);
            $name = $id;
            $data[] = array('id' => $id, 'name' => $name, 'desc' => '');
        }
        
        return $data; 
    }

    static function formatName($id,$name)
    {
        return $id.' - '.$name;
    }
    
}
