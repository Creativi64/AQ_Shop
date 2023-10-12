
CREATE TABLE IF NOT EXISTS `adodb_logsql` (
  `created` datetime NOT NULL,
  `sql0` varchar(250) NOT NULL,
  `sql1` text NOT NULL,
  `params` text NOT NULL,
  `tracer` text NOT NULL,
  `timer` decimal(16,6) NOT NULL
) ENGINE=#E_ DEFAULT CHARSET=utf8;


CREATE TABLE `##_acl_area` (
  `area_id` INT NOT NULL auto_increment,
  `area_name` varchar(64) NOT NULL,
  `area_description` text,
  `category` varchar(32) default NULL,
  PRIMARY KEY  (`area_id`)
) ENGINE=#E_ DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


CREATE TABLE `##_acl_area_permissions` (
  `perm_id` INT NOT NULL auto_increment,
  `group_id` INT NOT NULL,
  `area_id` INT default NULL,
  `acl_read` INT default NULL,
  `acl_edit` INT default NULL,
  `acl_new` INT default NULL,
  `acl_delete` INT default NULL,
  PRIMARY KEY  (`perm_id`)
) ENGINE=#E_ DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


CREATE TABLE `##_acl_groups` (
  `group_id` INT NOT NULL auto_increment,
  `name` varchar(64) NOT NULL,
  `status` TINYINT UNSIGNED NOT NULL DEFAULT 1,
  PRIMARY KEY  (`group_id`)
) ENGINE=#E_  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


INSERT INTO `##_acl_groups` VALUES (1, 'Super Admin', 1);

CREATE TABLE `##_acl_nav` (
  `pid` INT NOT NULL auto_increment,
  `text` varchar(64) default NULL,
  `plugin_code` varchar(255) NULL DEFAULT '',
  `icon` varchar(255) default NULL,
  `url_i` varchar(255) default NULL,
  `url_d` varchar(255) default NULL,
  `url_h` varchar(512) default NULL,
  `sortorder` INT unsigned default NULL,
  `parent` varchar(32) default NULL,
  `type` varchar(5) default NULL,
  `navtype` varchar(1) default NULL,
  `cls` varchar(15) default NULL,
  `handler` varchar(20) default NULL,
  `iconCls` varchar(25) default NULL,
  PRIMARY KEY  (`pid`),
  UNIQUE KEY `text` (`text`)
) ENGINE=#E_ DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


INSERT INTO `##_acl_nav` (`text`, `icon`, `url_i`, `url_d`, `sortorder`, `parent`, `type`, `navtype`, `cls`, `handler`, `iconCls`, `url_h`) VALUES ('language', 'images/icons/comments.png', '&pg=overview', 'adminHandler.php', 311000, 'localizing', 'I', 'W', NULL, NULL, 'fa fa-language', 'https://xtcommerce.atlassian.net/wiki/spaces/MANUAL/pages/917611');
INSERT INTO `##_acl_nav` (`text`, `icon`, `url_i`, `url_d`, `sortorder`, `parent`, `type`, `navtype`, `cls`, `handler`, `iconCls`, `url_h`) VALUES ('language_content', 'images/icons/comment_edit.png', NULL, 'adminHandler.php', 311000, 'localizing', 'I', 'W', NULL, NULL, 'fas fa-paragraph', 'https://xtcommerce.atlassian.net/wiki/spaces/MANUAL/pages/917608');
INSERT INTO `##_acl_nav` (`text`, `icon`, `url_i`, `url_d`, `sortorder`, `parent`, `type`, `navtype`, `cls`, `handler`, `iconCls`, `url_h`) VALUES ('localizing', 'images/icons/world.png', NULL, NULL, 310000, 'config', 'G', 'W', NULL, NULL, 'fa fa-map-pin', '');
INSERT INTO `##_acl_nav` (`text` ,`icon` ,`url_i` ,`url_d` ,`sortorder` ,`parent` ,`type` ,`navtype` ,`cls` ,`handler` ,`iconCls`, `url_h`) VALUES ('config_plugin', NULL , NULL , NULL , 350000, '0', 'G', 'W', NULL , NULL , 'fa fa-puzzle-piece', '');
INSERT INTO `##_acl_nav` (`text`, `icon`, `url_i`, `url_d`, `sortorder`, `parent`, `type`, `navtype`, `cls`, `handler`, `iconCls`, `url_h`) VALUES ('email_manager', 'images/icons/email.png', '&pg=overview', 'adminHandler.php', 220000, 'contentroot', 'I', 'W', NULL, NULL, 'far fa-envelope', 'https://xtcommerce.atlassian.net/wiki/spaces/MANUAL/pages/917771');
INSERT INTO `##_acl_nav` (`text`, `icon`, `url_i`, `url_d`, `sortorder`, `parent`, `type`, `navtype`, `cls`, `handler`, `iconCls`, `url_h`) VALUES ('content_manager', 'images/icons/page_white_stack.png', NULL, NULL, 210000, 'contentroot', 'G', 'W', NULL, NULL, 'far fa-file-alt', '');
INSERT INTO `##_acl_nav` (`text`, `icon`, `url_i`, `url_d`, `sortorder`, `parent`, `type`, `navtype`, `cls`, `handler`, `iconCls`, `url_h`) VALUES ('manufacturer', 'images/icons/wrench.png', '&pg=overview', 'adminHandler.php', 3000, 'shop', 'I', 'W', NULL, NULL, 'fa fa-industry', 'https://xtcommerce.atlassian.net/wiki/spaces/MANUAL/pages/102697218');
INSERT INTO `##_acl_nav` (`text`, `icon`, `url_i`, `url_d`, `sortorder`, `parent`, `type`, `navtype`, `cls`, `handler`, `iconCls`, `url_h`) VALUES ('config_store', NULL, NULL, NULL, 400000, '0', 'G', 'W', NULL, NULL, 'fa fa-cogs', '');
INSERT INTO `##_acl_nav` (`text`, `icon`, `url_i`, `url_d`, `sortorder`, `parent`, `type`, `navtype`, `cls`, `handler`, `iconCls`, `url_h`) VALUES ('product', 'images/icons/application_side_tree.png', '&pg=overview', 'adminHandler.php', 2000, 'shop', 'I', 'W', NULL, NULL, 'fa fa-barcode', 'https://xtcommerce.atlassian.net/wiki/spaces/MANUAL/pages/917669');

INSERT INTO `##_acl_nav` (`text`, `icon`, `url_i`, `url_d`, `sortorder`, `parent`, `type`, `navtype`, `cls`, `handler`, `iconCls`, `url_h`) VALUES ('config', NULL, NULL, NULL, 300000, '0', 'G', 'W', NULL, NULL, 'fa fa-cog', '');
INSERT INTO `##_acl_nav` (`text`, `icon`, `url_i`, `url_d`, `sortorder`, `parent`, `type`, `navtype`, `cls`, `handler`, `iconCls`, `url_h`) VALUES ('contentroot', NULL, NULL, NULL, 200000, '0', 'G', 'W', NULL, NULL, 'fa fa-file', '');
INSERT INTO `##_acl_nav` (`text`, `icon`, `url_i`, `url_d`, `sortorder`, `parent`, `type`, `navtype`, `cls`, `handler`, `iconCls`, `url_h`) VALUES ('shop', NULL, NULL, NULL, 100000, '0', 'G', 'W', NULL, NULL, 'fa fa-book', '');
INSERT INTO `##_acl_nav` (`text`, `icon`, `url_i`, `url_d`, `sortorder`, `parent`, `type`, `navtype`, `cls`, `handler`, `iconCls`, `url_h`) VALUES ('tax', 'images/icons/table_save.png', '&pg=overview', 'adminHandler.php', 2, 'localizing', 'I', 'W', NULL, NULL, 'fa fa-percent', 'https://xtcommerce.atlassian.net/wiki/spaces/MANUAL/pages/917598');
INSERT INTO `##_acl_nav` (`text`, `icon`, `url_i`, `url_d`, `sortorder`, `parent`, `type`, `navtype`, `cls`, `handler`, `iconCls`, `url_h`) VALUES ('currency', 'images/icons/money_euro.png', '&pg=overview', 'adminHandler.php', 2, 'localizing', 'I', 'W', NULL, NULL, 'fa fa-euro-sign', 'https://xtcommerce.atlassian.net/wiki/spaces/MANUAL/pages/917610');
INSERT INTO `##_acl_nav` (`text`, `icon`, `url_i`, `url_d`, `sortorder`, `parent`, `type`, `navtype`, `cls`, `handler`, `iconCls`, `url_h`) VALUES ('tax_class', 'images/icons/table_relationship.png', '&pg=overview', 'adminHandler.php', 2, 'localizing', 'I', 'W', NULL, NULL, 'fa fa-folder', 'https://xtcommerce.atlassian.net/wiki/spaces/MANUAL/pages/917609');
INSERT INTO `##_acl_nav` (`text`, `icon`, `url_i`, `url_d`, `sortorder`, `parent`, `type`, `navtype`, `cls`, `handler`, `iconCls`, `url_h`) VALUES ('payment', 'images/icons/money.png', '&pg=overview', 'adminHandler.php', 2, 'config', 'I', 'W', NULL, NULL, 'fa fa-credit-card', 'https://xtcommerce.atlassian.net/wiki/spaces/MANUAL/pages/917705');
INSERT INTO `##_acl_nav` (`text`, `icon`, `url_i`, `url_d`, `sortorder`, `parent`, `type`, `navtype`, `cls`, `handler`, `iconCls`, `url_h`) VALUES ('shipping', 'images/icons/package_green.png', '&pg=overview', 'adminHandler.php', 2, 'config', 'I', 'W', NULL, NULL, 'fa fa-shipping-fast', 'https://xtcommerce.atlassian.net/wiki/spaces/MANUAL/pages/917772');
INSERT INTO `##_acl_nav` (`text`, `icon`, `url_i`, `url_d`, `sortorder`, `parent`, `type`, `navtype`, `cls`, `handler`, `iconCls`, `url_h`) VALUES ('countries', 'images/icons/page_world.png', '&pg=overview', 'adminHandler.php', 2, 'localizing', 'G', 'W', NULL, NULL, 'fas fa-globe-americas', 'https://xtcommerce.atlassian.net/wiki/spaces/MANUAL/pages/917613');
INSERT INTO `##_acl_nav` (`text`, `icon`, `url_i`, `url_d`, `sortorder`, `parent`, `type`, `navtype`, `cls`, `handler`, `iconCls`, `url_h`) VALUES ('ordertab', NULL, NULL, NULL, 100000, '0', 'G', 'W', NULL, NULL, 'fa fa-shopping-cart', '');
INSERT INTO `##_acl_nav` (`text`, `icon`, `url_i`, `url_d`, `sortorder`, `parent`, `type`, `navtype`, `cls`, `handler`, `iconCls`, `url_h`) VALUES ('order', 'images/icons/basket_edit.png', '&pg=overview', 'adminHandler.php', 2, 'ordertab', 'I', 'W', NULL, NULL, 'fa fa-shopping-cart', 'https://xtcommerce.atlassian.net/wiki/spaces/MANUAL/pages/295305237');
INSERT INTO `##_acl_nav` (`text`, `icon`, `url_i`, `url_d`, `sortorder`, `parent`, `type`, `navtype`, `cls`, `handler`, `iconCls`, `url_h`) VALUES ('customer', 'images/icons/group.png', '&pg=overview', 'adminHandler.php', 2, 'ordertab', 'I', 'W', NULL, NULL, 'far fa-address-book', 'https://xtcommerce.atlassian.net/wiki/spaces/MANUAL/pages/917709');
INSERT INTO `##_acl_nav` (`text`, `icon`, `url_i`, `url_d`, `sortorder`, `parent`, `type`, `navtype`, `cls`, `handler`, `iconCls`, `url_h`) VALUES ('customers_status', 'images/icons/folder_user.png', '&pg=overview', 'adminHandler.php', 2, 'config', 'I', 'W', NULL, NULL, 'fas fa-users', 'https://xtcommerce.atlassian.net/wiki/spaces/MANUAL/pages/917703');
INSERT INTO `##_acl_nav` (`text`, `icon`, `url_i`, `url_d`, `sortorder`, `parent`, `type`, `navtype`, `cls`, `handler`, `iconCls`, `url_h`) VALUES ('exp', 'images/icons/transmit_blue.png', NULL, NULL, NULL, 'contentroot', 'G', 'W', NULL, NULL, 'fas fa-file-export', '');
INSERT INTO `##_acl_nav` (`text`, `icon`, `url_i`, `url_d`, `sortorder`, `parent`, `type`, `navtype`, `cls`, `handler`, `iconCls`, `url_h`) VALUES ('export', 'images/icons/transmit_go.png', NULL, 'adminHandler.php', NULL, 'exp', 'I', 'W', NULL, NULL, 'fas fa-file-export', 'https://xtcommerce.atlassian.net/wiki/spaces/MANUAL/pages/917740');

INSERT INTO `##_acl_nav` (`text`, `icon`, `url_i`, `url_d`, `sortorder`, `parent`, `type`, `navtype`, `cls`, `handler`, `iconCls`, `url_h`) VALUES ('system_status', 'images/icons/application_view_tile.png', NULL, NULL, NULL, 'config', 'G', 'W', NULL, NULL, 'fa fa-list-ul', '');
INSERT INTO `##_acl_nav` (`text`, `icon`, `url_i`, `url_d`, `sortorder`, `parent`, `type`, `navtype`, `cls`, `handler`, `iconCls`, `url_h`) VALUES ('content', 'images/icons/layout.png', '&pg=overview', 'adminHandler.php', 210000, 'content_manager', 'I', 'W', NULL, NULL, 'far fa-file-alt', 'https://xtcommerce.atlassian.net/wiki/spaces/MANUAL/pages/101891560');
INSERT INTO `##_acl_nav` (`text`, `icon`, `url_i`, `url_d`, `sortorder`, `parent`, `type`, `navtype`, `cls`, `handler`, `iconCls`, `url_h`) VALUES ('content_blocks', 'images/icons/layout_content.png', '&pg=overview', 'adminHandler.php', 210000, 'content_manager', 'I', 'W', NULL, 'fas fa-th', '', 'https://xtcommerce.atlassian.net/wiki/spaces/MANUAL/pages/917738');
INSERT INTO `##_acl_nav` (`text`, `icon`, `url_i`, `url_d`, `sortorder`, `parent`, `type`, `navtype`, `cls`, `handler`, `iconCls`, `url_h`) VALUES ('media_manager', 'images/icons/cd.png', NULL, NULL, 210000, 'contentroot', 'G', 'W', NULL, NULL, 'far fa-images', '');
INSERT INTO `##_acl_nav` (`text`, `icon`, `url_i`, `url_d`, `sortorder`, `parent`, `type`, `navtype`, `cls`, `handler`, `iconCls`, `url_h`) VALUES ('plugin_installed', 'images/icons/plugin_add.png', '&pg=overview', 'adminHandler.php', 1000, 'config_plugin', 'I', 'W', NULL, NULL, 'far fa-check-circle', 'https://xtcommerce.atlassian.net/wiki/spaces/MANUAL/pages/295337989');
INSERT INTO `##_acl_nav` (`text`, `icon`, `url_i`, `url_d`, `sortorder`, `parent`, `type`, `navtype`, `cls`, `handler`, `iconCls`, `url_h`) VALUES ('plugin_uninstalled', 'images/icons/plugin_disabled.png', '&pg=overview', 'adminHandler.php', 1100, 'config_plugin', 'I', 'W', NULL, NULL, 'far fa-times-circle', 'https://xtcommerce.atlassian.net/wiki/spaces/MANUAL/pages/295108639');
INSERT INTO `##_acl_nav` (`text`, `icon`, `url_i`, `url_d`, `sortorder`, `parent`, `type`, `navtype`, `cls`, `handler`, `iconCls`, `url_h`) VALUES ('configuration', 'images/icons/cog.png', NULL, NULL, NULL, 'config', 'G', 'W', NULL, NULL, 'fa fa-cog', '');
INSERT INTO `##_acl_nav` (`text`, `icon`, `url_i`, `url_d`, `sortorder`, `parent`, `type`, `navtype`, `cls`, `handler`, `iconCls`, `url_h`) VALUES ('admin_perm', 'images/icons/key.png', NULL, NULL, NULL, 'config', 'G', 'W', NULL, NULL, 'fa fa-lock', '');
INSERT INTO `##_acl_nav` (`text`, `icon`, `url_i`, `url_d`, `sortorder`, `parent`, `type`, `navtype`, `cls`, `handler`, `iconCls`, `url_h`) VALUES ('acl_area', 'images/icons/page_key.png', '&pg=overview', 'adminHandler.php', NULL, 'admin_perm', 'I', 'W', NULL, NULL, 'fa fa-list-ul', 'https://xtcommerce.atlassian.net/wiki/spaces/MANUAL/pages/295075843');
INSERT INTO `##_acl_nav` (`text`, `icon`, `url_i`, `url_d`, `sortorder`, `parent`, `type`, `navtype`, `cls`, `handler`, `iconCls`, `url_h`) VALUES ('acl_groups', 'images/icons/folder_key.png', '&pg=overview', 'adminHandler.php', NULL, 'admin_perm', 'I', 'W', NULL, NULL, 'fa fa-users', 'https://xtcommerce.atlassian.net/wiki/spaces/MANUAL/pages/295108617');
INSERT INTO `##_acl_nav` (`text`, `icon`, `url_i`, `url_d`, `sortorder`, `parent`, `type`, `navtype`, `cls`, `handler`, `iconCls`, `url_h`) VALUES ('acl_user', 'images/icons/user_red.png', '&pg=overview', 'adminHandler.php', NULL, 'admin_perm', 'I', 'W', NULL, NULL, 'fa fa-user', 'https://xtcommerce.atlassian.net/wiki/spaces/MANUAL/pages/295272449');

INSERT INTO `##_acl_nav` (`text`, `icon`, `url_i`, `url_d`, `sortorder`, `parent`, `type`, `navtype`, `cls`, `handler`, `iconCls`, `url_h`) VALUES ('systemroot', NULL, NULL, NULL, 600000, '0', 'G', 'W', NULL, NULL, 'fa fa-tachometer-alt', '');

INSERT INTO `##_acl_nav` (`text`, `icon`, `sortorder`, `parent`, `type`, `navtype`, `cls`, `handler`, `iconCls`, `url_h`) VALUES ('logs', 'images/icons/script_code_red.png', 2, 'systemroot', 'G', 'W', NULL, NULL, NULL, '');
INSERT INTO `##_acl_nav` (`text`, `icon`, `url_i`, `url_d`, `sortorder`, `parent`, `type`, `navtype`, `cls`, `handler`, `iconCls`, `url_h`) VALUES ('bruto_force_protection', 'images/icons/script_code.png', '&pg=overview', 'adminHandler.php', NULL, 'logs', 'I', 'W', NULL, NULL, NULL, 'https://xtcommerce.atlassian.net/wiki/spaces/MANUAL/pages/295075855');
INSERT INTO `##_acl_nav` (`text`, `icon`, `url_i`, `url_d`, `sortorder`, `parent`, `type`, `navtype`, `cls`, `handler`, `iconCls`, `url_h`) VALUES ('MediaGallery', '', '', '', NULL, 'media_manager', 'G', 'W', NULL, NULL, 'far fa-images', 'https://xtcommerce.atlassian.net/wiki/spaces/MANUAL/pages/917773');
INSERT INTO `##_acl_nav` (`text`, `icon`, `url_i`, `url_d`, `sortorder`, `parent`, `type`, `navtype`, `cls`, `handler`, `iconCls`, `url_h`) VALUES ('MediaFileTypes', 'images/icons/connect.png', '&pg=overview', 'adminHandler.php', NULL, 'media_manager', 'I', 'W', NULL, NULL, 'far fa-file-word', 'https://xtcommerce.atlassian.net/wiki/spaces/MANUAL/pages/917769');
INSERT INTO `##_acl_nav` (`text`, `icon`, `url_i`, `url_d`, `sortorder`, `parent`, `type`, `navtype`, `cls`, `handler`, `iconCls`, `url_h`) VALUES ('ImageTypes', 'images/icons/camera_edit.png', '&pg=overview', 'adminHandler.php', NULL, 'media_manager', 'I', 'W', NULL, NULL, 'far fa-file-image', 'https://xtcommerce.atlassian.net/wiki/spaces/MANUAL/pages/917770');

INSERT INTO `##_acl_nav` (`text`, `icon`, `url_i`, `url_d`, `sortorder`, `parent`, `type`, `navtype`, `cls`, `handler`, `iconCls`, `url_h`) VALUES ('callback', 'images/icons/script_code.png', '&pg=overview', 'adminHandler.php', NULL, 'logs', 'I', 'W', NULL, NULL, NULL, 'https://xtcommerce.atlassian.net/wiki/spaces/MANUAL/pages/295108635');
INSERT INTO `##_acl_nav` (`text`, `icon`, `url_i`, `url_d`, `sortorder`, `parent`, `type`, `navtype`, `cls`, `handler`, `iconCls`, `url_h`) VALUES ('LogHandler', 'images/icons/script_code.png', '&pg=overview', 'adminHandler.php', NULL, 'logs', 'I', 'W', NULL, NULL, 'far fa-file-alt', 'https://xtcommerce.atlassian.net/wiki/spaces/MANUAL/pages/295337985');

INSERT INTO `##_acl_nav` (`text`, `icon`, `url_i`, `url_d`, `sortorder`, `parent`, `type`, `navtype`, `cls`, `handler`, `iconCls`, `url_h`) VALUES('shipping_zone', 'images/icons/package_link.png', '&pg=overview', 'adminHandler.php', 10, 'localizing', 'I', 'W', NULL, NULL, 'fas fa-truck', 'https://xtcommerce.atlassian.net/wiki/spaces/MANUAL/pages/917779');
INSERT INTO `##_acl_nav` (`text`, `icon`, `url_i`, `url_d`, `sortorder`, `parent`, `type`, `navtype`, `cls`, `handler`, `iconCls`, `url_h`) VALUES('federal_states', 'images/icons/page_world.png', '&pg=overview', 'adminHandler.php', 7000, 'countries', 'I', 'W', NULL, NULL, NULL, 'https://xtcommerce.atlassian.net/wiki/spaces/MANUAL/pages/51052577');

