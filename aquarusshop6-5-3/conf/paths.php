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

define('_SRV_WEB_CORE', 'xtCore/');
define('_SRV_WEB_PLUGINS', 'plugins/');
define('_SRV_WEB_PLUGIN_CACHE', 'plugin_cache/');

define('_SRV_WEB_PAGES', 'xtCore/pages/');
define('_SRV_WEB_TEMPLATES', 'templates/');
define('_SRV_WEB_LOG', 'xtLogs/');
define('_SRV_WEB_ADMIN', 'xtAdmin/');
define('_SRV_WEB_EXPORT', 'export/');

define('_SRV_WEB_FRAMEWORK', 'xtFramework/');

define('_SRV_WEB_MEDIA_CONTENT', 'media/content/');
define('_SRV_WEB_MEDIA_EXPORT', 'media/export/');
define('_SRV_WEB_MEDIA_IMPORT', 'media/import/');

define('_SRV_WEB_MEDIA_ATTACHMENTS', 'media/files/');
define('_SRV_WEB_MEDIA_DOWNLOADS', 'media/files/');
define('_SRV_WEB_MEDIA_INFORAMTION', 'media/files/');

define('_SRV_WEB_IMAGES', 'media/images/');
define('_SRV_WEB_ICONS', 'media/icons/');
define('_SRV_WEB_MEDIA_FILES', 'media/files/');

define('_SRV_WEB_CRONJOBS', 'cronjobs/');

// Obsolete
/*
define('_SRV_WEB_IMAGES_ICONS', 'media/icons/');
define('_SRV_WEB_IMAGES_BANNER', 'media/images/');
define('_SRV_WEB_IMAGES_OPTIONS', 'media/images/');
define('_SRV_WEB_IMAGES_PRODUCTS', 'media/images/');
define('_SRV_WEB_IMAGES_CATEGORIES', 'media/images/');
define('_SRV_WEB_IMAGES_MANUFACTUERS', 'media/images/');
define('_SRV_WEB_MEDIA_FILE_TYPES', 'media/filetypes/');
*/

define('_DIR_ICON', 'icon/');
define('_DIR_ORG', 'org/');
define('_DIR_THUMB', 'thumb/');
define('_DIR_INFO', 'info/');

// reload backend when changed
define('PHP_EXTJS_DOC_ROOT',_SRV_WEBROOT._SRV_WEB_FRAMEWORK.'library/PhpExt');
define('CK_EDITOR_VERSION','4.22.1');
define('CK_EDITOR_DISTRIBUTION','full');
define('CK_EDITOR_SHOW_ON_FOCUS_ONLY', false);
