<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

$db->Execute("DELETE FROM ".TABLE_ADMIN_NAVIGATION." WHERE text = 'coupons'");
$db->Execute("DELETE FROM ".TABLE_ADMIN_NAVIGATION." WHERE text = 'xt_coupons'");
$db->Execute("DELETE FROM ".TABLE_ADMIN_NAVIGATION." WHERE text = 'xt_coupons_token'");
$db->Execute("DELETE FROM ".TABLE_ADMIN_NAVIGATION." WHERE text = 'xt_coupons_redeem'");


$db->Execute("DROP TABLE IF EXISTS ".DB_PREFIX."_coupons");
$db->Execute("DROP TABLE IF EXISTS ".DB_PREFIX."_coupons_categories");
$db->Execute("DROP TABLE IF EXISTS ".DB_PREFIX."_coupons_customers");
$db->Execute("DROP TABLE IF EXISTS ".DB_PREFIX."_coupons_description");
$db->Execute("DROP TABLE IF EXISTS ".DB_PREFIX."_coupons_generator");
$db->Execute("DROP TABLE IF EXISTS ".DB_PREFIX."_coupons_im_export");
$db->Execute("DROP TABLE IF EXISTS ".DB_PREFIX."_coupons_products");
$db->Execute("DROP TABLE IF EXISTS ".DB_PREFIX."_coupons_redeem");
$db->Execute("DROP TABLE IF EXISTS ".DB_PREFIX."_coupons_token");
$db->Execute("DROP TABLE IF EXISTS ".DB_PREFIX."_coupons_permission");