INSERT INTO `##_acl_nav` (`text`, `icon`, `url_i`, `url_d`, `sortorder`, `parent`, `type`, `navtype`, `cls`, `handler`, `iconCls`, `url_h`) VALUES ('phpinfo', 'images/icons/page_white_php.png', '', 'phpinfo.php?pg=overview', 7000, 'systemroot', 'I', 'W', NULL, NULL, 'fab fa-php', 'https://xtcommerce.atlassian.net/wiki/spaces/MANUAL/pages/295174147');
INSERT INTO `##_acl_nav` (`text`, `icon`, `url_i`, `url_d`, `sortorder`, `parent`, `type`, `navtype`, `cls`, `handler`, `iconCls`, `url_h`) VALUES ('license_info', 'images/icons/textfield_key.png', '', 'license_info.php?pg=overview', 7001, 'systemroot', 'I', 'W', NULL, NULL, NULL, 'https://xtcommerce.atlassian.net/wiki/spaces/MANUAL/pages/295305225');
INSERT INTO `##_acl_nav` (`text`, `icon`, `url_i`, `url_d`, `sortorder`, `parent`, `type`, `navtype`, `cls`, `handler`, `iconCls`, `url_h`) VALUES ('xt_cron', 'images/icons/clock.png', '&pg=overview', 'adminHandler.php', 4050, 'config', 'I', 'W', NULL, NULL, 'fa fa-clock', 'https://xtcommerce.atlassian.net/wiki/spaces/MANUAL/pages/25559180');
INSERT INTO `##_acl_nav` (`text`, `icon`, `url_i`, `url_d`, `sortorder`, `parent`, `type`, `navtype`, `cls`, `handler`, `iconCls`, `url_h`) VALUES ('cat_store1', NULL, NULL, NULL, 1000, 'shop', 'G', 'W', NULL, NULL, NULL, '');
INSERT INTO `##_acl_nav` (`text`, `icon`, `url_i`, `url_d`, `sortorder`, `parent`, `type`, `navtype`, `cls`, `handler`, `iconCls`, `url_h`) VALUES ('unasigned_cats', NULL, NULL, NULL, 1001, 'shop', 'G', 'W', NULL, NULL, NULL, '');

INSERT INTO `##_acl_nav` (`text`, `icon`, `url_i`, `url_d`, `sortorder`, `parent`, `type`, `navtype`, `cls`, `handler`, `iconCls`, `url_h`) VALUES ('config_seo', NULL , NULL , NULL , 360000, '0', 'G', 'W', NULL , NULL , 'fa fa-magic', '');
INSERT INTO `##_acl_nav` (`text`, `icon`, `url_i`, `url_d`, `sortorder`, `parent`, `type`, `navtype`, `cls`, `handler`, `iconCls`, `url_h`) VALUES ('seo_plugins', 'images/icons/script_gear.png', '&pg=overview', 'adminHandler.php', 1200, 'config_seo', 'I', 'W', NULL, NULL, NULL, 'https://xtcommerce.atlassian.net/wiki/spaces/MANUAL/pages/51052574');
INSERT INTO `##_acl_nav` (`text`, `icon`, `url_i`, `url_d`, `sortorder`, `parent`, `type`, `navtype`, `cls`, `handler`, `iconCls`, `url_h`) VALUES ('stop_words', 'images/icons/page_red.png', '&pg=overview', 'adminHandler.php', 1100, 'config_seo', 'I', 'W', NULL, NULL, NULL, 'https://xtcommerce.atlassian.net/wiki/spaces/MANUAL/pages/47972407');
INSERT INTO `##_acl_nav` (`text`, `icon`, `url_i`, `url_d`, `sortorder`, `parent`, `type`, `navtype`, `cls`, `handler`, `iconCls`, `url_h`) VALUES ('redirect', 'images/icons/link_go.png', NULL, NULL, 1300, 'config_seo', 'G', 'W', NULL, NULL, '', '');
INSERT INTO `##_acl_nav` (`text`, `icon`, `url_i`, `url_d`, `sortorder`, `parent`, `type`, `navtype`, `cls`, `handler`, `iconCls`, `url_h`) VALUES ('redirect_deleted', 'images/icons/link_delete.png', '&pg=overview', 'adminHandler.php', 12, 'redirect', 'I', 'W', NULL, NULL, '', 'https://xtcommerce.atlassian.net/wiki/spaces/MANUAL/pages/47972407');
INSERT INTO `##_acl_nav` (`text`, `icon`, `url_i`, `url_d`, `sortorder`, `parent`, `type`, `navtype`, `cls`, `handler`, `iconCls`, `url_h`) VALUES ('redirect_404', 'images/icons/link_break.png', '&pg=overview', 'adminHandler.php', 12, 'redirect', 'I', 'W', NULL, NULL, '', 'https://xtcommerce.atlassian.net/wiki/spaces/MANUAL/pages/47972409');
INSERT INTO `##_acl_nav` (`text`, `icon`, `url_i`, `url_d`, `sortorder`, `parent`, `type`, `navtype`, `cls`, `handler`, `iconCls`, `url_h`) VALUES ('conf_seo', 'images/icons/page_link.png' , NULL , 'adminHandler.php?load_section=configuration&edit_id=21' , 1000, 'config_seo', 'I', 'W', NULL , NULL , '', 'https://xtcommerce.atlassian.net/wiki/spaces/MANUAL/pages/917614');
INSERT INTO `##_acl_nav` (`text`, `icon`, `url_i`, `url_d`, `sortorder`, `parent`, `type`, `navtype`, `cls`, `handler`, `iconCls`, `url_h`) VALUES ('seo_regenerate', 'images/icons/building_link.png' , '&pg=overview' , 'adminHandler.php' , 1000, 'config_seo', 'I', 'W', NULL , NULL , NULL, 'https://xtcommerce.atlassian.net/wiki/spaces/MANUAL/pages/48332806');

INSERT INTO `##_acl_nav` (`text`, `icon`, `url_i`, `url_d`, `sortorder`, `parent`, `type`, `navtype`, `cls`, `handler`, `iconCls`, `url_h`) VALUES ('slider', 'images/icons/layers.png' , '&pg=overview' , 'adminHandler.php' , 200000, 'content_manager', 'I', 'W', NULL , NULL , 'far fa-images', 'https://xtcommerce.atlassian.net/wiki/spaces/MANUAL/pages/85884991');

INSERT INTO `##_acl_nav` (`text`, `icon`, `url_i`, `url_d`, `sortorder`, `parent`, `type`, `navtype`, `cls`, `handler`, `iconCls`, `url_h`) VALUES ('themes', '' , '&pg=overview' , 'adminHandler.php' , 4050, 'config', 'I', 'W', NULL , NULL , 'fab fa-css3-alt', 'https://xtcommerce.atlassian.net/wiki/spaces/MANUAL/pages/624656385');


CREATE TABLE `##_acl_task` (
  `id` INT NOT NULL auto_increment,
  `user_id` INT NOT NULL,
  `class` varchar(32) NOT NULL,
  `active_id` varchar(32) NULL,
  `action` enum('new','edit','save','view','delete','select','unset','copy','move','link','setStatus','setAllRights','unsetAllRights','rebuildSeo') NOT NULL,
  `closed` enum('true','false') NOT NULL,
  `task_key` varchar(255) default NULL,
  `date_added` datetime NOT NULL DEFAULT current_timestamp,
  `last_modified` datetime NULL DEFAULT NULL ON UPDATE current_timestamp,
  PRIMARY KEY  (`id`)
) ENGINE=#E_ DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


CREATE TABLE `##_acl_user` (
  `user_id` INT NOT NULL auto_increment,
  `group_id` INT NOT NULL,
  `handle` varchar(64) NOT NULL,
  `user_password` varchar(255) NOT NULL,
  `email` varchar(64) NOT NULL,
  `firstname` varchar(64) NOT NULL,
  `lastname` varchar(64) NOT NULL,
  `status` TINYINT UNSIGNED NOT NULL DEFAULT 1,
  `ip_restriction` varchar(255) default NULL,
  `password_request_key` varchar(255) default NULL,
  PRIMARY KEY  (`user_id`),
   UNIQUE KEY `handle_unique` (`handle` ASC)
) ENGINE=#E_ DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


INSERT INTO `##_acl_user` VALUES (1, 1, 'admin', '21232f297a57a5a743894a0e4a801fc3', 'admin@your_xt_shop.de', 'Admin', 'Admin', 1,'', '');

CREATE TABLE `##_callback_log` (
  `id` INT NOT NULL auto_increment,
  `module` varchar(64) NOT NULL DEFAULT '',
  `orders_id` INT NOT NULL DEFAULT 0,
  `transaction_id` varchar(255) NOT NULL DEFAULT '',
  `callback_data` longtext NOT NULL,
  `created` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `class` varchar(32) NOT NULL DEFAULT '',
  `error_msg` varchar(255) NOT NULL DEFAULT '',
  `error_data` longtext NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=#E_ DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE `##_campaigns` (
  `campaigns_id` INT NOT NULL auto_increment,
  `campaigns_name` varchar(32) NOT NULL default '',
  `campaigns_refID` varchar(64) default NULL,
  `campaigns_leads` INT default '0',
  `date_added` datetime NOT NULL DEFAULT current_timestamp,
  `last_modified` datetime NULL DEFAULT NULL ON UPDATE current_timestamp,
  PRIMARY KEY  (`campaigns_id`),
  KEY `IDX_CAMPAIGNS_NAME` (`campaigns_name`)
) ENGINE=#E_ DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE `##_categories` (
  `categories_id` INT NOT NULL auto_increment,
  `external_id` varchar(255) default NULL,
  `permission_id` INT default NULL,
  `categories_owner` INT NOT NULL default '1',
  `categories_image` varchar(255) default NULL,
  `categories_left` INT NOT NULL DEFAULT '0',
  `categories_right` INT NOT NULL DEFAULT '0',
  `categories_level` TINYINT UNSIGNED NOT NULL,
  `parent_id` INT default '0',
  `categories_status` TINYINT UNSIGNED NOT NULL DEFAULT 1,
  `categories_template` varchar(64) default NULL,
  `listing_template` varchar(64) default NULL,
  `sort_order` INT default '0',
  `products_sorting` varchar(32) default NULL,
  `products_sorting2` varchar(32) default NULL,
  `top_category` TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `start_page_category` TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `date_added` datetime NOT NULL DEFAULT current_timestamp,
  `last_modified` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp,
  `category_custom_link` TINYINT UNSIGNED NOT NULL,
  `category_custom_link_type` varchar(32) NOT NULL,
  `category_custom_link_id` INT NOT NULL,
  PRIMARY KEY  (`categories_id`),
  KEY `idx_categories_parent_id` (`parent_id`),
  INDEX `bb_nested_set_performance_categories_left` (`categories_left`)
) ENGINE=#E_ DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `##_categories_custom_link_url` (
  `categories_id` INT NOT NULL,
  `link_url` varchar(256) NULL,
  `language_code` char(2) NOT NULL,
  `store_id` INT NOT NULL,
  PRIMARY KEY (`language_code`,`categories_id`,`store_id`),
  INDEX `categories_id_store_id` (`categories_id`, `store_id`)
) ENGINE=#E_ DEFAULT CHARSET=utf8;

CREATE TABLE `##_categories_description` (
  `categories_id` INT NOT NULL default '0',
  `language_code` char(2) NOT NULL default '1',
  `categories_name` varchar(255) default NULL,
  `categories_heading_title` varchar(255) default NULL,
  `categories_description` text,
  `categories_description_bottom` text,
  `categories_store_id` INT NOT NULL,
  PRIMARY KEY  (`categories_id`,`language_code`,`categories_store_id`),
  FULLTEXT KEY `idx_categories_name` (`categories_name`),
  FULLTEXT KEY `language_code` (`language_code`)
) ENGINE=#E_ DEFAULT CHARSET=utf8mb4;


CREATE TABLE `##_categories_permission` (
  `pid` INT NOT NULL,
  `permission` TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `pgroup` varchar(255) NOT NULL,
  PRIMARY KEY  (`pid`,`pgroup`)
) ENGINE=#E_ DEFAULT CHARSET=utf8;


CREATE TABLE `##_config` (
  `id` INT NOT NULL auto_increment,
  `config_key` varchar(64) NOT NULL,
  `config_value` text NOT NULL,
  `group_id` INT NOT NULL,
  `sort_order` INT default NULL,
  `last_modified` datetime NULL DEFAULT NULL ON UPDATE current_timestamp,
  `date_added` datetime NOT NULL DEFAULT current_timestamp,
  `type` varchar(255) default NULL,
  `url` varchar(255) default NULL,
  PRIMARY KEY  (`id`),
  KEY `idx_configuration_group_id` (`group_id`)
) ENGINE=#E_  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE `##_config_lang` (
  `language_content_id` INT NOT NULL auto_increment,
  `language_code` char(2) default NULL,
  `config_key` varchar(255) default NULL,
  `language_value` text CHARACTER SET utf8mb4,
  `store_id` INT NOT NULL DEFAULT 0,
  `group_id` INT NOT NULL DEFAULT 0,
  `sort_order` INT NOT NULL DEFAULT 0,
  `type` varchar(255) default NULL,
  PRIMARY KEY  (`language_content_id`),
  UNIQUE KEY `key_lang_store` (`language_code`,`config_key`,`store_id`)
) ENGINE=#E_  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

-- invisible
INSERT INTO `##_config` (`config_key`, `config_value`, `group_id`, `sort_order`, `type`, `url`) VALUES ('_SYSTEM_DEBUG', 'false', 18, 9, 'dropdown', 'conf_truefalse');
INSERT INTO `##_config` (`config_key`, `config_value`, `group_id`, `sort_order`, `type`, `url`) VALUES ('_STORE_PRODUCT_NO_PICTURE', 'noimage.gif', 18, 9, '', '');
INSERT INTO `##_config` (`config_key`, `config_value`, `group_id`, `sort_order`, `type`, `url`) VALUES ('_STORE_IMAGES_PATH_FULL', 'true', 23, 9, 'dropdown', 'conf_truefalse');
INSERT INTO `##_config` (`config_key`, `config_value`, `group_id`, `sort_order`, `type`, `url`) VALUES ('_SYSTEM_SAVE_IP', 'false', 18, 10, 'dropdown', 'conf_truefalse');


-- template
INSERT INTO `##_config` (`config_key`, `config_value`, `group_id`, `sort_order`, `type`, `url`) VALUES ('_SYSTEM_TEMPLATE', 'ew_evelations', 15, 99, 'dropdown', 'templateSets');

-- permissions
INSERT INTO `##_config` (`config_key`, `config_value`, `group_id`, `sort_order`, `type`, `url`) VALUES ('_SYSTEM_GROUP_CHECK', 'true', 17, 9, 'dropdown', 'conf_truefalse');
INSERT INTO `##_config` (`config_key`, `config_value`, `group_id`, `sort_order`, `type`, `url`) VALUES ('_SYSTEM_ADMIN_PERMISSIONS', 'blacklist', 17, 9, 'dropdown', 'admin_perm');
INSERT INTO `##_config` (`config_key`, `config_value`, `group_id`, `sort_order`, `type`, `url`) VALUES ('_SYSTEM_GROUP_PERMISSIONS', 'blacklist', 17, 9, 'dropdown', 'admin_perm');
INSERT INTO `##_config` (`config_key`, `config_value`, `group_id`, `sort_order`, `type`, `url`) VALUES ('_SYSTEM_SIMPLE_GROUP_PERMISSIONS', 'false', 17, 11, 'dropdown', 'conf_truefalse');
INSERT INTO `##_config` (`config_key`, `config_value`, `group_id`, `sort_order`, `type`, `url`) VALUES ('_SYSTEM_ADMIN_RIGHTS', 'db', 17, 9, 'hidden', 'admin_rights');


INSERT INTO `##_config` (`config_key`, `config_value`, `group_id`, `sort_order`, `type`, `url`) VALUES ('_SYSTEM_SENDMAIL_PATH', '/usr/sbin/sendmail', 19, 2, NULL, NULL);
INSERT INTO `##_config` (`config_key`, `config_value`, `group_id`, `sort_order`, `type`, `url`) VALUES ('_SYSTEM_MAIL_TYPE', 'sendmail', 19, 3, 'dropdown', 'mail_types');
INSERT INTO `##_config` (`config_key`, `config_value`, `group_id`, `sort_order`, `type`, `url`) VALUES ('_SYSTEM_MAIL_DEBUG', 'true', 19, 4, 'dropdown', 'conf_truefalse');

-- performance


-- seo
INSERT INTO `##_config` (`config_key`, `config_value`, `group_id`, `sort_order`, `type`, `url`) VALUES ('_SYSTEM_MOD_REWRITE', 'true', 21, 1, 'dropdown', 'conf_truefalse');
INSERT INTO `##_config` (`config_key`, `config_value`, `group_id`, `sort_order`, `type`, `url`) VALUES ('_SYSTEM_MOD_REWRITE_404', 'true', 21, 2, 'dropdown', 'conf_truefalse');
INSERT INTO `##_config` (`config_key`, `config_value`, `group_id`, `sort_order`, `type`, `url`) VALUES ('_SYSTEM_HIDE_SUMAURL', 'false', 21, 3, 'dropdown', 'conf_truefalse');
INSERT INTO `##_config` (`config_key`, `config_value`, `group_id`, `sort_order`, `type`, `url`) VALUES ('_SYSTEM_METATAGS_WORDLENGTH', '6', 21, 4, NULL, NULL);
INSERT INTO `##_config` (`config_key`, `config_value`, `group_id`, `sort_order`, `type`, `url`) VALUES ('_SYSTEM_METATAGS_MAXLENGTH', '800', 21, 5, NULL, NULL);
INSERT INTO `##_config` (`config_key`, `config_value`, `group_id`, `sort_order`, `type`, `url`) VALUES ('_SYSTEM_METATAGS_MAXCOUNT', '20', 21, 6, NULL, NULL);
INSERT INTO `##_config` (`config_key`, `config_value`, `group_id`, `sort_order`, `type`, `url`) VALUES ('_SYSTEM_METATAGS_AUTOGENERATE', 'true', 21, 7, 'dropdown', 'conf_truefalse');
INSERT INTO `##_config` (`config_key`, `config_value`, `group_id`, `sort_order`, `type`, `url`) VALUES ('_SYSTEM_SEO_FILE_TYPE', '', 21, 8, '', '');
INSERT INTO `##_config` (`config_key`, `config_value`, `group_id`, `sort_order`, `type`, `url`) VALUES ('_SYSTEM_SEO_URL_LANG_BASED', 'true', 21, 9, 'dropdown', 'conf_truefalse');
INSERT INTO `##_config` (`config_key`, `config_value`, `group_id`, `sort_order`, `type`, `url`) VALUES ('_SYSTEM_SEO_PRODUCTS_CATEGORIES', 'true', 21, 10, 'dropdown', 'conf_truefalse');

INSERT INTO `##_config` (`config_key`, `config_value`, `group_id`, `sort_order`, `type`, `url`) VALUES ('_SYSTEM_CSS_MINIFY_OPTION', 'single', 21, 12, 'dropdown', 'minify');
INSERT INTO `##_config` (`config_key`, `config_value`, `group_id`, `sort_order`, `type`, `url`) VALUES ('_SYSTEM_JS_MINIFY_OPTION', 'single', 21, 13, 'dropdown', 'minify');
INSERT INTO `##_config` (`config_key`, `config_value`, `group_id`, `sort_order`, `type`, `url`) VALUES ('_SYSTEM_CSS_CACHE_TIME', '423000', 21, 14, NULL, NULL);
INSERT INTO `##_config` (`config_key`, `config_value`, `group_id`, `sort_order`, `type`, `url`) VALUES ('_SYSTEM_JS_CACHE_TIME', '423000', 21, 15, NULL, NULL);
INSERT INTO `##_config` (`config_key`, `config_value`, `group_id`, `sort_order`, `type`, `url`) VALUES ('_SYSTEM_MOD_REWRITE_NO_DUPLICATE_URLS', 'false', 21, 16, 'dropdown', 'conf_truefalse');
INSERT INTO `##_config` (`config_key`, `config_value`, `group_id`, `sort_order`, `type`, `url`) VALUES ('_SYSTEM_HTML_MINIFY_OPTION', '0', 21, 20, 'status', '');

-- stock/shipping
INSERT INTO `##_config` (`config_key`, `config_value`, `group_id`, `sort_order`, `type`, `url`) VALUES ('_SYSTEM_STOCK_HANDLING', 'false', 22, 10, 'dropdown', 'conf_truefalse');
INSERT INTO `##_config` (`config_key`, `config_value`, `group_id`, `sort_order`, `type`, `url`) VALUES ('_SYSTEM_SHIPPING_STATUS', 'false', 22, 11, 'dropdown', 'conf_truefalse');
INSERT INTO `##_config` (`config_key`, `config_value`, `group_id`, `sort_order`, `type`, `url`) VALUES ('_SYSTEM_STOCK_RULES', 'true', 22, 11, 'dropdown', 'conf_truefalse');

-- admin options
INSERT INTO `##_config` (`config_key`, `config_value`, `group_id`, `sort_order`, `type`, `url`) VALUES ('_SYSTEM_METATAGS_WORDS_COUNTER', 'true', 25, 16, 'dropdown', 'conf_truefalse');
INSERT INTO `##_config` (`config_key`, `config_value`, `group_id`, `sort_order`, `type`, `url`) VALUES ('_SYSTEM_ADMIN_FILTER', 'true', 25, 15, 'dropdown', 'conf_truefalse');
INSERT INTO `##_config` (`config_key`, `config_value`, `group_id`, `sort_order`, `type`, `url`) VALUES ('_SYSTEM_FILE_SORT', 'file_asc', 25, 17, 'dropdown', 'file_sort');
INSERT INTO `##_config` (`config_key`, `config_value`, `group_id`, `sort_order`, `type`, `url`) VALUES ('_SYSTEM_SAVEBUTTON_POSITION', 'bottom', 25, 18, 'dropdown', 'savebutton_position');
INSERT INTO `##_config` (`config_key`, `config_value`, `group_id`, `sort_order`, `type`, `url`) VALUES ('_SYSTEM_SHOW_OVERLOAD_MESSAGE', 'true', 25, 18, 'dropdown', 'conf_truefalse');
INSERT INTO `##_config` (`config_key`, `config_value`, `group_id`, `sort_order`, `type`, `url`) VALUES ('_SYSTEM_ADMIN_PAGE_SIZE', '50', 25, 25, NULL, NULL);
INSERT INTO `##_config` (`config_key`, `config_value`, `group_id`, `sort_order`, `type`, `url`) VALUES ('_SYSTEM_ADMIN_PAGE_SIZE_PRODUCT',       '50', 25, 30, NULL, NULL);
INSERT INTO `##_config` (`config_key`, `config_value`, `group_id`, `sort_order`, `type`, `url`) VALUES ('_SYSTEM_ADMIN_PAGE_SIZE_MANUFACTURER',  '25', 25, 35, NULL, NULL);
INSERT INTO `##_config` (`config_key`, `config_value`, `group_id`, `sort_order`, `type`, `url`) VALUES ('_SYSTEM_ADMIN_PAGE_SIZE_ORDER',         '25', 25, 40, NULL, NULL);
INSERT INTO `##_config` (`config_key`, `config_value`, `group_id`, `sort_order`, `type`, `url`) VALUES ('_SYSTEM_ADMIN_PAGE_SIZE_CUSTOMER',      '25', 25, 45, NULL, NULL);
INSERT INTO `##_config` (`config_key`, `config_value`, `group_id`, `sort_order`, `type`, `url`) VALUES ('_SYSTEM_ADMIN_PAGE_SIZE_PLUGIN',        '25', 25, 50, NULL, NULL);
INSERT INTO `##_config` (`config_key`, `config_value`, `group_id`, `sort_order`, `type`, `url`) VALUES ('_SYSTEM_ADMIN_PAGE_SIZE_SEO_DELETED',   '50', 25, 55, NULL, NULL);
INSERT INTO `##_config` (`config_key`, `config_value`, `group_id`, `sort_order`, `type`, `url`) VALUES ('_SYSTEM_ADMIN_PAGE_SIZE_SEO_404',       '50', 25, 60, NULL, NULL);
INSERT INTO `##_config` (`config_key`, `config_value`, `group_id`, `sort_order`, `type`, `url`) VALUES ('_SYSTEM_ADMIN_PAGE_SIZE_SEO_REGENERATE','24', 25, 65, NULL, NULL);
INSERT INTO `##_config` (`config_key`, `config_value`, `group_id`, `sort_order`, `type`, `url`) VALUES ('_SYSTEM_USE_WYSIWYG', 'ckeditor', 25, 10, 'dropdown', 'wysiwyg');
INSERT INTO `##_config` (`config_key`, `config_value`, `group_id`, `sort_order`, `type`, `url`) VALUES ('_SYSTEM_ORDER_EDIT_NEW_ORDER_IN_FRONTEND', 'true', 25, 10, 'dropdown', 'conf_truefalse');
INSERT INTO `##_config` (`config_key`, `config_value`, `group_id`, `sort_order`, `type`, `url`) VALUES ('_SYSTEM_ORDER_EDIT_SHOW_ORDER_EDITOR_COLUMN', 'true', 25, 10, 'dropdown', 'conf_truefalse');
INSERT INTO `##_config` (`config_key`, `config_value`, `group_id`, `sort_order`, `type`, `url`) VALUES ('_SYSTEM_AUTOLOAD', 'false', 25, 5, 'dropdown', 'conf_truefalse');

