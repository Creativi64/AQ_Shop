<?php
defined('_VALID_CALL') or die('Direct Access is not allowed.');

$db->Execute("CREATE TABLE IF NOT EXISTS ".DB_PREFIX."_aq_timed_banner (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `name` varchar(255) NOT NULL,
    `image` varchar(255) NULL,
    `link` varchar(255) NULL,
    `start` datetime NOT NULL,
    `ende` datetime NOT NULL,
    `status` tinyint(1) NOT NULL DEFAULT '1',
    `sort_order` int(11) NOT NULL DEFAULT '0',
    `date_added` datetime NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

$db->Execute("CREATE TABLE IF NOT EXISTS ".DB_PREFIX."_aq_timed_banner_description (
    `id` int(11) NOT NULL,
    `language_code` char(2) NOT NULL,
    `title` varchar(255) NOT NULL,
    `description` text NOT NULL,
    PRIMARY KEY (`id`,`language_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

// Add admin navigation entry
$db->Execute("INSERT INTO ".TABLE_ADMIN_NAVIGATION." 
    (pid, text, icon, url_i, url_d, sortorder, parent, type, navtype,`iconCls`) VALUES 
    (NULL, 'aq_timed_banner', 'images/icons/clock.png', 
    '&plugin=aq_timed_banner', 
    'adminHandler.php', 
    '5003',
    'shop', 
    'I', 
    'W', 
    'fa fa-clock')");
