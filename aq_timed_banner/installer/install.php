<?php
defined('_VALID_CALL') or die('Direct Access is not allowed.');

$db->Execute("CREATE TABLE IF NOT EXISTS ".DB_PREFIX."_aq_timed_banner (
    `banner_id` int(11) NOT NULL AUTO_INCREMENT,
    `banner_name` varchar(255) NOT NULL,
    `banner_image` varchar(255) NOT NULL,
    `banner_link` varchar(255) NOT NULL,
    `banner_start` datetime NOT NULL,
    `banner_end` datetime NOT NULL,
    `banner_status` tinyint(1) NOT NULL DEFAULT '1',
    `sort_order` int(11) NOT NULL DEFAULT '0',
    `date_added` datetime NOT NULL,
    PRIMARY KEY (`banner_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

$db->Execute("CREATE TABLE IF NOT EXISTS ".DB_PREFIX."_aq_timed_banner_description (
    `banner_id` int(11) NOT NULL,
    `language_code` char(2) NOT NULL,
    `banner_title` varchar(255) NOT NULL,
    `banner_description` text NOT NULL,
    PRIMARY KEY (`banner_id`,`language_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