-- miscellaneous
INSERT INTO `##_config` (`config_key`, `config_value`, `group_id`, `sort_order`, `type`, `url`) VALUES ('_SYSTEM_USE_PRICE', 'true', 23, 12, 'dropdown', 'conf_truefalse');
INSERT INTO `##_config` (`config_key`, `config_value`, `group_id`, `sort_order`, `type`, `url`) VALUES ('_SYSTEM_SRV_WEBROOT_PREFIX', '', 23, 15, '', '');
INSERT INTO `##_config` (`config_key`, `config_value`, `group_id`, `sort_order`, `type`, `url`) VALUES ('_SYSTEM_IMG_QUALITY', '90', 23, 10, NULL, NULL);
INSERT INTO `##_config` (`config_key`, `config_value`, `group_id`, `sort_order`, `type`, `url`) VALUES ('_SYSTEM_PRODUCT_COPY_PREFIX', '-Kopie-', 23, 10, NULL, NULL);
INSERT INTO `##_config` (`config_key`, `config_value`, `group_id`, `sort_order`, `type`, `url`) VALUES ('_SYSTEM_IMG_SHRINK_ONLY', 'true', 23, 10, 'dropdown', 'conf_truefalse');

CREATE TABLE `##_config_1` (
  `id` INT NOT NULL auto_increment,
  `config_key` varchar(64) NOT NULL,
  `config_value` text NOT NULL,
  `group_id` INT NOT NULL,
  `sort_order` INT default NULL,
  `last_modified` datetime NULL DEFAULT NULL ON UPDATE current_timestamp,
  `date_added` datetime NOT NULL DEFAULT current_timestamp,
  `type` varchar(255) default NULL,
  `url` varchar(255) default NULL,
  PRIMARY KEY  (`id`),
  KEY `idx_configuration_group_id` (`group_id`)
) ENGINE=#E_ DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


-- general settings group 1
INSERT INTO `##_config_1` (`config_key`, `config_value`, `group_id`, `sort_order`, `type`, `url`) VALUES ('_STORE_LOGO', 'logo.png', 1, 12, 'dropdown', 'conf_storelogo');
INSERT INTO `##_config_1` (`config_key`, `config_value`, `group_id`, `sort_order`, `type`, `url`) VALUES ('_STORE_FAVICON', 'favicon', 1, 15, NULL, NULL);
INSERT INTO `##_config_1` (`config_key`, `config_value`, `group_id`, `sort_order`, `type`, `url`) VALUES ('_STORE_ZONE', '31', 1, 18, 'dropdown', 'tax_zones');
INSERT INTO `##_config_1` (`config_key`, `config_value`, `group_id`, `sort_order`, `type`, `url`) VALUES ('_STORE_CURRENCY', 'EUR', 1, 30, 'dropdown', 'currencies');
INSERT INTO `##_config_1` (`config_key`, `config_value`, `group_id`, `sort_order`, `type`, `url`) VALUES ('_STORE_LANGUAGE', 'de', 1, 40, 'dropdown', 'language_codes');
INSERT INTO `##_config_1` (`config_key`, `config_value`, `group_id`, `sort_order`, `type`, `url`) VALUES ('_STORE_CAPTCHA', 'Standard', 1, 80, 'dropdown', 'captcha');
INSERT INTO `##_config_1` (`config_key`, `config_value`, `group_id`, `sort_order`, `type`, `url`) VALUES ('_STORE_CUSTOMERS_STATUS_ID_GUEST', '1', 1, 90, 'dropdown', 'customers_status');
INSERT INTO `##_config_1` (`config_key`, `config_value`, `group_id`, `sort_order`, `type`, `url`) VALUES ('_STORE_CUSTOMERS_STATUS_ID', '2', 1, 91, 'dropdown', 'customers_status');
INSERT INTO `##_config_1` (`config_key`, `config_value`, `group_id`, `sort_order`, `type`, `url`) VALUES ('_STORE_DEFAULT_ORDER_STATUS', '16', 1, 95, 'dropdown', 'order_status');
INSERT INTO `##_config_1` (`config_key`, `config_value`, `group_id`, `sort_order`, `type`, `url`) VALUES ('_STORE_ALLOW_DECIMAL_QUANTITIY', 'false', 1, 100, 'dropdown', 'conf_truefalse');
INSERT INTO `##_config_1` (`config_key`, `config_value`, `group_id`, `sort_order`, `type`, `url`) VALUES ('_STORE_TERMSCOND_CHECK', 'false', 1, 110,  'dropdown', 'conf_truefalse');
INSERT INTO `##_config_1` (`config_key`, `config_value`, `group_id`, `sort_order`, `type`, `url`) VALUES ('_STORE_DIGITALCOND_CHECK', 'true', 1, 120,  'dropdown', 'conf_truefalse');
INSERT INTO `##_config_1` (`config_key`, `config_value`, `group_id`, `sort_order`, `type`, `url`) VALUES ('_STORE_PRODUCT_DOWNLOAD_PUBLIC_ALLOWED', 'true', 1, 130,  'dropdown', 'conf_truefalse');
INSERT INTO `##_config_1` (`config_key`, `config_value`, `group_id`, `sort_order`, `type`, `url`) VALUES ('_STORE_DEFAULT_PRODUCT_CONDITION', 'NewCondition', 1, 150,  'dropdown', 'products_conditions');

-- customer options group 5
INSERT INTO `##_config_1` (`config_key`, `config_value`, `group_id`, `sort_order`, `type`, `url`) VALUES ('_STORE_ACCOUNT_GENDER', 'true', 5, 0, 'dropdown', 'conf_truefalse');
INSERT INTO `##_config_1` (`config_key`, `config_value`, `group_id`, `sort_order`, `type`, `url`) VALUES ('_STORE_ACCOUNT_SALUTATION_PRESET', '', 5, 5, NULL, NULL);
INSERT INTO `##_config_1` (`config_key`, `config_value`, `group_id`, `sort_order`, `type`, `url`) VALUES ('_STORE_ACCOUNT_USE_TITLE', 1, 5, 10, 'status', NULL);
INSERT INTO `##_config_1` (`config_key`, `config_value`, `group_id`, `sort_order`, `type`, `url`) VALUES ('_STORE_ACCOUNT_TITLE_REQUIRED', 0, 5, 15, 'status', NULL);
INSERT INTO `##_config_1` (`config_key`, `config_value`, `group_id`, `sort_order`, `type`, `url`) VALUES ('_STORE_ACCOUNT_TITLE_PRESET', '', 5, 20, 'textarea', NULL);
INSERT INTO `##_config_1` (`config_key`, `config_value`, `group_id`, `sort_order`, `type`, `url`) VALUES ('_STORE_FIRST_NAME_MIN_LENGTH', '2', 5, 25, NULL, NULL);
INSERT INTO `##_config_1` (`config_key`, `config_value`, `group_id`, `sort_order`, `type`, `url`) VALUES ('_STORE_LAST_NAME_MIN_LENGTH', '2', 5, 30, NULL, NULL);
INSERT INTO `##_config_1` (`config_key`, `config_value`, `group_id`, `sort_order`, `type`, `url`) VALUES ('_STORE_ACCOUNT_DOB', 'true', 5, 35, 'dropdown', 'conf_truefalse');
INSERT INTO `##_config_1` (`config_key`, `config_value`, `group_id`, `sort_order`, `type`, `url`) VALUES ('_STORE_ACCOUNT_MIN_AGE', '', 5, 40, NULL, NULL);
INSERT INTO `##_config_1` (`config_key`, `config_value`, `group_id`, `sort_order`, `type`, `url`) VALUES ('_STORE_ACCOUNT_DOB_FORMAT', 'dd.mm.yyyy', 5, 45, 'dropdown', 'date_format');
INSERT INTO `##_config_1` (`config_key`, `config_value`, `group_id`, `sort_order`, `type`, `url`) VALUES ('_STORE_ACCOUNT_DOB_PRESET', '', 5, 50, NULL, NULL);
INSERT INTO `##_config_1` (`config_key`, `config_value`, `group_id`, `sort_order`, `type`, `url`) VALUES ('_STORE_EMAIL_ADDRESS_MIN_LENGTH', '6', 5, 55, NULL, NULL);
INSERT INTO `##_config_1` (`config_key`, `config_value`, `group_id`, `sort_order`, `type`, `url`) VALUES ('_STORE_ACCOUNT_COMPANY', 'true', 5, 60, 'dropdown', 'conf_truefalse');
INSERT INTO `##_config_1` (`config_key`, `config_value`, `group_id`, `sort_order`, `type`, `url`) VALUES ('_STORE_COMPANY_MIN_LENGTH', '0', 5, 65, NULL, NULL);
INSERT INTO `##_config_1` (`config_key`, `config_value`, `group_id`, `sort_order`, `type`, `url`) VALUES ('_STORE_ACCOUNT_COMPANY_VAT_CHECK', 'true', 5, 70, 'dropdown', 'conf_truefalse');
INSERT INTO `##_config_1` (`config_key`, `config_value`, `group_id`, `sort_order`, `type`, `url`) VALUES ('_STORE_STREET_ADDRESS_MIN_LENGTH', '5', 5, 75, NULL, NULL);
INSERT INTO `##_config_1` (`config_key`, `config_value`, `group_id`, `sort_order`, `type`, `url`) VALUES ('_STORE_ACCOUNT_SUBURB', 'false', 5, 80, 'dropdown', 'conf_truefalse');
INSERT INTO `##_config_1` (`config_key`, `config_value`, `group_id`, `sort_order`, `type`, `url`) VALUES ('_STORE_POSTCODE_MIN_LENGTH', '4', 5, 85, NULL, NULL);
INSERT INTO `##_config_1` (`config_key`, `config_value`, `group_id`, `sort_order`, `type`, `url`) VALUES ('_STORE_CITY_MIN_LENGTH', '3', 5, 90, NULL, NULL);
INSERT INTO `##_config_1` (`config_key`, `config_value`, `group_id`, `sort_order`, `type`, `url`) VALUES ('_STORE_ACCOUNT_FEDERAL_STATES', 'true', 5, 95,  'dropdown', 'conf_truefalse');
INSERT INTO `##_config_1` (`config_key`, `config_value`, `group_id`, `sort_order`, `type`, `url`) VALUES ('_STORE_SHOW_PHONE_PREFIX', 'false', 5, 100,  'dropdown', 'conf_truefalse');
INSERT INTO `##_config_1` (`config_key`, `config_value`, `group_id`, `sort_order`, `type`, `url`) VALUES ('_STORE_TELEPHONE_MIN_LENGTH', '0', 5, 105, NULL, NULL);
INSERT INTO `##_config_1` (`config_key`, `config_value`, `group_id`, `sort_order`, `type`, `url`) VALUES ('_STORE_MOBILE_PHONE_MIN_LENGTH', '0', 5, 110, NULL, NULL);
INSERT INTO `##_config_1` (`config_key`, `config_value`, `group_id`, `sort_order`, `type`, `url`) VALUES ('_STORE_FAX_MIN_LENGTH', '0', 5, 115, NULL, NULL);
INSERT INTO `##_config_1` (`config_key`, `config_value`, `group_id`, `sort_order`, `type`, `url`) VALUES ('_STORE_PASSWORD_MIN_LENGTH', '5', 5, 120, NULL, NULL);
INSERT INTO `##_config_1` (`config_key`, `config_value`, `group_id`, `sort_order`, `type`, `url`) VALUES ('_STORE_ALLOW_GUEST_ORDERS', 'true', 5, 125,  'dropdown', 'conf_truefalse');
INSERT INTO `##_config_1` (`config_key`, `config_value`, `group_id`, `sort_order`, `type`, `url`) VALUES ('_STORE_ACCOUNT_SHOW_GDPR_DOWNLOAD', 0, 5, 130, 'status', NULL);
INSERT INTO `##_config_1` (`config_key`, `config_value`, `group_id`, `sort_order`, `type`, `url`) VALUES ('_STORE_ADDRESS_BOOK_ENTRIES', '5', 5, 135, NULL, NULL);

-- vat options group 6
INSERT INTO `##_config_1` (`config_key`, `config_value`, `group_id`, `sort_order`, `type`, `url`) VALUES ('_STORE_VAT_CHECK_TYPE', 'format', 6, 3, 'dropdown', 'vat_check');
INSERT INTO `##_config_1` (`config_key`, `config_value`, `group_id`, `sort_order`, `type`, `url`) VALUES ('_STORE_VAT_CHECK_MOVE', 'false', 6, 3, 'dropdown', 'conf_truefalse');
INSERT INTO `##_config_1` (`config_key`, `config_value`, `group_id`, `sort_order`, `type`, `url`) VALUES ('_STORE_VAT_CHECK_STATUS_IN', '3', 6, 3, 'dropdown', 'customers_status');
INSERT INTO `##_config_1` (`config_key`, `config_value`, `group_id`, `sort_order`, `type`, `url`) VALUES ('_STORE_VAT_CHECK_STATUS_OUT', '4', 6, 3, 'dropdown', 'customers_status');

INSERT INTO `##_config_1` (`config_key`, `config_value`, `group_id`, `sort_order`, `type`, `url`) VALUES ('_STORE_VAT_CHANGE_CLIENT_GROUP_ON_VAT_CHANGE', 'false', 6, 3, 'dropdown', 'conf_truefalse');

-- shipping options group 7
INSERT INTO `##_config_1` (`config_key`, `config_value`, `group_id`, `sort_order`, `type`, `url`) VALUES ('_STORE_STOCK_CHECK_DISPLAY', 'true', 7, 3, 'dropdown', 'conf_truefalse');
INSERT INTO `##_config_1` (`config_key`, `config_value`, `group_id`, `sort_order`, `type`, `url`) VALUES ('_STORE_STOCK_CHECK_BUY', 'false', 7, 3, 'dropdown', 'conf_truefalse');

-- product listing options group 8
INSERT INTO `##_config_1` (`config_key`, `config_value`, `group_id`, `sort_order`, `type`, `url`) VALUES ('_STORE_SEARCH_RESULTS', '20', 8, 2, NULL, NULL);
INSERT INTO `##_config_1` (`config_key`, `config_value`, `group_id`, `sort_order`, `type`, `url`) VALUES ('_STORE_PRODUCT_LIST_RESULTS', '20', 8, 2, NULL, NULL);
INSERT INTO `##_config_1` (`config_key`, `config_value`, `group_id`, `sort_order`, `type`, `url`) VALUES ('_STORE_MAX_CATEGORIES_PER_ROW', '3', 8, NULL, NULL, NULL);
INSERT INTO `##_config_1` (`config_key`, `config_value`, `group_id`, `sort_order`, `type`, `url`) VALUES ('_STORE_TEMPLATE_PRODUCT_LISTING', 'product_listing_v1.html', 8, 120, 'dropdown', 'listing_template');
INSERT INTO `##_config_1` (`config_key`, `config_value`, `group_id`, `sort_order`, `type`, `url`) VALUES ('_STORE_TEMPLATE_CATEGORY_LISTING', 'categorie_listing.html', 8, 120, 'dropdown', 'categories_template');
INSERT INTO `##_config_1` (`config_key`, `config_value`, `group_id`, `sort_order`, `type`, `url`) VALUES ('_STORE_TEMPLATE_PRODUCT_SEARCH_RESULT', 'product_listing_v1.html', 8, 120, 'dropdown', 'listing_template');
INSERT INTO `##_config_1` (`config_key`, `config_value`, `group_id`, `sort_order`, `type`, `url`) VALUES ('_STORE_TEMPLATE_PRODUCT_LISTING_MANUFACTURERS', 'product_listing_v1.html', 8, 120, 'dropdown', 'listing_template');


-- email options group 12
INSERT INTO `##_config_1` (`config_key`, `config_value`, `group_id`, `sort_order`, `type`, `url`) VALUES ('_STORE_SMTP_HOST', 'localhost', 12, 3, NULL, NULL);
INSERT INTO `##_config_1` (`config_key`, `config_value`, `group_id`, `sort_order`, `type`, `url`) VALUES ('_STORE_SMTP_PORT', '25', 12, 4, NULL, NULL);
INSERT INTO `##_config_1` (`config_key`, `config_value`, `group_id`, `sort_order`, `type`, `url`) VALUES ('_STORE_SMTP_USERNAME', '', 12, 6, NULL, NULL);
INSERT INTO `##_config_1` (`config_key`, `config_value`, `group_id`, `sort_order`, `type`, `url`) VALUES ('_STORE_SMTP_PASSWORD', '', 12, 7, 'password', NULL);
INSERT INTO `##_config_1` (`config_key`, `config_value`, `group_id`, `sort_order`, `type`, `url`) VALUES ('_STORE_CONTACT_EMAIL', '', 12, 8, NULL, NULL);
INSERT INTO `##_config_1` (`config_key`, `config_value`, `group_id`, `sort_order`, `type`, `url`) VALUES ('_STORE_SMTP_AUTH', 'true', 12, 8, 'dropdown', 'conf_truefalse');
INSERT INTO `##_config_1` (`config_key`, `config_value`, `group_id`, `sort_order`, `type`, `url`) VALUES ('_STORE_SMTP_SECURE', 'tls', 12, 9, 'dropdown', 'smtp_secure');


-- meta tags options group 16
INSERT INTO `##_config_1` (`config_key`, `config_value`, `group_id`, `sort_order`, `type`, `url`) VALUES ('_STORE_META_ROBOTS', 'index,follow', 16, 10, 'textfield', NULL);
INSERT INTO `##_config_1` (`config_key`, `config_value`, `group_id`, `sort_order`, `type`, `url`) VALUES ('_STORE_META_PAGINATION_ROBOTS','false', 16, 13, 'dropdown', 'conf_truefalse');
INSERT INTO `##_config_1` (`config_key`, `config_value`, `group_id`, `sort_order`, `type`, `url`) VALUES ('_STORE_HREFLANG_DEFAULT','de', 16, 20, 'dropdown', 'language_codes');

-- template options group 18
INSERT INTO `##_config_1` (`config_key`, `config_value`, `group_id`, `sort_order`, `type`, `url`) VALUES ('_STORE_DEFAULT_TEMPLATE', 'ew_evelations', 18, 26, 'dropdown', 'templateSets');
INSERT INTO `##_config_1` (`config_key`, `config_value`, `group_id`, `sort_order`, `type`, `url`) VALUES ('_STORE_META_DOCTYPE_HTML', 'html5', 18, 30, NULL, NULL);
INSERT INTO `##_config_1` (`config_key`, `config_value`, `group_id`, `sort_order`, `type`, `url`) VALUES ('_STORE_GOTO_CART_DIRECTLY', 0, 18, 35, 'status', NULL);
INSERT INTO `##_config_1` (`config_key`, `config_value`, `group_id`, `sort_order`, `type`, `url`) VALUES ('_STORE_STAY_ON_PAGE_AFTER_CUSTOMER_LOGIN', 1, 18, 40, 'status', NULL);


-- shopowner infos group 26
INSERT INTO `##_config_1` (`config_key`, `config_value`, `group_id`, `sort_order`, `type`, `url`) VALUES ('_STORE_SHOPOWNER_COMPANY','', 26, 1, NULL, NULL);
INSERT INTO `##_config_1` (`config_key`, `config_value`, `group_id`, `sort_order`, `type`, `url`) VALUES ('_STORE_SHOPOWNER_CEO','', 26, 2, NULL, NULL);
INSERT INTO `##_config_1` (`config_key`, `config_value`, `group_id`, `sort_order`, `type`, `url`) VALUES ('_STORE_SHOPOWNER_STREETADDRESS','', 26, 4, NULL, NULL);
INSERT INTO `##_config_1` (`config_key`, `config_value`, `group_id`, `sort_order`, `type`, `url`) VALUES ('_STORE_SHOPOWNER_CITY','', 26, 5, NULL, NULL);
INSERT INTO `##_config_1` (`config_key`, `config_value`, `group_id`, `sort_order`, `type`, `url`) VALUES ('_STORE_SHOPOWNER_ZIP','', 26, 6, NULL, NULL);
INSERT INTO `##_config_1` (`config_key`, `config_value`, `group_id`, `sort_order`, `type`, `url`) VALUES ('_STORE_COUNTRY', 'DE', 26, 7, 'dropdown', 'countries');
INSERT INTO `##_config_1` (`config_key`, `config_value`, `group_id`, `sort_order`, `type`, `url`) VALUES ('_STORE_SHOPOWNER_TELEPHONE','', 26, 8, NULL, NULL);
INSERT INTO `##_config_1` (`config_key`, `config_value`, `group_id`, `sort_order`, `type`, `url`) VALUES ('_STORE_SHOPOWNER_FAX','', 26, 9, NULL, NULL);
INSERT INTO `##_config_1` (`config_key`, `config_value`, `group_id`, `sort_order`, `type`, `url`) VALUES ('_STORE_SHOPOWNER_VATID','', 26, 10, NULL, NULL);
INSERT INTO `##_config_1` (`config_key`, `config_value`, `group_id`, `sort_order`, `type`, `url`) VALUES ('_STORE_SHOPOWNER_REGISTERID','', 26, 11, NULL, NULL);
INSERT INTO `##_config_1` (`config_key`, `config_value`, `group_id`, `sort_order`, `type`, `url`) VALUES ('_STORE_SHOPOWNER_REGISTER_COURT','', 26, 12, NULL, NULL);

