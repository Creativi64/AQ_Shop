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

include '../xtFramework/admin/main.php';


if (!$xtc_acl->isLoggedIn()) {
    die('login required');
}


$categories = new category();

$source = $_POST['dropId'];
$target = $_POST['targetId'];
$position = $_POST['position'];

$param ='/[^a-zA-Z0-9_-]/';

$source=preg_replace($param,'',$source);
$target=preg_replace($param,'',$target);


// check
if (!strstr($source,'subcat_')) { // check if source is allowed  (must be a category!

    Echo '({"success":false})';
} else {

    if (!strstr($target,'subcat_') && !strstr($target,'node_category') && !strstr($target,'node_cat_store') && !strstr($target,'node_unasigned_cats')) { // check if target is category or root
        // error
        Echo '({"success":false})';
    } else {
        // only move if dropped into categorie
        $source= str_replace('subcat_','',$source);
        $source = str_replace('node_category','',$source);
        $source = str_replace('node_cat_store','',$source);
        $source = str_replace('node_unasigned_cats','',$source);

        $target = str_replace('subcat_','',$target);
        $target = str_replace('node_category','',$target);

        if ($position=='append')
        {
            if (strstr($target,'node_cat_store') ) $target = '';
            $target = str_replace('node_unasigned_cats','',$target);

            $target=(int)$target;
            $source=(int)$source;
            echo $target;
            $categories->moveCategory($source,$target);

            $obj = new stdClass;
            $obj->success = true;
            Echo '({"success":true})';
        } else {
            if (strstr($target,'node_cat_store') ) $target = '';//$target = str_replace('node_cat_store','',$target);
            $target = str_replace('node_unasigned_cats','',$target);

            $source=(int)$source;
            $target=(int)$target;

            $categories->sortCategory($source,$target,$position);

            $obj = new stdClass;
            $obj->success = true;
            Echo '({"success":true})';
        }
    }
}
