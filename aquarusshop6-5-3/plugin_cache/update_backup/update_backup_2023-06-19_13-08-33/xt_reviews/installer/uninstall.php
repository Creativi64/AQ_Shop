<?php


defined('_VALID_CALL') or die('Direct Access is not allowed.');

require_once _SRV_WEBROOT . _SRV_WEB_PLUGINS.'xt_reviews/hooks/page_registry_php_bottom.php';

global $db;

$db->Execute("DELETE FROM `".TABLE_ADMIN_NAVIGATION."` WHERE `text` = 'xt_reviews'");
$db->Execute("UPDATE `".TABLE_PRODUCTS."` SET `products_average_rating` = 0, `products_rating_count` = 0");
$db->Execute("DROP TABLE IF EXISTS `".DB_PREFIX."_products_reviews_permission`;");
$db->Execute("DROP TABLE IF EXISTS `".DB_PREFIX."_products_reviews`;");
$db->Execute("DELETE FROM `".DB_PREFIX."_mail_templates_content` WHERE `tpl_id` IN (SELECT tpl_id FROM `".DB_PREFIX."_mail_templates` WHERE tpl_type='review-notification-mail')");
$db->Execute("DELETE FROM `".DB_PREFIX."_mail_templates` WHERE `tpl_type` = 'review-notification-mail'");