-- order edit group 28
INSERT INTO `##_config_1` (`config_key`, `config_value`, `group_id`, `sort_order`, `type`, `url`) VALUES ('_SYSTEM_ORDER_EDIT_USE_CUSTOMER_CURRENCY', 'true', 28, 10, 'dropdown', 'conf_truefalse');
INSERT INTO `##_config_1` (`config_key`, `config_value`, `group_id`, `sort_order`, `type`, `url`) VALUES ('_SYSTEM_ORDER_EDIT_ALLOW_NEGATIVE_STOCK', 'true', 28, 10, 'dropdown', 'conf_truefalse');
INSERT INTO `##_config_1` (`config_key`, `config_value`, `group_id`, `sort_order`, `type`, `url`) VALUES ('_SYSTEM_ORDER_EDIT_NEW_ORDER_PAYMENT', 'xt_prepayment', 28, 10, 'dropdown', 'order_edit_payment_methods');
INSERT INTO `##_config_1` (`config_key`, `config_value`, `group_id`, `sort_order`, `type`, `url`) VALUES ('_SYSTEM_ORDER_EDIT_NEW_ORDER_SHIPPING', 'Standard', 28, 10, 'dropdown', 'shipping_methods');
INSERT INTO `##_config_1` (`config_key`, `config_value`, `group_id`, `sort_order`, `type`, `url`) VALUES ('_SYSTEM_ORDER_EDIT_NEW_ORDER_ORDER_SOURCE', '1', 28, 10, 'dropdown', 'order_sources');

CREATE TABLE `##_config_group` (
  `group_id` INT NOT NULL auto_increment,
  `group_title` varchar(64) NOT NULL,
  `group_icon` varchar(64) default NULL,
  `sort_order` INT default NULL,
  `visible` TINYINT UNSIGNED NOT NULL DEFAULT 1,
  `iconCls` varchar(25) default NULL,
  `url_h` varchar(512) default NULL,
  PRIMARY KEY  (`group_id`),
  UNIQUE KEY `group_title` (`group_title`)
) ENGINE=#E_ DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

INSERT INTO `##_config_group` VALUES (1, 'TEXT_MY_STORE', 'wrench_orange.png', 1, 1, NULL, 'https://xtcommerce.atlassian.net/wiki/spaces/MANUAL/pages/917671');
INSERT INTO `##_config_group` VALUES (6, 'TEXT_VAT_ID_OPTIONS', 'tag_green.png', 6, 1, NULL, 'https://xtcommerce.atlassian.net/wiki/spaces/MANUAL/pages/917670');
INSERT INTO `##_config_group` VALUES (7, 'TEXT_STOCK_OPTIONS', 'lorry.png', 7, 1, NULL, 'https://xtcommerce.atlassian.net/wiki/spaces/MANUAL/pages/917673');
INSERT INTO `##_config_group` VALUES (5, 'TEXT_CUSTOMER_DETAILS', 'vcard.png', 5, 1, NULL, 'https://xtcommerce.atlassian.net/wiki/spaces/MANUAL/pages/917672');
INSERT INTO `##_config_group` VALUES (8, 'TEXT_PRODUCT_LISTING', 'table.png', 8, 1, NULL, 'https://xtcommerce.atlassian.net/wiki/spaces/MANUAL/pages/917665');
INSERT INTO `##_config_group` VALUES (12, 'TEXT_EMAIL_OPTIONS', 'email_open.png', 12, 1, NULL, 'https://xtcommerce.atlassian.net/wiki/spaces/MANUAL/pages/917666');
INSERT INTO `##_config_group` VALUES (15, 'TEXT_CONF_TEMPLATES_NAV', 'application_view_gallery.png', 1, 2, NULL, 'https://xtcommerce.atlassian.net/wiki/spaces/MANUAL/pages/9961510');
INSERT INTO `##_config_group` VALUES (16, 'TEXT_SEARCH_ENGINES', 'ipod_cast.png', 16, 1, NULL, 'https://xtcommerce.atlassian.net/wiki/spaces/MANUAL/pages/917668');
INSERT INTO `##_config_group` VALUES (17, 'TEXT_CONF_PERMISSIONS', 'shield.png', 17, 2, NULL, 'https://xtcommerce.atlassian.net/wiki/spaces/MANUAL/pages/917603');
INSERT INTO `##_config_group` VALUES (18, 'TEXT_TEMPLATES_NAV', 'application_view_gallery.png', 1, 1, NULL, 'https://xtcommerce.atlassian.net/wiki/spaces/MANUAL/pages/9961510');
INSERT INTO `##_config_group` VALUES (19, 'TEXT_CONF_MAIL', 'email_go.png', 19, 2, NULL, 'https://xtcommerce.atlassian.net/wiki/spaces/MANUAL/pages/917606');
INSERT INTO `##_config_group` VALUES (20, 'TEXT_CONF_PERFORMANCE', 'database_lightning.png', 20, 0, NULL, 'https://xtcommerce.atlassian.net/wiki/spaces/MANUAL/pages/917615');
INSERT INTO `##_config_group` VALUES (21, 'TEXT_CONF_SEO', 'page_link.png', 21, 0, NULL, '');
INSERT INTO `##_config_group` VALUES (22, 'TEXT_CONF_STOCK', 'layers.png', 22, 2, NULL, 'https://xtcommerce.atlassian.net/wiki/spaces/MANUAL/pages/917533');
INSERT INTO `##_config_group` VALUES (23, 'TEXT_CONF_OTHER', 'application_osx.png', 23, 2, NULL, 'https://xtcommerce.atlassian.net/wiki/spaces/MANUAL/pages/917616');
INSERT INTO `##_config_group` VALUES (25, 'TEXT_CONF_ADMIN_OPTIONS_NAV', 'application_view_tile.png', 25, 2, NULL, 'https://xtcommerce.atlassian.net/wiki/spaces/MANUAL/pages/28770309');
INSERT INTO `##_config_group` VALUES (26, 'TEXT_MY_STORE_INFO', 'building_edit.png', 2, 1, NULL, 'https://xtcommerce.atlassian.net/wiki/spaces/MANUAL/pages/95354918');
INSERT INTO `##_config_group` VALUES (28, 'TEXT_ORDERS_EDIT', 'basket_edit.png', 2, 1, NULL, 'https://xtcommerce.atlassian.net/wiki/spaces/MANUAL/pages/40960036');

CREATE TABLE `##_config_payment` (
  `id` INT NOT NULL auto_increment,
  `config_key` varchar(64) NOT NULL,
  `config_value` text NOT NULL,
  `group_id` INT NOT NULL,
  `sort_order` INT default NULL,
  `last_modified` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp,
  `date_added` datetime NOT NULL DEFAULT current_timestamp,
  `type` varchar(255) default NULL,
  `url` varchar(255) default NULL,
  `payment_id` INT default NULL,
  `shop_id` INT NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `config_key` (`config_key`,`shop_id`),
  KEY `idx_configuration_group_id` (`group_id`)
) ENGINE=#E_ DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


CREATE TABLE IF NOT EXISTS `##_config_plugin` (
  `id` INT NOT NULL auto_increment,
  `config_key` varchar(255) default NULL,
  `config_value` text,
  `plugin_id` INT NOT NULL,
  `type` varchar(255) default NULL,
  `url` varchar(255) default NULL,
  `group_id` INT default '0',
  `shop_id` INT NOT NULL,
  `last_modified` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp,
  `date_added` datetime NOT NULL DEFAULT current_timestamp,
  `sort_order` INT NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `config_key` (`config_key`,`shop_id`)
) ENGINE=#E_ DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


CREATE TABLE `##_content` (
  `content_id` INT NOT NULL auto_increment,
  `content_parent` INT NOT NULL default '0',
  `content_status` TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `content_hook` INT NOT NULL,
  `content_form` varchar(64) default NULL,
  `content_image` varchar(255) default NULL,
  `content_sort` INT NOT NULL DEFAULT 0,
  PRIMARY KEY  (`content_id`)
) ENGINE=#E_ DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


INSERT INTO `##_content` VALUES (1, 0, 1, 1, 'shipping.php','',0);
INSERT INTO `##_content` VALUES (2, 0, 1, 2, '0','',0);
INSERT INTO `##_content` VALUES (3, 0, 1, 3, '0','',0);
INSERT INTO `##_content` VALUES (4, 0, 1, 0, '0','',0);
INSERT INTO `##_content` VALUES (5, 0, 1, 4, '0','',0);
INSERT INTO `##_content` VALUES (6, 0, 1, 5, 'contact.php','',0);
INSERT INTO `##_content` VALUES (7, 0, 1, 8, '0','',0);
INSERT INTO `##_content` VALUES (8, 0, 1, 0, '0','',0);
INSERT INTO `##_content` VALUES (10, 0, 1, 0, '0','',0);

CREATE TABLE `##_content_block` (
  `block_id` INT NOT NULL auto_increment,
  `block_tag` varchar(64) NOT NULL,
  `block_status` TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `block_protected` TINYINT UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY  (`block_id`)
) ENGINE=#E_ DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


INSERT INTO `##_content_block` VALUES (1, 'shipping', 1, 1);
INSERT INTO `##_content_block` VALUES (2, 'privacy', 1, 1);
INSERT INTO `##_content_block` VALUES (3, 'conditions', 1, 1);
INSERT INTO `##_content_block` VALUES (4, 'startpage', 1, 1);
INSERT INTO `##_content_block` VALUES (5, 'contact', 1, 1);
INSERT INTO `##_content_block` VALUES (6, 'footer', 1, 0);
INSERT INTO `##_content_block` VALUES (7, 'information', 1, 0);
INSERT INTO `##_content_block` VALUES (8, 'rescission', 1, 1);
INSERT INTO `##_content_block` VALUES (9, 'content', 1, 0);

CREATE TABLE `##_content_elements` (
  `content_id` INT NOT NULL,
  `language_code` char(2) NOT NULL,
  `content_title` varchar(255) default NULL,
  `content_heading` varchar(255)  default NULL,
  `content_body` mediumtext,
  `content_body_short` text,
  `content_file` varchar(255) default NULL,
  `content_store_id` INT NOT NULL,
  PRIMARY KEY  (`content_id`,`language_code`,`content_store_id`)
) ENGINE=#E_ DEFAULT CHARSET=utf8mb4;


CREATE TABLE `##_content_permission` (
  `pid` INT NOT NULL,
  `permission` TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `type` varchar(32) NOT NULL,
  `pgroup` varchar(255) NOT NULL,
  PRIMARY KEY  (`pid`,`type`,`pgroup`)
) ENGINE=#E_ DEFAULT CHARSET=utf8;


CREATE TABLE `##_content_to_block` (
  `id` INT NOT NULL auto_increment,
  `block_id` INT default NULL,
  `content_id` INT default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=#E_ DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


INSERT INTO `##_content_to_block` VALUES (1, 7, 1);
INSERT INTO `##_content_to_block` VALUES (2, 7, 2);
INSERT INTO `##_content_to_block` VALUES (3, 7, 3);
INSERT INTO `##_content_to_block` VALUES (4, 7, 4);
INSERT INTO `##_content_to_block` VALUES (14, 7, 7);
INSERT INTO `##_content_to_block` VALUES (7, 6, 1);
INSERT INTO `##_content_to_block` VALUES (8, 6, 2);
INSERT INTO `##_content_to_block` VALUES (9, 6, 3);
INSERT INTO `##_content_to_block` VALUES (10, 6, 4);
INSERT INTO `##_content_to_block` VALUES (13, 6, 7);
INSERT INTO `##_content_to_block` VALUES (15, 9, 8);
INSERT INTO `##_content_to_block` VALUES (16, 6, 10);
INSERT INTO `##_content_to_block` VALUES (17, 7, 10);


CREATE TABLE `##_countries` (
  `countries_iso_code_2` char(2) NOT NULL,
  `countries_iso_code_3` char(3) NOT NULL,
  `status` TINYINT UNSIGNED NOT NULL DEFAULT 1,
  `zone_id` INT default NULL,
  `phone_prefix` VARCHAR( 32 ) NOT NULL,
  PRIMARY KEY  (`countries_iso_code_2`)
) ENGINE=#E_ DEFAULT CHARSET=utf8;

INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('AD', 'AND', 1, 30, '00376');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('AE', 'ARE', 1, 24, '00971');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('AF', 'AFG', 1, 24, '0093');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('AG', 'ATG', 1, 26, '001268');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('AI', 'AIA', 1, 26, '001264');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('AL', 'ALB', 1, 30, '00355');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('AM', 'ARM', 1, 24, '00374');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('AN', 'ANT', 1, 0, '00599');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('AO', 'AGO', 1, 25, '00244');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('AQ', 'ATA', 1, 0, '');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('AR', 'ARG', 1, 27, '0054');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('AS', 'ASM', 1, 0, '001684');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('AT', 'AUT', 1, 31, '0043');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('AU', 'AUS', 1, 29, '0061');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('AW', 'ABW', 1, 26, '00297');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('AZ', 'AZE', 1, 24, '00994');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('BA', 'BIH', 1, 30, '00387');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('BB', 'BRB', 1, 26, '001246');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('BD', 'BGD', 1, 24, '00880');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('BE', 'BEL', 1, 31, '0032');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('BF', 'BFA', 1, 25, '00226');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('BG', 'BGR', 1, 31, '00359');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('BH', 'BHR', 1, 24, '00973');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('BI', 'BDI', 1, 25, '00257');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('BJ', 'BEN', 1, 25, '00229');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('BM', 'BMU', 1, 26, '001441');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('BN', 'BRN', 1, 24, '00673');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('BO', 'BOL', 1, 27, '00591');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('BR', 'BRA', 1, 27, '0055');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('BS', 'BHS', 1, 26, '001242');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('BT', 'BTN', 1, 24, '00975');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('BV', 'BVT', 1, 0, '');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('BW', 'BWA', 1, 25, '00267');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('BY', 'BLR', 1, 30, '00375');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('BZ', 'BLZ', 1, 26, '00501');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('CA', 'CAN', 1, 26, '001');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('CC', 'CCK', 1, 0, '006189162');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('CF', 'CAF', 1, 25, '00236');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('CG', 'COG', 1, 25, '00242');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('CH', 'CHE', 1, 30, '0041');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('CI', 'CIV', 1, 25, '00225');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('CK', 'COK', 1, 0, '00682');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('CL', 'CHL', 1, 27, '0056');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('CM', 'CMR', 1, 25, '00237');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('CN', 'CHN', 1, 24, '0086');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('CO', 'COL', 1, 27, '0057');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('CR', 'CRI', 1, 26, '00506');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('CU', 'CUB', 1, 26, '0053');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('CV', 'CPV', 1, 25, '00238');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('CX', 'CXR', 1, 0, '006189164');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('CY', 'CYP', 1, 31, '00357');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('CZ', 'CZE', 1, 31, '00420');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('DE', 'DEU', 1, 31, '0049');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('DJ', 'DJI', 1, 25, '00253');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('DK', 'DNK', 1, 31, '0045');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('DM', 'DMA', 1, 26, '001767');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('DO', 'DOM', 1, 26, '001809,001829,001849');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('DZ', 'DZA', 1, 25, '00213');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('EC', 'ECU', 1, 27, '00593');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('EE', 'EST', 1, 31, '00372');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('EG', 'EGY', 1, 25, '0020');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('EH', 'ESH', 1, 25, '002125288,002125289');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('ER', 'ERI', 1, 25, '00291');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('ES', 'ESP', 1, 31, '0034');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('ET', 'ETH', 1, 25, '00251');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('FI', 'FIN', 1, 31, '00358');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('FJ', 'FJI', 1, 0, '00679');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('FK', 'FLK', 1, 27, '00500');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('FM', 'FSM', 1, 0, '00691');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('FO', 'FRO', 1, 0, '00298');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('FR', 'FRA', 1, 31, '0033');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('FX', 'FXX', 1, 0, '');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('GA', 'GAB', 1, 25, '00241');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('GB', 'GBR', 1, 30, '0044');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('GD', 'GRD', 1, 26, '001473');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('GE', 'GEO', 1, 24, '00995');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('GF', 'GUF', 1, 27, '00594');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('GH', 'GHA', 1, 25, '00233');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('GI', 'GIB', 1, 0, '00350');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('GL', 'GRL', 1, 26, '00299');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('GM', 'GMB', 1, 25, '00220');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('GN', 'GIN', 1, 25, '00224');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('GP', 'GLP', 1, 26, '00590');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('GQ', 'GNQ', 1, 25, '00240');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('GR', 'GRC', 1, 31, '0030');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('GS', 'SGS', 1, 27, '00500');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('GT', 'GTM', 1, 26, '00502');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('GU', 'GUM', 1, 0, '001671');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('GW', 'GNB', 1, 25, '00245');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('GY', 'GUY', 1, 27, '00592');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('HK', 'HKG', 1, 24, '00852');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('HM', 'HMD', 1, 0, '');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('HN', 'HND', 1, 26, '00504');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('HR', 'HRV', 1, 31, '00385');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('HT', 'HTI', 1, 26, '00509');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('HU', 'HUN', 1, 31, '0036');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('ID', 'IDN', 1, 24, '0062');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('IE', 'IRL', 1, 31, '00353');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('IL', 'ISR', 1, 24, '00972');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('IN', 'IND', 1, 24, '0091');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('IO', 'IOT', 1, 0, '00246');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('IQ', 'IRQ', 1, 24, '00964');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('IR', 'IRN', 1, 24, '0098');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('IS', 'ISL', 1, 30, '00354');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('IT', 'ITA', 1, 31, '0039');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('JM', 'JAM', 1, 26, '001876');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('JO', 'JOR', 1, 24, '00962');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('JP', 'JPN', 1, 24, '0081');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('KE', 'KEN', 1, 25, '00254');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('KG', 'KGZ', 1, 24, '00996');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('KH', 'KHM', 1, 24, '00855');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('KI', 'KIR', 1, 0, '00686');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('KM', 'COM', 1, 25, '00269');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('KN', 'KNA', 1, 26, '001869');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('KP', 'PRK', 1, 24, '00850');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('KR', 'KOR', 1, 24, '0082');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('KW', 'KWT', 1, 24, '00965');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('KY', 'CYM', 1, 26, '001345');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('KZ', 'KAZ', 1, 24, '007');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('LA', 'LAO', 1, 24, '00856');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('LB', 'LBN', 1, 24, '00961');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('LC', 'LCA', 1, 26, '001758');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('LI', 'LIE', 1, 30, '00423');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('LK', 'LKA', 1, 24, '0094');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('LR', 'LBR', 1, 25, '00231');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('LS', 'LSO', 1, 25, '00266');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('LT', 'LTU', 1, 31, '00370');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('LU', 'LUX', 1, 31, '00352');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('LV', 'LVA', 1, 31, '00371');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('LY', 'LBY', 1, 25, '00218');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('MA', 'MAR', 1, 25, '00212');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('MC', 'MCO', 1, 30, '00377');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('MD', 'MDA', 1, 0, '00373');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('MG', 'MDG', 1, 25, '00261');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('MH', 'MHL', 1, 0, '00692');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('MK', 'MKD', 1, 0, '00389');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('ML', 'MLI', 1, 25, '00223');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('MM', 'MMR', 1, 24, '0095');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('MN', 'MNG', 1, 24, '00976');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('MO', 'MAC', 1, 0, '00853');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('MP', 'MNP', 1, 0, '001670');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('MQ', 'MTQ', 1, 26, '00596');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('MR', 'MRT', 1, 0, '00222');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('MS', 'MSR', 1, 26, '001664');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('MT', 'MLT', 1, 31, '00356');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('MU', 'MUS', 1, 25, '00230');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('MV', 'MDV', 1, 24, '00960');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('MW', 'MWI', 1, 25, '00265');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('MX', 'MEX', 1, 26, '0052');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('MY', 'MYS', 1, 24, '0060');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('MZ', 'MOZ', 1, 25, '00258');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('NA', 'NAM', 1, 25, '00264');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('NC', 'NCL', 1, 0, '00687');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('NE', 'NER', 1, 25, '00227');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('NF', 'NFK', 1, 0, '00672');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('NG', 'NGA', 1, 0, '00234');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('NI', 'NIC', 1, 26, '00505');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('NL', 'NLD', 1, 31, '0031');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('NO', 'NOR', 1, 30, '0047');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('NP', 'NPL', 1, 24, '00977');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('NR', 'NRU', 1, 0, '00674');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('NU', 'NIU', 1, 0, '00683');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('NZ', 'NZL', 1, 29, '0064');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('OM', 'OMN', 1, 24, '00968');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('PA', 'PAN', 1, 26, '00507');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('PE', 'PER', 1, 27, '0051');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('PF', 'PYF', 1, 0, '00689');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('PG', 'PNG', 1, 0, '00675');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('PH', 'PHL', 1, 24, '0063');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('PK', 'PAK', 1, 24, '0092');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('PL', 'POL', 1, 31, '0048');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('PM', 'SPM', 1, 26, '00508');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('PN', 'PCN', 1, 0, '0064');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('PR', 'PRI', 1, 26, '001787,001939');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('PT', 'PRT', 1, 31, '00351');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('PW', 'PLW', 1, 0, '00680');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('PY', 'PRY', 1, 27, '00595');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('QA', 'QAT', 1, 24, '00974');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('RE', 'REU', 1, 0, '00262');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('RO', 'ROM', 1, 31, '0040');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('RU', 'RUS', 1, 24, '007');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('RW', 'RWA', 1, 25, '00250');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('SA', 'SAU', 1, 24, '00966');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('SB', 'SLB', 1, 0, '00677');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('SC', 'SYC', 1, 25, '00248');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('SD', 'SDN', 1, 25, '00249');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('SE', 'SWE', 1, 31, '0046');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('SG', 'SGP', 1, 24, '0065');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('SH', 'SHN', 1, 0, '00290');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('SI', 'SVN', 1, 31, '00386');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('SJ', 'SJM', 1, 0, '');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('SK', 'SVK', 1, 31, '00421');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('SL', 'SLE', 1, 25, '00232');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('SM', 'SMR', 1, 30, '00378');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('SN', 'SEN', 1, 25, '00221');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('SO', 'SOM', 1, 25, '00252');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('SR', 'SUR', 1, 27, '00597');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('ST', 'STP', 1, 25, '00239');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('SV', 'SLV', 1, 26, '00503');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('SY', 'SYR', 1, 24, '00963');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('SZ', 'SWZ', 1, 25, '00268');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('TC', 'TCA', 1, 0, '001649');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('TD', 'TCD', 1, 25, '00235');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('TF', 'ATF', 1, 0, '');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('TG', 'TGO', 1, 25, '00228');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('TH', 'THA', 1, 24, '0066');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('TJ', 'TJK', 1, 24, '00992');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('TK', 'TKL', 1, 0, '00690');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('TM', 'TKM', 1, 24, '00993');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('TN', 'TUN', 1, 25, '00216');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('TO', 'TON', 1, 0, '00676');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('TP', 'TMP', 1, 0, '00670');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('TR', 'TUR', 1, 24, '0090');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('TT', 'TTO', 1, 26, '001868');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('TV', 'TUV', 1, 0, '00688');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('TW', 'TWN', 1, 24, '00886');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('TZ', 'TZA', 1, 25, '00255');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('UA', 'UKR', 1, 30, '00380');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('UG', 'UGA', 1, 25, '00256');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('UM', 'UMI', 1, 0, '00690');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('US', 'USA', 1, 26, '001');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('UY', 'URY', 1, 27, '00598');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('UZ', 'UZB', 1, 24, '00998');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('VA', 'VAT', 1, 30, '0039');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('VC', 'VCT', 1, 26, '001784');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('VE', 'VEN', 1, 27, '0058');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('VG', 'VGB', 1, 26, '001284');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('VI', 'VIR', 1, 26, '001340');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('VN', 'VNM', 1, 24, '0084');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('VU', 'VUT', 1, 0, '00678');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('WF', 'WLF', 1, 0, '00681');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('WS', 'WSM', 1, 0, '00685');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('YE', 'YEM', 1, 24, '00967');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('YT', 'MYT', 1, 0, '00262');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('ZA', 'ZAF', 1, 25, '0027');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('ZM', 'ZMB', 1, 25, '00260');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('ZR', 'ZAR', 1, 0, '00243');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('ZW', 'ZWE', 1, 25, '00263');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('RS', 'SRB', 1, 30, '00381');
INSERT INTO `##_countries` (`countries_iso_code_2`, `countries_iso_code_3`, `status`, `zone_id`, `phone_prefix`) VALUES ('TL', 'TLS', 1, 24, '00670');

CREATE TABLE `##_federal_states` (
	`states_id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
	`states_code` VARCHAR(5) NOT NULL ,
	`country_iso_code_2` VARCHAR(2) NOT NULL ,
	`status` TINYINT UNSIGNED NOT NULL DEFAULT 1
) ENGINE=#E_ DEFAULT CHARSET=utf8;

CREATE TABLE `##_federal_states_description` (
	`states_id` INT NOT NULL ,
	`language_code` VARCHAR(2) NOT NULL ,
	`state_name` VARCHAR(64) NOT NULL ,
	PRIMARY KEY ( `states_id`, `language_code` )
) ENGINE=#E_ DEFAULT CHARSET=utf8;

CREATE TABLE `##_countries_description` (
  `language_code` char(2) NOT NULL,
  `countries_name` varchar(64) default NULL,
  `countries_iso_code_2` char(2) NOT NULL,
  PRIMARY KEY  (`language_code`,`countries_iso_code_2`),
  KEY `IDX_COUNTRIES_NAME` (`countries_name`)
) ENGINE=#E_ DEFAULT CHARSET=utf8;

CREATE TABLE `##_countries_permission` (
  `pid` varchar(3) NOT NULL,
  `permission` TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `pgroup` varchar(255) NOT NULL,
  PRIMARY KEY (`pid`,`pgroup`)
) ENGINE=#E_ DEFAULT CHARSET=utf8;

CREATE TABLE `##_cron` (
  `cron_id` INT unsigned NOT NULL AUTO_INCREMENT,
  `cron_note` varchar(150) DEFAULT NULL,
  `running_status` TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `active_status` TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `cron_value` TINYINT UNSIGNED DEFAULT NULL,
  `cron_type` char(1) DEFAULT NULL,
  `hour` TINYINT unsigned DEFAULT NULL,
  `minute` TINYINT unsigned DEFAULT NULL,
  `cron_action` varchar(150) DEFAULT NULL,
  `cron_parameter` text,
  `last_run_date` datetime DEFAULT NULL,
  `next_run_date` datetime DEFAULT NULL,
  PRIMARY KEY (`cron_id`)
) ENGINE=#E_ DEFAULT CHARSET=utf8;

CREATE TABLE `##_currencies` (
  `currencies_id` INT NOT NULL auto_increment,
  `title` varchar(32) NOT NULL,
  `code` char(3) NOT NULL,
  `prefix` varchar(12) default NULL,
  `suffix` varchar(12) default NULL,
  `dec_point` char(1) default ',',
  `thousands_sep` char(1) default '.',
  `decimals` tinyint UNSIGNED default 2,
  `value_multiplicator` float(13,8) default 1,
  `last_updated` datetime default NULL,
  PRIMARY KEY  (`currencies_id`)
) ENGINE=#E_ DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


CREATE TABLE `##_customers` (
  `customers_id` INT NOT NULL auto_increment,
  `external_id` varchar(255) default NULL,
  `customers_cid` varchar(32) default NULL,
  `customers_vat_id` varchar(20) default NULL,
  `customers_vat_id_status` TINYINT UNSIGNED default NULL,
  `customers_status` TINYINT UNSIGNED NOT NULL default 1,
  `customers_email_address` varchar(96) NOT NULL,
  `customers_password` varchar(255) default NULL,
  `password_type` TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `account_type` TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `password_request_key` varchar(32) default NULL,
  `payment_unallowed` varchar(255) default NULL,
  `shipping_unallowed` varchar(255) default NULL,
  `date_added` datetime NOT NULL DEFAULT current_timestamp,
  `last_modified` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp,
  `shop_id` INT NOT NULL default '1',
  `customers_default_currency` char(3) default NULL,
  `customers_default_language` char(2) default NULL,
  `campaign_id` INT NOT NULL DEFAULT '0',
  PRIMARY KEY  (`customers_id`)
) ENGINE=#E_ DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE `##_customers_addresses` (
  `address_book_id` INT NOT NULL auto_increment,
  `external_id` varchar(255) default NULL,
  `customers_id` INT NOT NULL,
  `customers_gender` char(1) default NULL,
  `customers_title` varchar(64) default NULL,
  `customers_dob` datetime NULL default NULL,
  `customers_phone` varchar(32) default NULL,
  `customers_mobile_phone` varchar(32) default NULL,
  `customers_fax` varchar(32) default NULL,
  `customers_company` varchar(64) default NULL,
  `customers_company_2` varchar(64) default NULL,
  `customers_company_3` varchar(64) default NULL,
  `customers_firstname` varchar(32) NOT NULL,
  `customers_lastname` varchar(32) NOT NULL,
  `customers_street_address` varchar(64) NOT NULL,
  `customers_address_addition` varchar(64) default NULL,
  `customers_suburb` varchar(32) default NULL,
  `customers_postcode` varchar(10) NOT NULL,
  `customers_city` varchar(32) NOT NULL,
  `customers_country_code` char(2) NOT NULL,
  `customers_federal_state_code` INT default NULL,
  `address_class` varchar(32) default NULL,
  `date_added` datetime NOT NULL DEFAULT current_timestamp,
  `last_modified` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp,
  PRIMARY KEY  (`address_book_id`),
  KEY `idx_address_book_customers_id` (`customers_id`)
) ENGINE=#E_ DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE `##_customers_basket` (
  `basket_id` INT NOT NULL auto_increment,
  `customers_id` INT NOT NULL,
  `products_key` varchar(255) default NULL,
  `products_id` INT default NULL,
  `products_quantity` decimal(15,2) default NULL,
  `products_info` longtext,
  `date_added` datetime NOT NULL DEFAULT current_timestamp,
  `type` varchar(32) default NULL,
  `status` TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `sort_order` INT default '0',
  PRIMARY KEY  (`basket_id`)
) ENGINE=#E_ DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE `##_customers_status` (
  `customers_status_id` INT NOT NULL auto_increment,
  `customers_status_min_order` INT default NULL,
  `customers_status_max_order` INT default NULL,
  `customers_status_show_price` TINYINT UNSIGNED NOT NULL DEFAULT 1,
  `customers_status_show_price_tax` TINYINT UNSIGNED NOT NULL DEFAULT 1,
  `customers_status_add_tax_ot` TINYINT UNSIGNED NOT NULL DEFAULT 1,
  `customers_status_graduated_prices` TINYINT UNSIGNED NOT NULL DEFAULT 1,
  `customers_fsk18` TINYINT UNSIGNED NOT NULL DEFAULT 1,
  `customers_fsk18_display` TINYINT UNSIGNED NOT NULL DEFAULT 1,
  `customers_status_master` INT default '0',
  `customers_status_template` varchar(255) default NULL,
  `customers_status_tax_rates_calculation_base` VARCHAR(64) NULL DEFAULT 'b2c_eu',
  PRIMARY KEY  (`customers_status_id`)
) ENGINE=#E_  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


INSERT INTO `##_customers_status` VALUES (1, 0, 0, 1, 1, 1, 1, 1, 1, 0, '', 'b2c_eu');
INSERT INTO `##_customers_status` VALUES (2, 0, 0, 1, 1, 1, 1, 1, 1, 0, '', 'b2c_eu');
INSERT INTO `##_customers_status` VALUES (3, 0, 0, 1, 0, 1, 1, 1, 1, 0, '', 'shipping_address');
INSERT INTO `##_customers_status` VALUES (4, 0, 0, 1, 0, 0, 1, 1, 1, 0, '', 'shipping_address');


CREATE TABLE `##_customers_status_description` (
  `customers_status_id` INT NOT NULL default '0',
  `language_code` char(2) NOT NULL default '1',
  `customers_status_name` varchar(32) default NULL,
  PRIMARY KEY  (`customers_status_id`,`language_code`),
  KEY `idx_orders_status_name` (`customers_status_name`)
) ENGINE=#E_ DEFAULT CHARSET=utf8;


CREATE TABLE `##_failed_login` (
  `fail_id` INT NOT NULL auto_increment,
  `check_type` TINYINT UNSIGNED NOT NULL,
  `lookup` varchar(96) NOT NULL,
  `last_try` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `fail_count` INT NOT NULL default '0',
  `lock_until` timestamp NULL default NULL,
  `fail_type` varchar(32) NOT NULL,
  PRIMARY KEY  (`fail_id`),
  UNIQUE KEY `check` (`lookup`)
) ENGINE=#E_ DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE  TABLE `##_failed_pages` (
  `fail_id` INT NOT NULL AUTO_INCREMENT,
  `ip` varchar(64) NOT NULL,
  `last_try` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `fail_count` INT NOT NULL DEFAULT '0',
  `lock_until` timestamp NULL DEFAULT NULL,
  `fail_type` varchar(32) NOT NULL,
  PRIMARY KEY (`fail_id`),
  UNIQUE KEY `check` (`ip`, `fail_type`)
) ENGINE=#E_ DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE `##_feed` (
  `feed_id` INT NOT NULL AUTO_INCREMENT,
  `feed_key` varchar(32) NOT NULL, /* add */
  `feed_language_code` char(2) NOT NULL,
  `feed_store_id` INT NOT NULL DEFAULT '0',
  `feed_title` varchar(64) NOT NULL,
  `feed_type` INT NOT NULL,
  `feed_header` text,
  `feed_body` text,
  `feed_footer` text,
  `feed_mail` varchar(255) DEFAULT NULL,
  `feed_mail_flag` TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `feed_mail_header` varchar(255) DEFAULT NULL,
  `feed_mail_body` text,
  `feed_ftp_flag` TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `feed_ftp_server` varchar(255) DEFAULT NULL,
  `feed_ftp_tls` TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `feed_ftp_user` varchar(64) DEFAULT NULL,
  `feed_ftp_password` varchar(64) DEFAULT NULL,
  `feed_ftp_dir` varchar(255) DEFAULT NULL,
  `feed_ftp_passiv` TINYINT UNSIGNED NOT NULL DEFAULT 1,
  `feed_filename` varchar(64) DEFAULT NULL,
  `feed_filetype` varchar(64) DEFAULT NULL,
  `feed_encoding` varchar(32) NOT NULL DEFAULT 'UTF-8', /* add */
  `feed_save` TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `feed_export_limit` INT NOT NULL DEFAULT '100', /* add */
  `feed_linereturn_deactivated` TINYINT UNSIGNED NOT NULL DEFAULT 0, /* add */
  `feed_p_currency_code` char(32) NOT NULL DEFAULT '0',
  `feed_p_customers_status` INT NOT NULL DEFAULT '0',
  `feed_p_campaign` varchar(64) DEFAULT NULL,
  `feed_p_price_min` text, /* add */
  `feed_p_price_max` text, /* add */
  `feed_p_quantity_min` text, /* add */
  `feed_p_quantity_max` text, /* add */
  `feed_p_model_min` varchar(255) DEFAULT NULL, /* add */
  `feed_p_model_max` varchar(255) DEFAULT NULL, /* add */
  `feed_p_deactivated_status` TINYINT UNSIGNED NOT NULL DEFAULT '0', /* add */
  `feed_categories` text DEFAULT NULL, /* add */
  `feed_manufacturers` text DEFAULT NULL, /* add */
  `feed_o_customers_status` INT NOT NULL DEFAULT '0',
  `feed_o_orders_status` INT NOT NULL DEFAULT '0',
  `feed_date_range_orders` INT NOT NULL DEFAULT '0',
  `feed_date_from_orders` datetime NULL DEFAULT NULL,
  `feed_date_to_orders` datetime NULL DEFAULT NULL,
  `feed_post_flag` INT NOT NULL DEFAULT '0',
  `feed_post_server` varchar(255) DEFAULT NULL,
  `feed_post_field` varchar(255) DEFAULT NULL,
  `feed_pw_flag` TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `feed_pw_user` varchar(32) DEFAULT NULL,
  `feed_pw_pass` varchar(32) DEFAULT NULL,
  `feed_p_slave` TINYINT UNSIGNED NOT NULL DEFAULT 1,
  PRIMARY KEY  (`feed_id`)
) ENGINE=#E_  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE `##_image_type` (
  `id` INT NOT NULL auto_increment,
  `folder` varchar(32) NOT NULL,
  `width` INT NOT NULL,
  `height` INT NOT NULL,
  `process` enum('true','false') NOT NULL default 'true',
  `class` varchar(32) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=#E_  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

INSERT INTO `##_image_type` (`folder`, `width`, `height`, `process`, `class`) VALUES('thumb', 290, 400, 'true', 'default');
INSERT INTO `##_image_type` (`folder`, `width`, `height`, `process`, `class`) VALUES('info', 725, 725, 'true', 'default');
INSERT INTO `##_image_type` (`folder`, `width`, `height`, `process`, `class`) VALUES('icon', 20, 20, 'true', 'default');
INSERT INTO `##_image_type` (`folder`, `width`, `height`, `process`, `class`) VALUES('manufacturer/thumb', 120, 80, 'true', 'manufacturer');
INSERT INTO `##_image_type` (`folder`, `width`, `height`, `process`, `class`) VALUES('manufacturer/info', 200, 180, 'true', 'manufacturer');
INSERT INTO `##_image_type` (`folder`, `width`, `height`, `process`, `class`) VALUES('manufacturer/icon', 20, 20, 'true', 'manufacturer');
INSERT INTO `##_image_type` (`folder`, `width`, `height`, `process`, `class`) VALUES('category/thumb', 352, 174, 'true', 'category');
INSERT INTO `##_image_type` (`folder`, `width`, `height`, `process`, `class`) VALUES('category/info', 200, 180, 'true', 'category');
INSERT INTO `##_image_type` (`folder`, `width`, `height`, `process`, `class`) VALUES('category/icon', 20, 20, 'true', 'category');
INSERT INTO `##_image_type` (`folder`, `width`, `height`, `process`, `class`) VALUES('category/startpage', 360, 120, 'true', 'category');
INSERT INTO `##_image_type` (`folder`, `width`, `height`, `process`, `class`) VALUES('category/listingTop', 1200, 600, 'true', 'category');




CREATE TABLE `##_languages` (
  `languages_id` INT NOT NULL auto_increment,
  `language_status` TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `name` varchar(32) NOT NULL,
  `content_language` char(2) NOT NULL,
  `code` char(2) NOT NULL,
  `allow_edit` TINYINT UNSIGNED NOT NULL DEFAULT 1,
  `image` varchar(64) default NULL,
  `sort_order` INT default NULL,
  `language_charset` text NOT NULL,
  `default_currency` varchar(64) NOT NULL,
  `font` varchar(255) NOT NULL,
  `font_size` TINYINT UNSIGNED NOT NULL,
  `font_position` TINYINT UNSIGNED NOT NULL,
  `setlocale` varchar(255) NOT NULL,
  PRIMARY KEY  (`languages_id`),
  UNIQUE KEY `code_2` (`code`),
  KEY `IDX_LANGUAGES_NAME` (`name`),
  KEY `code` (`code`),
  KEY `sort_order` (`sort_order`)
) ENGINE=#E_  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE `##_language_content` (
  `language_content_id` INT NOT NULL auto_increment,
  `translated` TINYINT UNSIGNED NOT NULL DEFAULT 1,
  `language_code` char(2) default NULL,
  `language_key` varchar(255) default NULL,
  `language_value` text CHARACTER SET utf8mb4,
  `class` varchar(32) NOT NULL default 'store',
  `plugin_key` varchar(32) default NULL,
  `readonly` TINYINT UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY  (`language_content_id`),
  UNIQUE KEY `language_code` (`language_code`,`language_key`,`class`),
  KEY `language_code_2` (`language_code`,`class`)
) ENGINE=#E_  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


CREATE TABLE `##_mail_templates` (
  `tpl_id` INT NOT NULL auto_increment,
  `tpl_type` varchar(255) NULL DEFAULT '',
  `tpl_special` varchar(64) default '0',
  `email_from` varchar(255) default NULL,
  `email_from_name` varchar(255) default NULL,
  `email_reply` varchar(255) default NULL,
  `email_reply_name` varchar(255) default NULL,
  `email_forward` varchar(255) default NULL,
  PRIMARY KEY  (`tpl_id`)
) ENGINE=#E_ DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

INSERT INTO `##_mail_templates` VALUES (1, 'new_password', '0', '', '', '', '', '');
INSERT INTO `##_mail_templates` VALUES (2, 'create_account', '0', '', '', '', '', '');
INSERT INTO `##_mail_templates` VALUES (5, 'password_optin', '0', '', '', '', '', '');
INSERT INTO `##_mail_templates` VALUES (6, 'send_order', '0', '', '', '', '', '');
INSERT INTO `##_mail_templates` VALUES (8, 'update_order-admin', 'ALL', '', '', '', '', '');
INSERT INTO `##_mail_templates` VALUES (9, 'contact_mail-admin', '0', '', '', '', '', '');
INSERT INTO `##_mail_templates` VALUES (10, 'contact_mail', 'ALL', '', '', '', '', '');
INSERT INTO `##_mail_templates` VALUES (11, 'dsgvo', '0', '', '', '', '', '');

CREATE TABLE `##_mail_templates_content` (
  `tpl_id` INT NOT NULL default '0',
  `language_code` char(2) NOT NULL default '0',
  `mail_body_html` text,
  `mail_body_txt` text,
  `mail_subject` varchar(255) default NULL,
  PRIMARY KEY  (`tpl_id`,`language_code`)
) ENGINE=#E_ DEFAULT CHARSET=utf8mb4;


CREATE TABLE `##_manufacturers` (
  `manufacturers_id` INT NOT NULL auto_increment,
  `external_id` varchar(255) default NULL,
  `manufacturers_name` varchar(255) default NULL,
  `manufacturers_image` varchar(255) default NULL,
  `manufacturers_status` TINYINT UNSIGNED NOT NULL DEFAULT 1,
  `manufacturers_sort` SMALLINT NOT NULL default 0,
  `products_sorting` varchar(32) default NULL,
  `products_sorting2` varchar(32) default NULL,
  `date_added` datetime NULL DEFAULT current_timestamp,
  `last_modified` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp,
  PRIMARY KEY  (`manufacturers_id`),
  KEY `IDX_MANUFACTURERS_NAME` (`manufacturers_name`)
) ENGINE=#E_ DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE `##_manufacturers_info` (
  `manufacturers_id` INT NOT NULL,
  `language_code` char(2) NOT NULL,
  `manufacturers_description` text,
  `manufacturers_url` varchar(255) default NULL,
  `manufacturers_store_id` INT NOT NULL,
  PRIMARY KEY  (`manufacturers_id`,`language_code`,`manufacturers_store_id`)
) ENGINE=#E_ DEFAULT CHARSET=utf8mb4;

CREATE TABLE `##_manufacturers_permission` (
  `pid` INT NOT NULL,
  `permission` TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `pgroup` varchar(255) NOT NULL,
  PRIMARY KEY  (`pid`,`pgroup`)
) ENGINE=#E_ DEFAULT CHARSET=utf8;

CREATE TABLE `##_media` (
  `id` INT NOT NULL auto_increment,
  `file` varchar(255) NOT NULL,
  `type` varchar(6) default NULL,
  `class` varchar(64) default NULL,
  `download_status` enum('free','order') NOT NULL default 'free',
  `status` enum('true','false') NOT NULL default 'true',
  `owner` INT NOT NULL default '1',
  `date_added` datetime NOT NULL DEFAULT current_timestamp,
  `last_modified` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp,
  `max_dl_count` INT NOT NULL default '0',
  `max_dl_days` INT NOT NULL default '0',
  `total_downloads` INT NOT NULL default '0',
  `copyright_holder` VARCHAR(255) NULL,
  PRIMARY KEY  (`id`),
  KEY `file` (`file`),
  KEY `type` (`type`),
  KEY `class` (`class`),
  KEY `download_status` (`download_status`)
) ENGINE=#E_ DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE `##_media_description` (
  `id` INT NOT NULL,
  `language_code` char(2) NOT NULL,
  `media_name` varchar(64) default NULL,
  `media_description` text,
  UNIQUE KEY `id` (`id`,`language_code`)
) ENGINE=#E_ DEFAULT CHARSET=utf8mb4;


CREATE TABLE `##_media_file_types` (
  `mft_id` INT NOT NULL auto_increment,
  `file_ext` varchar(6) default NULL,
  `file_type` varchar(32) default NULL,
  PRIMARY KEY  (`mft_id`)
) ENGINE=#E_ DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

INSERT INTO `##_media_file_types` VALUES (1, 'pdf', 'files');
INSERT INTO `##_media_file_types` VALUES (2, 'zip', 'files');
INSERT INTO `##_media_file_types` VALUES (3, 'gif', 'images');
INSERT INTO `##_media_file_types` VALUES (4, 'png', 'images');
INSERT INTO `##_media_file_types` VALUES (5, 'jpg', 'images');
INSERT INTO `##_media_file_types` VALUES (6, 'doc', 'files');
INSERT INTO `##_media_file_types` VALUES (7, 'jpeg', 'images');
INSERT INTO `##_media_file_types` VALUES (8, 'exe', 'files');
INSERT INTO `##_media_file_types` VALUES (9, 'gz', 'files');
INSERT INTO `##_media_file_types` VALUES (10, 'xls', 'files');
INSERT INTO `##_media_file_types` VALUES (11, 'rar', 'files');
INSERT INTO `##_media_file_types` VALUES (12, 'mp3', 'files');
INSERT INTO `##_media_file_types` VALUES (13, 'dmg', 'files');
INSERT INTO `##_media_file_types` VALUES (14, 'mp4', 'files');
INSERT INTO `##_media_file_types` VALUES (15, 'ogg', 'files');
INSERT INTO `##_media_file_types` VALUES (16, '3gp', 'files');
INSERT INTO `##_media_file_types` VALUES (17, 'aac', 'files');

CREATE TABLE `##_media_gallery` (
  `mg_id` INT NOT NULL auto_increment,
  `parent_id` INT default 0,
  `sort_order` INT default '0',
  `class` varchar(32) default NULL,
  `status` TINYINT UNSIGNED NOT NULL DEFAULT 1,
  `u_id` INT NOT NULL default '1',
  PRIMARY KEY  (`mg_id`)
) ENGINE=#E_ DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

INSERT INTO `##_media_gallery` (`mg_id`, `parent_id`, `sort_order`, `class`, `status`, `u_id`) VALUES(1, 0, 1, 'default', 1, 1);
INSERT INTO `##_media_gallery` (`mg_id`, `parent_id`, `sort_order`, `class`, `status`, `u_id`) VALUES(2, 0, 2, 'product', 1, 1);
INSERT INTO `##_media_gallery` (`mg_id`, `parent_id`, `sort_order`, `class`, `status`, `u_id`) VALUES(3, 0, 3, 'category', 1, 1);
INSERT INTO `##_media_gallery` (`mg_id`, `parent_id`, `sort_order`, `class`, `status`, `u_id`) VALUES(4, 0, 4, 'manufacturer', 1, 1);
INSERT INTO `##_media_gallery` (`mg_id`, `parent_id`, `sort_order`, `class`, `status`, `u_id`) VALUES(5, 0, 5, 'content', 1, 1);
INSERT INTO `##_media_gallery` (`mg_id`, `parent_id`, `sort_order`, `class`, `status`, `u_id`) VALUES(6, 0, 99998, 'files_free', 1, 1);
INSERT INTO `##_media_gallery` (`mg_id`, `parent_id`, `sort_order`, `class`, `status`, `u_id`) VALUES(7, 0, 99999, 'files_order', 1, 1);
INSERT INTO `##_media_gallery` (`mg_id`, `parent_id`, `sort_order`, `class`, `status`, `u_id`) VALUES(8, 0, 6, 'logo', 1, 1);

CREATE TABLE `##_media_gallery_description` (
  `mg_id` INT NOT NULL,
  `language_code` char(2) NOT NULL,
  `name` varchar(64) default NULL,
  PRIMARY KEY  (`mg_id`,`language_code`)
) ENGINE=#E_ DEFAULT CHARSET=utf8;


CREATE TABLE `##_media_link` (
  `ml_id` INT NOT NULL auto_increment,
  `m_id` INT NOT NULL,
  `link_id` INT NOT NULL,
  `class` varchar(32) default NULL,
  `type` enum('gallery','media', 'images') NOT NULL default 'images',
  `sort_order` INT NULL,
  PRIMARY KEY  (`ml_id`),
  KEY `link_id` (`link_id`),
  KEY `class` (`class`),
  KEY `type` (`type`),
  KEY `m_id` (`m_id`)
) ENGINE=#E_ DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE `##_media_symlink` (
  `symlink_id` INT NOT NULL auto_increment,
  `symlink_dir` varchar(64) NOT NULL,
  `symlink_valid` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`symlink_id`)
) ENGINE=#E_ DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


CREATE TABLE `##_media_to_media_gallery` (
  `ml_id` INT NOT NULL auto_increment,
  `m_id` INT NOT NULL,
  `mg_id` INT NULL DEFAULT 0,
  PRIMARY KEY  (`ml_id`)
) ENGINE=#E_ DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE `##_orders` (
  `orders_id` INT NOT NULL auto_increment,
  `customers_id` INT NOT NULL,
  `customers_cid` varchar(32) default NULL,
  `customers_vat_id` varchar(20) default NULL,
  `customers_status` INT default NULL,
  `customers_email_address` varchar(96) default NULL,
  `delivery_gender` varchar(32) default NULL,
  `delivery_phone` varchar(32) default NULL,
  `delivery_mobile_phone` varchar(32) default NULL,
  `delivery_fax` varchar(32) default NULL,
  `delivery_title` varchar(64) default NULL,
  `delivery_firstname` varchar(64) NOT NULL,
  `delivery_lastname` varchar(64) NOT NULL,
  `delivery_company` varchar(64) default NULL,
  `delivery_company_2` varchar(64) default NULL,
  `delivery_company_3` varchar(64) default NULL,
  `delivery_street_address` varchar(64) NOT NULL,
  `delivery_address_addition` varchar(64) default NULL,
  `delivery_suburb` varchar(32) default NULL,
  `delivery_city` varchar(32) NOT NULL,
  `delivery_postcode` varchar(10) NOT NULL,
  `delivery_zone` varchar(32) default NULL,
  `delivery_zone_code` INT default NULL,
  `delivery_country` varchar(32) NOT NULL,
  `delivery_country_code` char(2) NOT NULL,
  `delivery_federal_state_code` INT default NULL,
  `delivery_federal_state_code_iso` varchar(5) default NULL,
  `delivery_address_book_id` INT default NULL,
  `billing_gender` varchar(32) default NULL,
  `billing_phone` varchar(32) default NULL,
  `billing_mobile_phone` varchar(32) default NULL,
  `billing_fax` varchar(32) default NULL,
  `billing_title` varchar(64) default NULL,
  `billing_firstname` varchar(64) NOT NULL,
  `billing_lastname` varchar(64) NOT NULL,
  `billing_company` varchar(64) default NULL,
  `billing_company_2` varchar(64) default NULL,
  `billing_company_3` varchar(64) default NULL,
  `billing_street_address` varchar(64) NOT NULL,
  `billing_address_addition` varchar(64) default NULL,
  `billing_suburb` varchar(32) default NULL,
  `billing_city` varchar(32) NOT NULL,
  `billing_postcode` varchar(10) NOT NULL,
  `billing_zone` varchar(32) default NULL,
  `billing_zone_code` INT default NULL,
  `billing_country` varchar(32) NOT NULL,
  `billing_country_code` char(2) NOT NULL,
  `billing_federal_state_code` INT default NULL,
  `billing_federal_state_code_iso` varchar(5) default NULL,
  `billing_address_book_id` INT default NULL,
  `payment_code` varchar(64) default NULL,
  `subpayment_code` varchar(32) default NULL,
  `shipping_code` varchar(64) default NULL,
  `currency_code` char(3) default NULL,
  `currency_value` decimal(15,4) default NULL,
  `language_code` char(2) default NULL,
  `comments` TEXT CHARACTER SET utf8mb4 NULL default NULL ,
  `last_modified` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp,
  `date_purchased` datetime default NULL,
  `orders_status` TINYINT UNSIGNED default NULL,
  `orders_date_finished` datetime default NULL,
  `account_type` TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `allow_tax` TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `customers_ip` varchar(128) default NULL,
  `shop_id` INT NOT NULL default '1',
  `orders_data` longtext,
  `campaign_id` INT NOT NULL DEFAULT '0',
  `source_id` INT NOT NULL DEFAULT '0',
  `authorization_id` varchar(255) NOT NULL DEFAULT '',
  `authorization_amount` DECIMAL(15,4) NULL,
  `authorization_expire` DATETIME NULL,
  `orders_source_external_id` VARCHAR(32) DEFAULT NULL,
  `order_edit_acl_user` INT DEFAULT NULL,
  PRIMARY KEY  (`orders_id`),
  KEY `customers_id` (`customers_id`)
) ENGINE=#E_ DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE `##_orders_products` (
  `orders_products_id` INT NOT NULL auto_increment,
  `orders_id` INT NOT NULL,
  `products_id` INT NOT NULL,
  `products_model` varchar(64) default NULL,
  `products_name` varchar(255) NOT NULL,
  `products_price` decimal(15,4) NOT NULL,
  `products_discount` decimal(4,2) default NULL,
  `products_tax` decimal(7,4) default NULL,
  `products_tax_class` INT NOT NULL,
  `products_quantity` decimal(15,2) default NULL,
  `products_unit` INT NOT NULL DEFAULT 0,
  `products_data` longtext,
  `allow_tax` TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `products_shipping_time` varchar(255) default NULL,
  PRIMARY KEY  (`orders_products_id`),
  KEY `idx_orders_products_id` (`orders_id`,`products_id`),
  KEY `idx_products_id` (`products_id`),
  KEY `orders_id` (`orders_id`),
  KEY `products_quantity` (`products_quantity`)
) ENGINE=#E_ DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1 ;

CREATE TABLE `##_orders_products_media` (
  `orders_id` INT NOT NULL,
  `media_id` INT NOT NULL,
  `orders_products_id` INT NOT NULL,
  `download_count` INT NOT NULL default '0',
  UNIQUE KEY `orders_id` (`orders_id`,`media_id`,`orders_products_id`)
) ENGINE=#E_ DEFAULT CHARSET=utf8;

CREATE TABLE `##_orders_status_history` (
  `orders_status_history_id` INT NOT NULL auto_increment,
  `orders_id` INT default NULL,
  `orders_status_id` int default NULL,
  `date_added` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `customer_notified` TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `comments` text CHARACTER SET utf8mb4,
  `change_trigger` varchar(32) default 'user',
  `callback_id` varchar(64) default '',
  `customer_show_comment` TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `callback_message` varchar(255) default NULL,
  PRIMARY KEY  (`orders_status_history_id`)
) ENGINE=#E_ DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE `##_orders_total` (
  `orders_total_id` INT NOT NULL auto_increment,
  `orders_id` INT NOT NULL,
  `orders_total_key` varchar(32) default NULL,
  `orders_total_key_id` INT NULL,
  `orders_total_model` varchar(64) default NULL,
  `orders_total_name` varchar(64) default NULL,
  `orders_total_price` decimal(15,4) default NULL,
  `orders_total_tax` decimal(7,4) default NULL,
  `orders_total_tax_class` INT default NULL,
  `orders_total_quantity` decimal(15,2) NOT NULL default '1',
  `allow_tax` TINYINT UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY  (`orders_total_id`),
  KEY `idx_key_id` (`orders_id`,`orders_total_key`)
) ENGINE=#E_ DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE `##_orders_stats` (
  `orders_id` INT NOT NULL,
  `orders_stats_price` decimal(15,4) default NULL,
  `products_count` INT default NULL,
  PRIMARY KEY  (`orders_id`)
) ENGINE=#E_ DEFAULT CHARSET=utf8;

CREATE TABLE `##_orders_source` (
	`source_id` INT NOT NULL AUTO_INCREMENT,
	`source_name` VARCHAR(32) NULL DEFAULT '0',
	PRIMARY KEY (`source_id`)
) ENGINE=#E_ DEFAULT CHARSET=utf8;

INSERT INTO `##_orders_source` (`source_name`) VALUES ('order_source_phone');
INSERT INTO `##_orders_source` (`source_name`) VALUES ('order_source_fax');
INSERT INTO `##_orders_source` (`source_name`) VALUES ('order_source_email');
INSERT INTO `##_orders_source` (`source_name`) VALUES ('order_source_casual');

CREATE TABLE `##_payment` (
  `payment_id` INT NOT NULL auto_increment,
  `payment_code` varchar(32) NOT NULL,
  `payment_dir` varchar(255) NOT NULL,
  `payment_icon` varchar(255) default NULL,
  `payment_tax_class` INT default NULL,
  `payment_tpl` varchar(64) default NULL,
  `payment_cost_info` TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `status` TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `sort_order` INT default NULL,
  `plugin_required` TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `plugin_installed` INT default '0',
  PRIMARY KEY  (`payment_id`)
) ENGINE=#E_ DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE `##_payment_cost` (
  `payment_cost_id` INT NOT NULL auto_increment,
  `payment_id` INT NOT NULL,
  `payment_geo_zone` INT NOT NULL default '0',
  `payment_country_code` char(2) NOT NULL default '0',
  `payment_type_value_from` decimal(15,2) NOT NULL default '0.00',
  `payment_type_value_to` decimal(15,2) NOT NULL default '0.00',
  `payment_price` decimal(15,4) NOT NULL default '0.0000',
  `payment_cost_discount` TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `payment_cost_percent` TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `payment_allowed` TINYINT UNSIGNED NOT NULL DEFAULT 0,
/*  `payment_price_type` TINYINT UNSIGNED NOT NULL DEFAULT 0,  Feature will be added*/
/*  `payment_min_orders` INT NOT NULL default '0', Feature will be added*/
  PRIMARY KEY  (`payment_cost_id`)
) ENGINE=#E_ DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE `##_payment_description` (
  `payment_id` INT NOT NULL,
  `language_code` char(2) NOT NULL,
  `payment_name` varchar(255) default NULL,
  `payment_desc` text,
/*  `payment_email_desc` text,   */
  `payment_email_desc_txt` text,
  `payment_email_desc_html` text,
  `payment_description_store_id` INT NOT NULL DEFAULT 1,
  PRIMARY KEY  (`language_code`,`payment_id`,`payment_description_store_id`)
) ENGINE=#E_ DEFAULT CHARSET=utf8;

CREATE TABLE `##_plugin_code` (
  `id` INT NOT NULL auto_increment,
  `plugin_id` INT NOT NULL,
  `hook` varchar(255) default NULL,
  `code` text,
  `code_status` TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `plugin_status` TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `plugin_code` varchar(255) default NULL,
  `sortorder` INT default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `plugin_id` (`plugin_id`,`hook`),
  KEY `plugin_id_2` (`plugin_id`),
  KEY `hook_code_status_plugin_status_sortorder` (`hook`,`code_status`,`plugin_status`,`sortorder`),
  FULLTEXT KEY `code` (`code`)
) ENGINE=#E_ DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE `##_plugin_products` (
  `plugin_id` INT NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  `version` varchar(255) default NULL,
  `version_available` varchar(255) default NULL,
  `description` text,
  `url` varchar(255) default NULL,
  `plugin_status` TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `code` varchar(255) default NULL,
  `type` varchar(64) default NULL,
  `developer` VARCHAR( 255 ) NOT NULL DEFAULT '',
  `documentation_link` VARCHAR( 255 ) NOT NULL DEFAULT '',
  `marketplace_link` VARCHAR( 255 ) NOT NULL DEFAULT '',
  PRIMARY KEY  (`plugin_id`),
  KEY `plugin_status` (`plugin_status`)
) ENGINE=#E_ DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE `##_plugin_sql` (
  `plg_sql_id` INT NOT NULL auto_increment,
  `plugin_id` INT NOT NULL,
  `version` varchar(32) default NULL,
  `install` text,
  `uninstall` text,
  `created` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`plg_sql_id`)
) ENGINE=#E_ DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE `##_products` (
  `products_id` INT NOT NULL auto_increment,
  `external_id` varchar(255) default NULL,
  `permission_id` INT default NULL,
  `products_owner` INT NOT NULL default '1',
  `products_ean` varchar(128) CHARACTER SET utf8mb4 default NULL,
  `products_quantity` decimal(15,2) default NULL,
  `show_stock` TINYINT(1) NULL DEFAULT 0,
  `products_average_quantity` INT default '0',
  `products_shippingtime` TINYINT UNSIGNED default NULL,
  `products_shippingtime_nostock` TINYINT UNSIGNED default NULL,
  `products_model` varchar(255) CHARACTER SET utf8 default NULL,
  `products_mpn` varchar(512) CHARACTER SET utf8 default NULL,
  `price_flag_graduated_all` TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `price_flag_graduated_1` TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `price_flag_graduated_2` TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `price_flag_graduated_3` TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `price_flag_graduated_4` TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `products_sort` INT default '0',
  `products_image` varchar(255) default NULL,
  `products_price` decimal(15,4) default NULL,
  `date_added` timestamp NOT NULL DEFAULT current_timestamp,
  `last_modified` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp,
  `date_available` datetime default NULL,
  `products_weight` decimal(15,4) default NULL,
  `products_condition` varchar(64) CHARACTER SET utf8 default 'NewCondition',
  `products_status` TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `products_tax_class_id` INT default NULL,
  `product_template` varchar(64) default NULL,
  `product_list_template` varchar(64) default NULL,
  `manufacturers_id` INT default NULL,
  `products_ordered` INT default '0',
  `products_transactions` INT default '0',
  `products_fsk18` TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `products_vpe` INT default NULL,
  `products_vpe_status` TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `products_vpe_value` decimal(15,4) default '0.0000',
  `products_unit` INT default 0,
  `products_average_rating` decimal(14,4) default '0.0000',
  `products_rating_count` INT default '0',
  `products_digital` TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `flag_has_specials` TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `products_serials` TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `total_downloads` INT default '0',
  PRIMARY KEY  (`products_id`),
  KEY `idx_products_date_added` (`date_added`),
  KEY `products_status` (`products_status`),
  KEY `products_ordered` (`products_ordered`),
  KEY `manufacturers_id` (`manufacturers_id`)
) ENGINE=#E_ DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE `##_products_cross_sell` (
  `products_id` INT NOT NULL,
  `products_id_cross_sell` INT NOT NULL,
  UNIQUE KEY `products_id` (`products_id`,`products_id_cross_sell`)
) ENGINE=#E_ DEFAULT CHARSET=utf8;


CREATE TABLE `##_products_serials` (
  `serial_id` INT NOT NULL auto_increment,
  `serial_number` varchar(64) NULL default '',
  `status` TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `products_id` INT NOT NULL,
  `orders_id` INT NULL default 0,
  `orders_products_id` INT NULL default 0,
  PRIMARY KEY  (`serial_id`),
  FULLTEXT KEY `serial_number` (`serial_number`)
) ENGINE=#E_ DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE `##_products_description` (
  `products_id` INT NOT NULL,
  `language_code` char(2) NOT NULL,
  `reload_st` INT NULL DEFAULT 0,
  `products_name` varchar(255) default NULL,
  `products_description` text,
  `products_short_description` text,
  `products_keywords` varchar(255) default NULL,
  `products_url` varchar(255) default NULL,
  `products_store_id` INT NOT NULL,
  PRIMARY KEY  (`products_id`,`language_code`,`products_store_id`),
  INDEX `language_code` (`language_code`),
  KEY `products_store_id` (`products_store_id`),
  FULLTEXT KEY `products_name` (`products_name`),
  FULLTEXT KEY `products_description` (`products_description`),
  FULLTEXT KEY `products_short_description` (`products_short_description`),
  FULLTEXT KEY `products_keywords` (`products_keywords`)
) ENGINE=#E_ DEFAULT CHARSET=utf8mb4;


CREATE TABLE `##_products_permission` (
  `pid` INT NOT NULL,
  `permission` TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `pgroup` varchar(255) NOT NULL,
  PRIMARY KEY  (`pid`,`pgroup`)
) ENGINE=#E_ DEFAULT CHARSET=utf8;


CREATE TABLE `##_products_price_group_1` (
  `id` INT NOT NULL auto_increment,
  `products_id` INT NOT NULL,
  `discount_quantity` INT default NULL,
  `price` decimal(15,4) default NULL,
  PRIMARY KEY  (`id`),
  KEY `products_id` (`products_id`)
) ENGINE=#E_ DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE `##_products_price_group_2` (
  `id` INT NOT NULL auto_increment,
  `products_id` INT NOT NULL,
  `discount_quantity` INT default NULL,
  `price` decimal(15,4) default NULL,
  PRIMARY KEY  (`id`),
  KEY `products_id` (`products_id`)
) ENGINE=#E_ DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE `##_products_price_group_3` (
  `id` INT NOT NULL auto_increment,
  `products_id` INT NOT NULL,
  `discount_quantity` INT default NULL,
  `price` decimal(15,4) default NULL,
  PRIMARY KEY  (`id`),
  KEY `products_id` (`products_id`)
) ENGINE=#E_ DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE `##_products_price_group_4` (
  `id` INT NOT NULL auto_increment,
  `products_id` INT NOT NULL,
  `discount_quantity` INT default NULL,
  `price` decimal(15,4) default NULL,
  PRIMARY KEY  (`id`),
  KEY `products_id` (`products_id`)
) ENGINE=#E_ DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE `##_products_price_group_all` (
  `id` INT NOT NULL auto_increment,
  `products_id` INT NOT NULL,
  `discount_quantity` INT default NULL,
  `price` decimal(15,4) default NULL,
  PRIMARY KEY  (`id`),
  KEY `products_id` (`products_id`)
) ENGINE=#E_ DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE `##_products_price_special` (
  `id` INT NOT NULL auto_increment,
  `products_id` INT NOT NULL,
  `specials_price` decimal(15,4) NOT NULL,
  `date_available` datetime default NULL,
  `date_expired` datetime default NULL,
  `status` TINYINT UNSIGNED NOT NULL DEFAULT 1,
  `group_permission_all` TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `group_permission_1` TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `group_permission_2` TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `group_permission_3` TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `group_permission_4` TINYINT UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY  (`id`)
) ENGINE=#E_ DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE `##_products_to_categories` (
  `products_id` INT NOT NULL,
  `categories_id` INT NOT NULL,
  `master_link` TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `store_id` INT NOT NULL,
  PRIMARY KEY `products_id_categories_id_store_id_master_link` (`products_id`, `categories_id`, `store_id`),
  UNIQUE KEY `products_id_categories_id_store_id_master_link` (`products_id`, `categories_id`, `store_id`, `master_link`),
  KEY `categories_id` (`categories_id`),
  KEY `master_link` (`master_link`)
) ENGINE=#E_ DEFAULT CHARSET=utf8;

CREATE TABLE `##_seo_url` (
  `url_md5` varchar(32) NOT NULL,
  `url_text` varchar(2048) NOT NULL,
  `language_code` char(2) NOT NULL default '0',
  `link_type` INT NOT NULL,
  `link_id` INT NOT NULL,
  `meta_title` varchar(255) default NULL,
  `meta_description` text,
  `meta_keywords` text,
  `store_id` INT NOT NULL,
  PRIMARY KEY  (`url_md5`,`store_id`,`language_code`),
  KEY `link_id` (`link_id`),
  KEY `language_code` (`language_code`),
  KEY `link_type` (`link_type`),
  KEY `store_id` (`store_id`),
  FULLTEXT KEY `url_text` (`url_text`)
) ENGINE=#E_ DEFAULT CHARSET=utf8;

CREATE TABLE `##_sessions2` (
  `sesskey` varchar(64) NOT NULL default '',
  `expiry` datetime NOT NULL,
  `expireref` varchar(250) default '',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `sessdata` longtext,
  PRIMARY KEY  (`sesskey`),
  KEY `sess2_expiry` (`expiry`),
  KEY `sess2_expireref` (`expireref`)
) ENGINE=#E_ DEFAULT CHARSET=utf8;


CREATE TABLE `##_shipping` (
  `shipping_id` INT NOT NULL auto_increment,
  `shipping_code` varchar(32) NOT NULL,
  `shipping_dir` varchar(255) default NULL,
  `shipping_icon` varchar(255) default NULL,
  `shipping_tax_class` INT default NULL,
  `status` TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `sort_order` INT default NULL,
  `shipping_type` varchar(32) default NULL,
  `shipping_tpl` varchar(64) default NULL,
  `use_shipping_zone` TINYINT UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY  (`shipping_id`)
) ENGINE=#E_ DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE `##_shipping_cost` (
  `shipping_cost_id` INT NOT NULL auto_increment,
  `shipping_id` INT NOT NULL,
  `shipping_geo_zone` INT default '0',
  `shipping_country_code` char(2) default NULL,
  `shipping_type_value_from` decimal(15,4) default '0',
  `shipping_type_value_to` decimal(15,4) default '0',
  `shipping_price` decimal(15,4) default '0',
  `shipping_allowed` TINYINT UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY  (`shipping_cost_id`)
) ENGINE=#E_ DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE `##_shipping_description` (
  `shipping_id` INT NOT NULL,
  `language_code` char(2) NOT NULL,
  `shipping_name` varchar(255) default NULL,
  `shipping_desc` text,
  `shipping_email_desc_txt` text,
  `shipping_email_desc_html` text,
  PRIMARY KEY  (`language_code`,`shipping_id`)
) ENGINE=#E_ DEFAULT CHARSET=utf8;

CREATE TABLE `##_stores` (
  `shop_id` INT NOT NULL auto_increment,
  `shop_ssl_domain` varchar(255) default NULL,
  `shop_ssl` TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `shop_status` TINYINT UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY  (`shop_id`)
) ENGINE=#E_ DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


INSERT INTO `##_stores` VALUES (1, 'localhost', 0, 1);

CREATE TABLE `##_system_log` (
  `log_id` INT NOT NULL auto_increment,
  `class` varchar(32) NOT NULL,
  `message_source` varchar(64) NOT NULL,
  `identification` INT NOT NULL,
  `data` longtext NOT NULL,
  `created` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `popuptrigger` TINYINT UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY  (`log_id`),
  INDEX `popuptrigger` (`popuptrigger`),
  FULLTEXT KEY `class` (`class`,`message_source`)
) ENGINE=#E_ DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE `##_shipping_zones` (
  `zone_id` INT NOT NULL auto_increment,
  `zone_name` varchar(32) default NULL,
  `zone_countries` text,
  PRIMARY KEY  (`zone_id`)
) ENGINE=#E_ DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE `##_system_status` (
  `status_id` INT NOT NULL auto_increment,
  `status_class` varchar(32) default NULL,
  `status_values` text,
  PRIMARY KEY  (`status_id`)
) ENGINE=#E_ DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE `##_media_download_ip` (
 `user_ip` VARCHAR( 32 ) NOT NULL ,
 `download_time` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
 `media_id` int NOT NULL
) ENGINE=#E_ DEFAULT CHARSET=utf8;


INSERT INTO `##_system_status` VALUES (1, 'stock_rule', 'a:2:{s:4:"data";a:1:{s:10:"percentage";s:3:"100";}s:7:"sorting";s:10:"percentage";}');
INSERT INTO `##_system_status` VALUES (2, 'stock_rule', 'a:2:{s:4:"data";a:1:{s:10:"percentage";s:2:"80";}s:7:"sorting";s:10:"percentage";}');
INSERT INTO `##_system_status` VALUES (3, 'stock_rule', 'a:2:{s:4:"data";a:1:{s:10:"percentage";s:2:"50";}s:7:"sorting";s:10:"percentage";}');
INSERT INTO `##_system_status` VALUES (4, 'stock_rule', 'a:2:{s:4:"data";a:1:{s:10:"percentage";s:1:"0";}s:7:"sorting";s:10:"percentage";}');
INSERT INTO `##_system_status` VALUES (5, 'stock_rule', 'a:2:{s:4:"data";a:1:{s:10:"percentage";s:2:"20";}s:7:"sorting";s:10:"percentage";}');
INSERT INTO `##_system_status` VALUES (6, 'shipping_status', NULL);
INSERT INTO `##_system_status` VALUES (7, 'shipping_status', NULL);
INSERT INTO `##_system_status` VALUES (8, 'shipping_status', NULL);
INSERT INTO `##_system_status` VALUES (9, 'base_price', NULL);
INSERT INTO `##_system_status` VALUES (10, 'base_price', NULL);
INSERT INTO `##_system_status` VALUES (11, 'base_price', NULL);
INSERT INTO `##_system_status` VALUES (12, 'base_price', NULL);
INSERT INTO `##_system_status` VALUES (13, 'base_price', NULL);
INSERT INTO `##_system_status` VALUES (14, 'base_price', NULL);
INSERT INTO `##_system_status` VALUES (15, 'base_price', NULL);

INSERT INTO `##_system_status` VALUES (16, 'order_status', 'a:1:{s:4:"data";a:5:{s:15:"enable_download";i:0;s:7:"visible";s:1:"1";s:13:"visible_admin";i:1;s:19:"calculate_statistic";i:0;s:12:"reduce_stock";i:0;}}');
INSERT INTO `##_system_status` VALUES (17, 'order_status', 'a:1:{s:4:"data";a:5:{s:15:"enable_download";i:0;s:7:"visible";s:1:"1";s:13:"visible_admin";i:1;s:19:"calculate_statistic";i:0;s:12:"reduce_stock";i:0;}}');
INSERT INTO `##_system_status` VALUES (23, 'order_status', 'a:1:{s:4:"data";a:5:{s:15:"enable_download";i:1;s:7:"visible";s:1:"1";s:13:"visible_admin";i:1;s:19:"calculate_statistic";i:1;s:12:"reduce_stock";i:0;}}');
INSERT INTO `##_system_status` VALUES (32, 'order_status', 'a:1:{s:4:"data";a:5:{s:15:"enable_download";i:0;s:7:"visible";s:1:"1";s:13:"visible_admin";i:1;s:19:"calculate_statistic";i:0;s:12:"reduce_stock";i:0;}}');
INSERT INTO `##_system_status` VALUES (33, 'order_status', 'a:1:{s:4:"data";a:5:{s:15:"enable_download";i:1;s:7:"visible";s:1:"1";s:13:"visible_admin";i:1;s:19:"calculate_statistic";i:1;s:12:"reduce_stock";i:0;}}');
INSERT INTO `##_system_status` VALUES (34, 'order_status', 'a:1:{s:4:"data";a:5:{s:15:"enable_download";i:0;s:7:"visible";s:1:"1";s:13:"visible_admin";i:1;s:19:"calculate_statistic";i:0;s:12:"reduce_stock";i:1;}}');

INSERT INTO `##_system_status` VALUES (60, 'order_status', 'a:1:{s:4:"data";a:5:{s:15:"enable_download";i:0;s:7:"visible";s:1:"1";s:13:"visible_admin";i:1;s:19:"calculate_statistic";i:0;s:12:"reduce_stock";i:1;}}');
INSERT INTO `##_system_status` VALUES (61, 'order_status', 'a:1:{s:4:"data";a:5:{s:15:"enable_download";i:0;s:7:"visible";s:1:"1";s:13:"visible_admin";i:1;s:19:"calculate_statistic";i:0;s:12:"reduce_stock";i:1;}}');
INSERT INTO `##_system_status` VALUES (62, 'order_status', 'a:1:{s:4:"data";a:5:{s:15:"enable_download";i:0;s:7:"visible";s:1:"1";s:13:"visible_admin";i:1;s:19:"calculate_statistic";i:0;s:12:"reduce_stock";i:1;}}');
INSERT INTO `##_system_status` VALUES (63, 'order_status', 'a:1:{s:4:"data";a:5:{s:15:"enable_download";i:0;s:7:"visible";s:1:"1";s:13:"visible_admin";i:1;s:19:"calculate_statistic";i:0;s:12:"reduce_stock";i:1;}}');
INSERT INTO `##_system_status` VALUES (64, 'order_status', 'a:1:{s:4:"data";a:5:{s:15:"enable_download";i:0;s:7:"visible";s:1:"1";s:13:"visible_admin";i:1;s:19:"calculate_statistic";i:0;s:12:"reduce_stock";i:1;}}');
INSERT INTO `##_system_status` VALUES (65, 'order_status', 'a:1:{s:4:"data";a:5:{s:15:"enable_download";i:0;s:7:"visible";s:1:"1";s:13:"visible_admin";i:1;s:19:"calculate_statistic";i:0;s:12:"reduce_stock";i:1;}}');
INSERT INTO `##_system_status` VALUES (66, 'order_status', 'a:1:{s:4:"data";a:5:{s:15:"enable_download";i:0;s:7:"visible";s:1:"1";s:13:"visible_admin";i:1;s:19:"calculate_statistic";i:0;s:12:"reduce_stock";i:1;}}');

INSERT INTO `##_system_status` VALUES (24, 'zone', 'a:1:{s:4:"data";a:2:{s:33:"_ZONE_VAT_CUSTOMERS_STATUS_shop_1";s:0:"";s:29:"_ZONE_CUSTOMERS_STATUS_shop_1";s:0:"";}}');
INSERT INTO `##_system_status` VALUES (25, 'zone', 'a:1:{s:4:"data";a:2:{s:33:"_ZONE_VAT_CUSTOMERS_STATUS_shop_1";s:0:"";s:29:"_ZONE_CUSTOMERS_STATUS_shop_1";s:0:"";}}');
INSERT INTO `##_system_status` VALUES (26, 'zone', 'a:1:{s:4:"data";a:2:{s:33:"_ZONE_VAT_CUSTOMERS_STATUS_shop_1";s:0:"";s:29:"_ZONE_CUSTOMERS_STATUS_shop_1";s:0:"";}}');
INSERT INTO `##_system_status` VALUES (27, 'zone', 'a:1:{s:4:"data";a:2:{s:33:"_ZONE_VAT_CUSTOMERS_STATUS_shop_1";s:0:"";s:29:"_ZONE_CUSTOMERS_STATUS_shop_1";s:0:"";}}');
INSERT INTO `##_system_status` VALUES (28, 'zone', 'a:1:{s:4:"data";a:2:{s:33:"_ZONE_VAT_CUSTOMERS_STATUS_shop_1";s:0:"";s:29:"_ZONE_CUSTOMERS_STATUS_shop_1";s:0:"";}}');
INSERT INTO `##_system_status` VALUES (29, 'zone', 'a:1:{s:4:"data";a:2:{s:33:"_ZONE_VAT_CUSTOMERS_STATUS_shop_1";s:0:"";s:29:"_ZONE_CUSTOMERS_STATUS_shop_1";s:0:"";}}');
INSERT INTO `##_system_status` VALUES (30, 'zone', 'a:1:{s:4:"data";a:2:{s:33:"_ZONE_VAT_CUSTOMERS_STATUS_shop_1";s:0:"";s:29:"_ZONE_CUSTOMERS_STATUS_shop_1";s:0:"";}}');
INSERT INTO `##_system_status` VALUES (31, 'zone', 'a:1:{s:4:"data";a:2:{s:33:"_ZONE_VAT_CUSTOMERS_STATUS_shop_1";s:0:"";s:29:"_ZONE_CUSTOMERS_STATUS_shop_1";s:0:"";}}');

INSERT INTO `##_system_status` VALUES (35, 'campaign', 'a:1:{s:4:\"data\";a:1:{s:6:\"ref_id\";s:64:\"&utm_source=google&utm_medium=preisvergleich&utm_campaign=google\";}}');
INSERT INTO `##_system_status` VALUES (36, 'campaign', 'a:1:{s:4:\"data\";a:1:{s:6:\"ref_id\";s:68:\"&utm_source=geizhals&utm_medium=preisvergleich&utm_campaign=geizhals\";}}');

INSERT INTO `##_system_status` VALUES (37, 'base_price', NULL);
INSERT INTO `##_system_status` VALUES (38, 'base_price', NULL);
INSERT INTO `##_system_status` VALUES (39, 'base_price', NULL);
INSERT INTO `##_system_status` VALUES (40, 'base_price', NULL);
INSERT INTO `##_system_status` VALUES (41, 'base_price', NULL);
INSERT INTO `##_system_status` VALUES (42, 'base_price', NULL);
INSERT INTO `##_system_status` VALUES (43, 'base_price', NULL);
INSERT INTO `##_system_status` VALUES (44, 'base_price', NULL);
INSERT INTO `##_system_status` VALUES (45, 'base_price', NULL);

CREATE TABLE `##_system_status_description` (
  `status_id` INT NOT NULL,
  `language_code` char(2) NOT NULL default '',
  `status_name` varchar(64) default NULL,
  `status_image` varchar(64) default NULL,
  PRIMARY KEY  (`status_id`,`language_code`)
) ENGINE=#E_ DEFAULT CHARSET=utf8;

CREATE TABLE `##_seo_stop_words` (
  `stop_word_id` INT NOT NULL auto_increment,
  `language_code` char(3) default NULL,
  `stopword_lookup` varchar(255) default NULL,
  `stopword_replacement` varchar(255) default '',
  `replace_word` TINYINT UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY  (`stop_word_id`),
  KEY `language_code` (`language_code`)
) ENGINE=#E_ DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE `##_tax_class` (
  `tax_class_id` INT NOT NULL auto_increment,
  `tax_class_title` varchar(32) NOT NULL,
  `last_modified` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp,
  `date_added` datetime NOT NULL DEFAULT current_timestamp,
  `is_digital_tax` TINYINT UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY  (`tax_class_id`)
) ENGINE=#E_ DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE `##_tax_rates` (
  `tax_rates_id` INT NOT NULL auto_increment,
  `tax_zone_id` INT NOT NULL default 0,
  `tax_class_id` INT NOT NULL default 0,
  `tax_rate` decimal(7,4) NOT NULL default '0.0000',
  `last_modified` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp,
  `date_added` datetime NOT NULL DEFAULT current_timestamp,
  `tax_rate_countries` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY  (`tax_rates_id`)
) ENGINE=#E_ DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


INSERT INTO `##_acl_area` (`area_name`, `area_description`, `category`) VALUES('category', 'Erlaubt Zugriff auf die Kategorieverwaltung', 'default');
INSERT INTO `##_acl_area` (`area_name`, `area_description`, `category`) VALUES('multistore', NULL, 'default');
INSERT INTO `##_acl_area` (`area_name`, `area_description`, `category`) VALUES('product', 'Erlaubt Zugriff auf die Produktverwaltung', 'default');
INSERT INTO `##_acl_area` (`area_name`, `area_description`, `category`) VALUES('supportcenter', NULL, 'default');
INSERT INTO `##_acl_area` (`area_name`, `area_description`, `category`) VALUES('documentation', 'Erlaubt den Zugriff auf die xt:Commerce Produktdokumentation.', 'default');
INSERT INTO `##_acl_area` (`area_name`, `area_description`, `category`) VALUES('order', 'Erlaubt Zugriff auf Bestellungen', 'default');
INSERT INTO `##_acl_area` (`area_name`, `area_description`, `category`) VALUES('customer', 'Erlaubt Zugriff auf die Kundendaten', 'default');
INSERT INTO `##_acl_area` (`area_name`, `area_description`, `category`) VALUES('customers_status', 'Erlaubt Zugriff auf die Kundengruppen und deren Kundenzuweisung', 'default');
INSERT INTO `##_acl_area` (`area_name`, `area_description`, `category`) VALUES('email_manager', 'Erlaubt Zugriff auf die Maileinstellungen', 'default');
INSERT INTO `##_acl_area` (`area_name`, `area_description`, `category`) VALUES('manufacturer', 'Erlaubt Zugriff auf die Hersteller', 'default');
INSERT INTO `##_acl_area` (`area_name`, `area_description`, `category`) VALUES('xt_reviews', 'Erlaubt Zugriff auf die Bewertungen', 'default');
INSERT INTO `##_acl_area` (`area_name`, `area_description`, `category`) VALUES('payment', 'Erlaubt Zugriff auf die Zahlungsarten', 'default');
INSERT INTO `##_acl_area` (`area_name`, `area_description`, `category`) VALUES('shipping', 'Erlaubt Zugriff auf die Versandarten', 'default');
INSERT INTO `##_acl_area` (`area_name`, `area_description`, `category`) VALUES('acl_area', NULL, 'default');
INSERT INTO `##_acl_area` (`area_name`, `area_description`, `category`) VALUES('acl_groups', 'Erlaubt Zugriff auf die Admin-Gruppen', 'default');
INSERT INTO `##_acl_area` (`area_name`, `area_description`, `category`) VALUES('acl_user', 'Erlaubt Zugriff auf die Administratoren', 'default');
INSERT INTO `##_acl_area` (`area_name`, `area_description`, `category`) VALUES('acl_group_to_permission', 'Erlaubt Zugriff auf die Admin-Gruppenberechtigungen', 'default');
INSERT INTO `##_acl_area` (`area_name`, `area_description`, `category`) VALUES('configuration', 'Erlaubt Zugriff auf die Konfiguration des Shopsystems', 'default');
INSERT INTO `##_acl_area` (`area_name`, `area_description`, `category`) VALUES('MediaImageManager', 'Erlaubt Zugriff auf den Mediamanager für Bilder', 'default');
INSERT INTO `##_acl_area` (`area_name`, `area_description`, `category`) VALUES('MediaFileManager', 'Erlaubt Zugriff auf den Media-Manager für Dateien', 'default');
INSERT INTO `##_acl_area` (`area_name`, `area_description`, `category`) VALUES('MediaFileTypes', 'Erlaubt Zugriff auf den Media-Manager für Dateitypen', 'default');
INSERT INTO `##_acl_area` (`area_name`, `area_description`, `category`) VALUES('ImageTypes', 'Erlaubt Zugriff auf den Media-Manager für Bildtypen', 'default');
INSERT INTO `##_acl_area` (`area_name`, `area_description`, `category`) VALUES('MediaImages', NULL, 'default');
INSERT INTO `##_acl_area` (`area_name`, `area_description`, `category`) VALUES('MediaGallery', NULL, 'default');
INSERT INTO `##_acl_area` (`area_name`, `area_description`, `category`) VALUES('plugin_installed', 'Erlaubt Zugriff auf die Installierten Plugins', 'default');
INSERT INTO `##_acl_area` (`area_name`, `area_description`, `category`) VALUES('plugin_uninstalled', 'Erlaubt Zugriff auf die nicht installierten Plugins', 'default');
INSERT INTO `##_acl_area` (`area_name`, `area_description`, `category`) VALUES('tax', 'Erlaubt Zugriff auf die Steuersätze', 'default');
INSERT INTO `##_acl_area` (`area_name`, `area_description`, `category`) VALUES('currency', 'Erlaubt Zugriff auf die W&auml;hrungen', 'default');
INSERT INTO `##_acl_area` (`area_name`, `area_description`, `category`) VALUES('tax_class', 'Erlaubt Zugriff auf die Steuerklassen', 'default');
INSERT INTO `##_acl_area` (`area_name`, `area_description`, `category`) VALUES('countries', 'Erlaubt Zugriff auf die Länderlisten', 'default');
INSERT INTO `##_acl_area` (`area_name`, `area_description`, `category`) VALUES('language', 'Erlaubt Zugriff auf die Sprachen', 'default');
INSERT INTO `##_acl_area` (`area_name`, `area_description`, `category`) VALUES('language_content', 'Erlaubt Zugriff auf die Sprachinhalte', 'default');
INSERT INTO `##_acl_area` (`area_name`, `area_description`, `category`) VALUES('bruto_force_protection', NULL, 'default');
INSERT INTO `##_acl_area` (`area_name`, `area_description`, `category`) VALUES('callback', NULL, 'default');
INSERT INTO `##_acl_area` (`area_name`, `area_description`, `category`) VALUES('shipping_price', 'Erlaubt Zugriff auf die Versandpreise', 'default');
INSERT INTO `##_acl_area` (`area_name`, `area_description`, `category`) VALUES('payment_price', 'Erlaubt Zugriff auf die Aufpreise von Zahlungsarten', 'default');
INSERT INTO `##_acl_area` (`area_name`, `area_description`, `category`) VALUES('stock_rule', 'Erlaubt zugriff auf die Zuordnung des Lagerbestandes', 'default');
INSERT INTO `##_acl_area` (`area_name`, `area_description`, `category`) VALUES('shipping_status', 'Erlaubt Zugriff auf die Versndstatis', 'default');
INSERT INTO `##_acl_area` (`area_name`, `area_description`, `category`) VALUES('base_price', NULL, 'default');
INSERT INTO `##_acl_area` (`area_name`, `area_description`, `category`) VALUES('order_status', 'Erlaubt Zugriff auf die Bestellstatis', 'default');
INSERT INTO `##_acl_area` (`area_name`, `area_description`, `category`) VALUES('zone', 'Erlaubt Zugriff auf die Zonenverwaltung', 'default');
INSERT INTO `##_acl_area` (`area_name`, `area_description`, `category`) VALUES('campaign', 'Erlaubt Zugriff auf die Kampagnen', 'default');
INSERT INTO `##_acl_area` (`area_name`, `area_description`, `category`) VALUES('export', 'Erlaubt Zugriff auf die Exportverwaltung', 'default');
INSERT INTO `##_acl_area` (`area_name`, `area_description`, `category`) VALUES('content', 'Erlaubt Zugriff auf die Contents', 'default');
INSERT INTO `##_acl_area` (`area_name`, `area_description`, `category`) VALUES('content_blocks', 'Erlaubt Zugriff auf die Content Bl&ouml;cke', 'default');
INSERT INTO `##_acl_area` (`area_name`, `area_description`, `category`) VALUES('address', 'Erlaubt Zugriff auf die Kundenadressen', 'default');
INSERT INTO `##_acl_area` (`area_name`, `area_description`, `category`) VALUES('cross_selling_products', 'Erlaubt Zugriff auf das Produkt-Cross-Selling', 'default');
INSERT INTO `##_acl_area` (`area_name`, `area_description`, `category`) VALUES('product_to_media', 'Erlaubt Zugriff auf die Zuordnung von Produkten zu Medien ', 'default');
INSERT INTO `##_acl_area` (`area_name`, `area_description`, `category`) VALUES('product_to_cat', 'Erlaubt Zugriff auf die Zuordnung von Produkten zu Kategorien', 'default');
INSERT INTO `##_acl_area` (`area_name`, `area_description`, `category`) VALUES('product_to_mastercat', 'Erlaubt Zugriff auf die Zuordnung von Produkten zu Masterkategorien', 'default');
INSERT INTO `##_acl_area` (`area_name`, `area_description`, `category`) VALUES('plugin_hookpoints', 'Erlaubt Zugriff auf die Hookpoints von Plugins', 'default');
INSERT INTO `##_acl_area` (`area_name`, `area_description`, `category`) VALUES('xt_master_slave', 'Erlaubt Zugriff auf das Master-Slave System', 'default');
INSERT INTO `##_acl_area` (`area_name`, `area_description`, `category`) VALUES('xt_im_export', 'Erlaubt Zugriff auf die Im- und Export Verwaltung', 'default');
INSERT INTO `##_acl_area` (`area_name`, `area_description`, `category`) VALUES('cross_selling_to_products', 'Erlaubt Zugriff auf die Zuordnung von Cross-Selling Produkten', 'default');



CREATE TABLE `##_plugin_history` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `plugin_code` varchar(255) CHARACTER SET utf8 NOT NULL,
  `old_version` varchar(32) CHARACTER SET utf8 NOT NULL,
  `current_version` varchar(32) CHARACTER SET utf8 DEFAULT NULL,
  `xml` text CHARACTER SET utf8,
  `date_added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=#E_ DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

INSERT INTO `##_federal_states` (`states_id`, `states_code`, `country_iso_code_2`, `status`) VALUES (1, 'AL', 'US', 1);
INSERT INTO `##_federal_states` (`states_id`, `states_code`, `country_iso_code_2`, `status`) VALUES (2, 'AK', 'US', 1);
INSERT INTO `##_federal_states` (`states_id`, `states_code`, `country_iso_code_2`, `status`) values (3, 'AS', 'US', 1);
INSERT INTO `##_federal_states` (`states_id`, `states_code`, `country_iso_code_2`, `status`) values (4, 'AZ', 'US', 1);
INSERT INTO `##_federal_states` (`states_id`, `states_code`, `country_iso_code_2`, `status`) values (5, 'AR', 'US', 1);
INSERT INTO `##_federal_states` (`states_id`, `states_code`, `country_iso_code_2`, `status`) values (6, 'CA', 'US', 1);
INSERT INTO `##_federal_states` (`states_id`, `states_code`, `country_iso_code_2`, `status`) values (7, 'CO', 'US', 1);
INSERT INTO `##_federal_states` (`states_id`, `states_code`, `country_iso_code_2`, `status`) values (8, 'CT', 'US', 1);
INSERT INTO `##_federal_states` (`states_id`, `states_code`, `country_iso_code_2`, `status`) values (9, 'DE', 'US', 1);
INSERT INTO `##_federal_states` (`states_id`, `states_code`, `country_iso_code_2`, `status`) values (10, 'DC', 'US', 1);
INSERT INTO `##_federal_states` (`states_id`, `states_code`, `country_iso_code_2`, `status`) values (11, 'FM', 'US', 1);
INSERT INTO `##_federal_states` (`states_id`, `states_code`, `country_iso_code_2`, `status`) values (12, 'FL', 'US', 1);
INSERT INTO `##_federal_states` (`states_id`, `states_code`, `country_iso_code_2`, `status`) values (13, 'GA', 'US', 1);
INSERT INTO `##_federal_states` (`states_id`, `states_code`, `country_iso_code_2`, `status`) values (14, 'GU', 'US', 1);
INSERT INTO `##_federal_states` (`states_id`, `states_code`, `country_iso_code_2`, `status`) values (15, 'HI', 'US', 1);
INSERT INTO `##_federal_states` (`states_id`, `states_code`, `country_iso_code_2`, `status`) values (16, 'ID', 'US', 1);
INSERT INTO `##_federal_states` (`states_id`, `states_code`, `country_iso_code_2`, `status`) values (17, 'IL', 'US', 1);
INSERT INTO `##_federal_states` (`states_id`, `states_code`, `country_iso_code_2`, `status`) values (18, 'IN', 'US', 1);
INSERT INTO `##_federal_states` (`states_id`, `states_code`, `country_iso_code_2`, `status`) values (19, 'IA', 'US', 1);
INSERT INTO `##_federal_states` (`states_id`, `states_code`, `country_iso_code_2`, `status`) values (20, 'KS', 'US', 1);
INSERT INTO `##_federal_states` (`states_id`, `states_code`, `country_iso_code_2`, `status`) values (21, 'KY', 'US', 1);
INSERT INTO `##_federal_states` (`states_id`, `states_code`, `country_iso_code_2`, `status`) values (22, 'LA', 'US', 1);
INSERT INTO `##_federal_states` (`states_id`, `states_code`, `country_iso_code_2`, `status`) values (23, 'ME', 'US', 1);
INSERT INTO `##_federal_states` (`states_id`, `states_code`, `country_iso_code_2`, `status`) values (24, 'MH', 'US', 1);
INSERT INTO `##_federal_states` (`states_id`, `states_code`, `country_iso_code_2`, `status`) values (25, 'MD', 'US', 1);
INSERT INTO `##_federal_states` (`states_id`, `states_code`, `country_iso_code_2`, `status`) values (26, 'MA', 'US', 1);
INSERT INTO `##_federal_states` (`states_id`, `states_code`, `country_iso_code_2`, `status`) values (27, 'MI', 'US', 1);
INSERT INTO `##_federal_states` (`states_id`, `states_code`, `country_iso_code_2`, `status`) values (28, 'MN', 'US', 1);
INSERT INTO `##_federal_states` (`states_id`, `states_code`, `country_iso_code_2`, `status`) values (29, 'MS', 'US', 1);
INSERT INTO `##_federal_states` (`states_id`, `states_code`, `country_iso_code_2`, `status`) values (30, 'MO', 'US', 1);
INSERT INTO `##_federal_states` (`states_id`, `states_code`, `country_iso_code_2`, `status`) values (31, 'MT', 'US', 1);
INSERT INTO `##_federal_states` (`states_id`, `states_code`, `country_iso_code_2`, `status`) values (32, 'NE', 'US', 1);
INSERT INTO `##_federal_states` (`states_id`, `states_code`, `country_iso_code_2`, `status`) values (33, 'NV', 'US', 1);
INSERT INTO `##_federal_states` (`states_id`, `states_code`, `country_iso_code_2`, `status`) values (34, 'NH', 'US', 1);
INSERT INTO `##_federal_states` (`states_id`, `states_code`, `country_iso_code_2`, `status`) values (35, 'NJ', 'US', 1);
INSERT INTO `##_federal_states` (`states_id`, `states_code`, `country_iso_code_2`, `status`) values (36, 'NM', 'US', 1);
INSERT INTO `##_federal_states` (`states_id`, `states_code`, `country_iso_code_2`, `status`) values (37, 'NY', 'US', 1);
INSERT INTO `##_federal_states` (`states_id`, `states_code`, `country_iso_code_2`, `status`) values (38, 'NC', 'US', 1);
INSERT INTO `##_federal_states` (`states_id`, `states_code`, `country_iso_code_2`, `status`) values (39, 'ND', 'US', 1);
INSERT INTO `##_federal_states` (`states_id`, `states_code`, `country_iso_code_2`, `status`) values (40, 'MP', 'US', 1);
INSERT INTO `##_federal_states` (`states_id`, `states_code`, `country_iso_code_2`, `status`) values (41, 'OH', 'US', 1);
INSERT INTO `##_federal_states` (`states_id`, `states_code`, `country_iso_code_2`, `status`) values (42, 'OK', 'US', 1);
INSERT INTO `##_federal_states` (`states_id`, `states_code`, `country_iso_code_2`, `status`) values (43, 'OR', 'US', 1);
INSERT INTO `##_federal_states` (`states_id`, `states_code`, `country_iso_code_2`, `status`) values (44, 'PW', 'US', 1);
INSERT INTO `##_federal_states` (`states_id`, `states_code`, `country_iso_code_2`, `status`) values (45, 'PA', 'US', 1);
INSERT INTO `##_federal_states` (`states_id`, `states_code`, `country_iso_code_2`, `status`) values (46, 'PR', 'US', 1);
INSERT INTO `##_federal_states` (`states_id`, `states_code`, `country_iso_code_2`, `status`) values (47, 'RI', 'US', 1);
INSERT INTO `##_federal_states` (`states_id`, `states_code`, `country_iso_code_2`, `status`) values (48, 'SC', 'US', 1);
INSERT INTO `##_federal_states` (`states_id`, `states_code`, `country_iso_code_2`, `status`) values (49, 'SD', 'US', 1);
INSERT INTO `##_federal_states` (`states_id`, `states_code`, `country_iso_code_2`, `status`) values (50, 'TN', 'US', 1);
INSERT INTO `##_federal_states` (`states_id`, `states_code`, `country_iso_code_2`, `status`) values (51, 'TX', 'US', 1);
INSERT INTO `##_federal_states` (`states_id`, `states_code`, `country_iso_code_2`, `status`) values (52, 'UT', 'US', 1);
INSERT INTO `##_federal_states` (`states_id`, `states_code`, `country_iso_code_2`, `status`) values (53, 'VT', 'US', 1);
INSERT INTO `##_federal_states` (`states_id`, `states_code`, `country_iso_code_2`, `status`) values (54, 'VI', 'US', 1);
INSERT INTO `##_federal_states` (`states_id`, `states_code`, `country_iso_code_2`, `status`) values (55, 'VA', 'US', 1);
INSERT INTO `##_federal_states` (`states_id`, `states_code`, `country_iso_code_2`, `status`) values (56, 'WA', 'US', 1);
INSERT INTO `##_federal_states` (`states_id`, `states_code`, `country_iso_code_2`, `status`) values (57, 'WV', 'US', 1);
INSERT INTO `##_federal_states` (`states_id`, `states_code`, `country_iso_code_2`, `status`) values (58, 'WI', 'US', 1);
INSERT INTO `##_federal_states` (`states_id`, `states_code`, `country_iso_code_2`, `status`) values (59, 'WY', 'US', 1);
INSERT INTO `##_federal_states` (`states_id`, `states_code`, `country_iso_code_2`, `status`) values (60, 'AA', 'US', 1);
INSERT INTO `##_federal_states` (`states_id`, `states_code`, `country_iso_code_2`, `status`) values (61, 'AE', 'US', 1);
INSERT INTO `##_federal_states` (`states_id`, `states_code`, `country_iso_code_2`, `status`) values (62, 'AP', 'US', 1);

INSERT INTO `##_seo_stop_words` (`language_code`, `stopword_lookup`, `stopword_replacement`, `replace_word`) VALUES ('ALL', 'Á', 'A', 1);
INSERT INTO `##_seo_stop_words` (`language_code`, `stopword_lookup`, `stopword_replacement`, `replace_word`) VALUES ('ALL', 'Í', 'I', 1);
INSERT INTO `##_seo_stop_words` (`language_code`, `stopword_lookup`, `stopword_replacement`, `replace_word`) VALUES ('ALL', 'Ò', 'O', 1);
INSERT INTO `##_seo_stop_words` (`language_code`, `stopword_lookup`, `stopword_replacement`, `replace_word`) VALUES ('ALL', 'Ý', 'Y', 1);
INSERT INTO `##_seo_stop_words` (`language_code`, `stopword_lookup`, `stopword_replacement`, `replace_word`) VALUES ('ALL', 'á', 'a', 1);
INSERT INTO `##_seo_stop_words` (`language_code`, `stopword_lookup`, `stopword_replacement`, `replace_word`) VALUES ('ALL', 'é', 'e', 1);
INSERT INTO `##_seo_stop_words` (`language_code`, `stopword_lookup`, `stopword_replacement`, `replace_word`) VALUES ('ALL', 'í', 'i', 1);
INSERT INTO `##_seo_stop_words` (`language_code`, `stopword_lookup`, `stopword_replacement`, `replace_word`) VALUES ('ALL', 'ó', 'o', 1);
INSERT INTO `##_seo_stop_words` (`language_code`, `stopword_lookup`, `stopword_replacement`, `replace_word`) VALUES ('ALL', 'ý', 'y', 1);
INSERT INTO `##_seo_stop_words` (`language_code`, `stopword_lookup`, `stopword_replacement`, `replace_word`) VALUES ('ALL', 'À', 'A', 1);
INSERT INTO `##_seo_stop_words` (`language_code`, `stopword_lookup`, `stopword_replacement`, `replace_word`) VALUES ('ALL', 'È', 'E', 1);
INSERT INTO `##_seo_stop_words` (`language_code`, `stopword_lookup`, `stopword_replacement`, `replace_word`) VALUES ('ALL', 'Ì', 'I', 1);
INSERT INTO `##_seo_stop_words` (`language_code`, `stopword_lookup`, `stopword_replacement`, `replace_word`) VALUES ('ALL', 'Ò', 'O', 1);
INSERT INTO `##_seo_stop_words` (`language_code`, `stopword_lookup`, `stopword_replacement`, `replace_word`) VALUES ('ALL', 'Ù', 'U', 1);
INSERT INTO `##_seo_stop_words` (`language_code`, `stopword_lookup`, `stopword_replacement`, `replace_word`) VALUES ('ALL', 'à', 'a', 1);
INSERT INTO `##_seo_stop_words` (`language_code`, `stopword_lookup`, `stopword_replacement`, `replace_word`) VALUES ('ALL', 'è', 'e', 1);
INSERT INTO `##_seo_stop_words` (`language_code`, `stopword_lookup`, `stopword_replacement`, `replace_word`) VALUES ('ALL', 'é', 'e', 1);
INSERT INTO `##_seo_stop_words` (`language_code`, `stopword_lookup`, `stopword_replacement`, `replace_word`) VALUES ('ALL', 'ì', 'i', 1);
INSERT INTO `##_seo_stop_words` (`language_code`, `stopword_lookup`, `stopword_replacement`, `replace_word`) VALUES ('ALL', 'ò', 'o', 1);
INSERT INTO `##_seo_stop_words` (`language_code`, `stopword_lookup`, `stopword_replacement`, `replace_word`) VALUES ('ALL', 'ù', 'u', 1);
INSERT INTO `##_seo_stop_words` (`language_code`, `stopword_lookup`, `stopword_replacement`, `replace_word`) VALUES ('ALL', 'Â', 'A', 1);
INSERT INTO `##_seo_stop_words` (`language_code`, `stopword_lookup`, `stopword_replacement`, `replace_word`) VALUES ('ALL', 'Ê', 'E', 1);
INSERT INTO `##_seo_stop_words` (`language_code`, `stopword_lookup`, `stopword_replacement`, `replace_word`) VALUES ('ALL', 'Î', 'I', 1);
INSERT INTO `##_seo_stop_words` (`language_code`, `stopword_lookup`, `stopword_replacement`, `replace_word`) VALUES ('ALL', 'Ô', 'O', 1);
INSERT INTO `##_seo_stop_words` (`language_code`, `stopword_lookup`, `stopword_replacement`, `replace_word`) VALUES ('ALL', 'Û', 'U', 1);
INSERT INTO `##_seo_stop_words` (`language_code`, `stopword_lookup`, `stopword_replacement`, `replace_word`) VALUES ('ALL', 'â', 'a', 1);
INSERT INTO `##_seo_stop_words` (`language_code`, `stopword_lookup`, `stopword_replacement`, `replace_word`) VALUES ('ALL', 'ê', 'e', 1);
INSERT INTO `##_seo_stop_words` (`language_code`, `stopword_lookup`, `stopword_replacement`, `replace_word`) VALUES ('ALL', 'î', 'i', 1);
INSERT INTO `##_seo_stop_words` (`language_code`, `stopword_lookup`, `stopword_replacement`, `replace_word`) VALUES ('ALL', 'ô', 'o', 1);
INSERT INTO `##_seo_stop_words` (`language_code`, `stopword_lookup`, `stopword_replacement`, `replace_word`) VALUES ('ALL', 'û', 'u', 1);
INSERT INTO `##_seo_stop_words` (`language_code`, `stopword_lookup`, `stopword_replacement`, `replace_word`) VALUES ('ALL', 'Æ', 'AE', 1);
INSERT INTO `##_seo_stop_words` (`language_code`, `stopword_lookup`, `stopword_replacement`, `replace_word`) VALUES ('ALL', 'Ç', 'C', 1);
INSERT INTO `##_seo_stop_words` (`language_code`, `stopword_lookup`, `stopword_replacement`, `replace_word`) VALUES ('ALL', 'Ð', 'Eth', 1);
INSERT INTO `##_seo_stop_words` (`language_code`, `stopword_lookup`, `stopword_replacement`, `replace_word`) VALUES ('ALL', 'Ø', 'O', 1);
INSERT INTO `##_seo_stop_words` (`language_code`, `stopword_lookup`, `stopword_replacement`, `replace_word`) VALUES ('ALL', 'Þ', 'Thorn', 1);
INSERT INTO `##_seo_stop_words` (`language_code`, `stopword_lookup`, `stopword_replacement`, `replace_word`) VALUES ('ALL', 'æ', 'ae', 1);
INSERT INTO `##_seo_stop_words` (`language_code`, `stopword_lookup`, `stopword_replacement`, `replace_word`) VALUES ('ALL', 'ç', 'c', 1);
INSERT INTO `##_seo_stop_words` (`language_code`, `stopword_lookup`, `stopword_replacement`, `replace_word`) VALUES ('ALL', 'đ', 'eth', 1);
INSERT INTO `##_seo_stop_words` (`language_code`, `stopword_lookup`, `stopword_replacement`, `replace_word`) VALUES ('ALL', 'ø', 'o', 1);
INSERT INTO `##_seo_stop_words` (`language_code`, `stopword_lookup`, `stopword_replacement`, `replace_word`) VALUES ('ALL', 'þ', 'thorn', 1);
INSERT INTO `##_seo_stop_words` (`language_code`, `stopword_lookup`, `stopword_replacement`, `replace_word`) VALUES ('ALL', 'Ä', 'ae', 1);
INSERT INTO `##_seo_stop_words` (`language_code`, `stopword_lookup`, `stopword_replacement`, `replace_word`) VALUES ('ALL', 'Ö', 'oe', 1);
INSERT INTO `##_seo_stop_words` (`language_code`, `stopword_lookup`, `stopword_replacement`, `replace_word`) VALUES ('ALL', 'Ü', 'ue', 1);
INSERT INTO `##_seo_stop_words` (`language_code`, `stopword_lookup`, `stopword_replacement`, `replace_word`) VALUES ('ALL', 'ä', 'ae', 1);
INSERT INTO `##_seo_stop_words` (`language_code`, `stopword_lookup`, `stopword_replacement`, `replace_word`) VALUES ('ALL', 'ü', 'ue', 1);
INSERT INTO `##_seo_stop_words` (`language_code`, `stopword_lookup`, `stopword_replacement`, `replace_word`) VALUES ('ALL', 'ö', 'oe', 1);
INSERT INTO `##_seo_stop_words` (`language_code`, `stopword_lookup`, `stopword_replacement`, `replace_word`) VALUES ('ALL', 'ß', 'ss', 1);


CREATE TABLE `##_search` (
  `id` INT unsigned NOT NULL AUTO_INCREMENT,
  `keyword` varchar(255) NOT NULL,
  `result_count` INT unsigned NOT NULL,
  `request_count` INT unsigned NOT NULL,
  `last_date` date NOT NULL,
  `shop_id` INT NOT NULL,
  PRIMARY KEY (`id`),
   UNIQUE KEY `keyword` (`keyword`,`shop_id`)
) ENGINE=#E_ DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `##_media_languages` (
  `ml_id` INT NOT NULL AUTO_INCREMENT,
  `m_id` INT NOT NULL,
  `language_code` char(2) NOT NULL,
  PRIMARY KEY (`ml_id`),
  UNIQUE KEY `ml_id` (`ml_id`)
) ENGINE=#E_  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `##_mail_templates_attachment` (
  `attachment_id` INT NOT NULL AUTO_INCREMENT,
  `tpl_id` INT NOT NULL,
  `attachment_status` TINYINT UNSIGNED NOT NULL DEFAULT 1,
  `attachment_file` varchar(255) NOT NULL,
  PRIMARY KEY (`attachment_id`)
) ENGINE=#E_ DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `##_download_log` (
  `download_log_id` INT NOT NULL AUTO_INCREMENT,
  `orders_id` INT NOT NULL,
  `media_id` INT,
  `download_action` varchar(255) NOT NULL DEFAULT '',
  `download_count` varchar(255) NOT NULL DEFAULT 1,
  `attempts_left` varchar(255) NOT NULL DEFAULT 0,
  `log_datetime` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `file` varchar(255) NOT NULL,
  PRIMARY KEY (`download_log_id`)
) ENGINE=#E_ DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `##_sales_stats` (
  `sale_stat_id` INT NOT NULL AUTO_INCREMENT,
  `sales_stat_type` TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `shop_id` INT NOT NULL,
  `customers_id` INT default NULL,
  `customers_status` TINYINT UNSIGNED default NULL,
  `products_count` INT NOT NULL,
  `date_added` datetime NOT NULL DEFAULT current_timestamp,
  PRIMARY KEY (`sale_stat_id`)
) ENGINE=#E_ DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `##_seo_url_redirect` (
  `url_md5` varchar(32) NOT NULL,
  `master_key` INT NOT NULL,
  `url_text` varchar(2048) NOT NULL,
  `language_code` char(2) NOT NULL DEFAULT '0',
  `link_type` INT NOT NULL,
  `link_id` INT NOT NULL,
  `store_id` INT NOT NULL,
  `url_text_redirect` varchar(2048) NULL,
  `url_md5_redirect` varchar(32) NULL,
  `is_deleted` TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `total_count` INT NOT NULL DEFAULT 0,
  `count_day_last_access` INT NOT NULL DEFAULT 0,
  `last_access` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  KEY `link_id` (`link_id`),
  FULLTEXT KEY `url_text` (`url_text`)
) ENGINE=#E_ DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `##_pdf_manager` (
  `template_id` INT NOT NULL AUTO_INCREMENT,
  `template_name` varchar(255) NOT NULL,
  `template_type` varchar(64) NOT NULL,
  `template_pdf_out_name` varchar(512) NOT NULL,
  `template_use_be_lng` TINYINT UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`template_id`)
) ENGINE=#E_  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `##_pdf_manager_content` (
  `template_id` INT NOT NULL,
  `language_code` char(2) NOT NULL DEFAULT '',
  `template_body` text NULL,
  PRIMARY KEY (`template_id`,`language_code`)
) ENGINE=#E_ DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `##_slider` (
  `slider_id` INT NOT NULL AUTO_INCREMENT,
  `slide_speed` INT NOT NULL DEFAULT '800',
  `pagination_speed` INT NOT NULL DEFAULT '800',
  `auto_play_speed` INT NOT NULL DEFAULT '7000',
  `slider_note` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`slider_id`)
) ENGINE=#E_ DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `##_slides` (
  `slide_id` INT NOT NULL AUTO_INCREMENT,
  `slider_id` INT NOT NULL DEFAULT '0',
  `slide_language_code` char(2) DEFAULT NULL,
  `slide_status` INT DEFAULT '1',
  `slide_date_from` timestamp NULL DEFAULT NULL,
  `slide_date_to` timestamp NULL DEFAULT NULL,
  `slide_image` varchar(255) DEFAULT NULL,
  `slide_link` varchar(255) DEFAULT NULL,
  `slide_alt_text` varchar(255) DEFAULT NULL,
  `sort_order` INT NOT NULL DEFAULT '0',
  PRIMARY KEY (`slide_id`)
) ENGINE=#E_ DEFAULT CHARSET=utf8;
