<?php

namespace ew_evelations;

use cakebake\lesscss\LessConverter;
use Exception;

/**
 * Plugin class
 */
class plugin
{
    /**
     * Template prefix for template name
     * Example: "ew_evelations"
     */
    const TEMPLATE_PREFIX = 'ew_';

    /**
     * Image Type Prefix for Image dirs
     * Non ASCII chars will be removed by default. For compatibility with versions < 5.0.0 we keep them here.
     * Example: "ewevelationsthumb"
     */
    const IMAGE_TYPE_PREFIX = 'ew-evelations-';

    /**
     * Content Block Prefix
     * Example: "ew_evelations_teaser"
     */
    const CONTENT_BLOCK_PREFIX = 'ew_evelations_';

    /**
     * Name of current plugin
     */
    const PLUGIN_NAME = 'ew_evelations_plugin';

    /**
     * @var null Plugin error message
     */
    public static $pluginErrorMessage = null;

    /**
     * @var bool Force to refresh Minify cache by set this to true
     */
    public static $forceRefreshMinifyCache = false;
    /**
     * @var true
     */
    public bool $testMode = false;
    /**
     * @var array $_categories_array Categories cache; indexed by max_navigation_level
     */
    protected $_categories_array = array();
    /**
     * @var array $_categories_array Flatten storage of categories; indexed by categories_id
     */
    protected $_categories_storage = array();
    /**
     * @var array Plugin config storage
     */
    protected $_configurationArray = array();
    /**
     * @var $_contentsByBlock array Contents storage
     */
    protected $_contentsByBlock = array();

    /**
     * @return bool
     */
    public static function isCompactTeaser()
    {
        return self::check_conf('CONFIG_EW_EVELATIONS_PLUGIN_COMPACT_TEASER');
    }

    /**
     * Is xt:C Template file cache enabled or not
     *
     * @return bool
     */
    public static function isFileCacheAllowed()
    {
        if (!self::isSystemCacheEnabled())
            return false;

        if (!self::isSessionKeyRemoved() && !self::isSessionCookieExistent() && self::isSessionKeyInURI())
            return false;

        return true;
    }

    /**
     * @return bool
     */
    public static function isSessionKeyInURI()
    {
        return strpos($_SERVER['REQUEST_URI'], session_name());
    }

    /**
     * @return bool
     */
    public static function isSessionCookieExistent()
    {
        return isset($_COOKIE[session_name()]);
    }

    /**
     * @return bool
     */
    public static function isSessionKeyRemoved()
    {
        return self::check_conf('_RMV_SESSION');
    }

    /**
     * Get web app icon path
     *
     * @param $fileName
     * @param $fileExtension
     * @return string
     */
    public static function getWebAppIcon($fileName, $fileExtension = 'png')
    {
        $iconDir = _SRV_WEB_TEMPLATES . _STORE_TEMPLATE . '/img/webapp/';
        $defaultIcon = "{$fileName}.{$fileExtension}";
        $mandantIcon = self::get_unique_shop_string("{$fileName}_", ".{$fileExtension}");

        $iconPath = file_exists($iconDir . $defaultIcon) ? $iconDir . $defaultIcon : null;
        $iconPath = file_exists($iconDir . $mandantIcon) ? $iconDir . $mandantIcon : $iconPath;

        return $iconPath;
    }

    /**
     * unique string for every shop
     *
     * @example class::get_unique_shop_string('stylesheet_', '.css'); //output: stylesheet_3.css for shopID3
     * @param string $prefix
     * @param string $suffix
     * @return bool|string
     */
    public static function get_unique_shop_string($prefix = '', $suffix = '')
    {
        if (($id = self::getShopId()) === null)
            return false;

        if (!is_string($prefix) || !is_string($suffix))
            return false;

        return "{$prefix}{$id}{$suffix}";
    }

    /**
     * Get current mandant/shop id
     *
     * @return int|null
     */
    public static function getShopId()
    {
        global $store_handler;

        return isset($store_handler->shop_id) ? (int)$store_handler->shop_id : null;
    }

    /**
     * Defines current shop id
     * @example Usage in smarty: {$smarty.const.EW_EVELATIONS_SHOP_ID}
     */
    public static function setShopIdConstant()
    {
        defined('EW_EVELATIONS_SHOP_ID') or define('EW_EVELATIONS_SHOP_ID', self::getShopId());
    }

    /**
     * Get primary color
     *
     * @return string Hex color
     */
    public static function getPrimaryColor()
    {
        return (defined('CONFIG_EW_EVELATIONS_PLUGIN_PRIMARY_COLOR') && self::isHexColorString(CONFIG_EW_EVELATIONS_PLUGIN_PRIMARY_COLOR)) ? CONFIG_EW_EVELATIONS_PLUGIN_PRIMARY_COLOR : null;
    }

    /**
     * Get secondary color
     *
     * @return string Hex color
     */
    public static function getSecondaryColor()
    {
        return (defined('CONFIG_EW_EVELATIONS_PLUGIN_SECONDARY_COLOR') && self::isHexColorString(CONFIG_EW_EVELATIONS_PLUGIN_SECONDARY_COLOR)) ? CONFIG_EW_EVELATIONS_PLUGIN_SECONDARY_COLOR : null;
    }

    /**
     * Check for a hex color string
     *
     * @param string $color
     * @return bool
     */
    public static function isHexColorString($color)
    {
        if (!is_string($color))
            return false;

        if (@preg_match('/^#[a-f0-9]{6}$/i', $color) !== 1)
            return false;

        return true;
    }

    /**
     * @param array $data
     * @param array $container
     * @return array
     */
    public static function buildMobileMenuContainer(array $data, array $container = [])
    {
        global $current_category_id, $category;

        $currentCID = (int)$current_category_id;
        foreach ($data as $item) {
            $parent = isset($item['parent_id']) ? (int)$item['parent_id'] : 0;
            $id = isset($item['categories_id']) ? (int)$item['categories_id'] : 0;

            // sub recursion
            if (isset($item['sub']) && !empty($item['sub'])) {
                $container = self::buildMobileMenuContainer($item['sub'], $container);
                $item['sub'] = 1;
            }

            // parent info
            if (!isset($container[$parent])) {
                if ($parent !== 0) {
                    $container[$parent] = $category->buildData(array('categories_id' => $parent));
                } else {
                    $container[$parent] = [
                        'categories_id' => $parent,
                        'categories_name' => defined('TEXT_EW_EVELATIONS_NAVBAR_MOBILE_TITLE') ? TEXT_EW_EVELATIONS_NAVBAR_MOBILE_TITLE : 'Menu',
                        'categories_link' => null,
                        'parent_id' => 0,
                    ];
                }
                $container[$parent]['children'] = [];
                $container[$parent]['active'] = false;
            }

            // parent active
            if ($container[$parent]['active'] === false) {
                $active = false;
                foreach ($container as $c) {
                    if (isset($c['active']) && $c['active'] === true) {
                        $active = true;
                        break;
                    }
                }
                if (!$active) {
                    if ($parent === $currentCID) {
                        $container[$parent]['active'] = true;
                    } elseif ($id === $currentCID) {
                        $container[$parent]['active'] = true;
                    } elseif (isset($category->level) && is_array($category->level) && in_array($parent, $category->level)) {
                        $container[$parent]['active'] = true;
                    }
                }
            }

            // collect
            $container[$parent]['children'][$id] = $item;
        }
        return $container;
    }

    /**
     * Build top categories from categories tree
     *
     * @param array  $categoriesTree            Categories data as multidimensional array
     * @param string $topCategoryIndicatorKey   Defaults to 'top_category' row >= xt:C 4.1.0
     * @param string $topCategoryIndicatorValue Defaults to '1' as enabled
     * @return array|null Top categories array
     */
    public static function buildTopCategoriesData($categoriesTree, $topCategoryIndicatorKey = 'top_category', $topCategoryIndicatorValue = '1')
    {
        if (($categories = self::convertNestedToFlatArray($categoriesTree, 'sub')) === null || empty($categories))
            return null;

        foreach ($categories as $k => $i) {
            if (!isset($i[$topCategoryIndicatorKey]) ||
                (string)$i[$topCategoryIndicatorKey] !== (string)$topCategoryIndicatorValue
            ) {

                unset($categories[$k]);
            }
        }

        return !empty($categories) ? $categories : null;
    }

    /**
     * Converts an associative multi level array to an flat indexed array
     *
     * @param array  $array      Nested array
     * @param string $subItemKey Nested array key, where the tree is located
     * @param bool   $unsetSubs  Remove nested arrays from flat array items
     * @return array Flat array or null
     */
    public static function convertNestedToFlatArray($array, $subItemKey = 'sub', $unsetSubs = true)
    {
        $data = [];

        if (is_array($array) && !empty($array)) {
            foreach ($array as $level) {
                $sub = isset($level[$subItemKey]) ? $level[$subItemKey] : [];
                if ($unsetSubs === true) {
                    unset($level[$subItemKey]);
                }
                $data[] = $level;
                if (!empty($sub)) {
                    $data = array_merge($data, self::convertNestedToFlatArray($sub, $subItemKey, $unsetSubs));
                }
            }
        }

        return $data;
    }

    /**
     * Build HTML list recursive for associative array with sub arrays
     *
     * @param array  $data       The associative array with sub arrays
     * @param string $subItemKey Key name of sub arrays
     * @param int    $count      Starting point of counter
     * @param bool   $wrap       Wrap the output with ul or not
     * @return null|string
     */
    public static function buildHtmlList($data, $subItemKey = 'sub', $count = 0, $wrap = false)
    {
        if (!is_array($data) || empty($data)) {
            return null;
        }

        $output = '';
        $count++;
        $i = 0;
        $c = count($data);

        $output .= ($wrap === true) ? '<ul class="c' . $count . '">' . PHP_EOL : null;

        foreach ($data as $k => $level) {
            $i++;

            $active = ($level['active'] == 1) ? ' active' : null;
            $current = ($level['current'] == 1) ? ' current' : null;
            $sub = (isset($level['sub']) && !empty($level['sub'])) ? ' has-subs' : null;
            $first = ($i == 1) ? ' first' : null;
            $last = ($i == $c) ? ' last' : null;

            $output .= '<li id="cid-' . $level['categories_id'] . '" class="cat-level-' . $level['level'] .
                       ' list-level-' . $count .
                       ' list-item-' . $i .
                       $active .
                       $current .
                       $sub .
                       $first .
                       $last .
                       '">' .
                       PHP_EOL;
            $output .= '<a href="' . $level['categories_link'] . '" class="no-ripple">' .
                       $level['categories_name'] .
                       '</a>' .
                       PHP_EOL;

            if (isset($level[$subItemKey])) {
                $output .= (($children = self::buildHtmlList($level[$subItemKey], $subItemKey, $count, true)) !== null) ? $children : null;
            }

            $output .= '</li>' . PHP_EOL;
        }

        $output .= ($wrap === true) ? '</ul>' . PHP_EOL : null;

        return $output;
    }

    /**
     * Get config depth of the category representation
     *
     * @param int $default
     * @return int
     */
    public static function getCategoryDepth($default = 0)
    {
        return (defined('CONFIG_EW_EVELATIONS_PLUGIN_CAT_MAXLEVEL') && (int)CONFIG_EW_EVELATIONS_PLUGIN_CAT_MAXLEVEL > $default) ? (int)CONFIG_EW_EVELATIONS_PLUGIN_CAT_MAXLEVEL : $default;
    }

    /**
     * Get category data by id
     *
     * @param mixed $categoriesId
     * @return array|null
     */
    public static function getCategoryDataById($categoriesId)
    {
        global $category;

        if (empty($categoriesId) || (int)$categoriesId === 0)
            return null;

        $data = $category->buildData(array('categories_id' => $categoriesId));

        return (!empty($data) && is_array($data)) ? $data : null;
    }

    /**
     * HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries
     *
     * @return array Paths to js files
     */
    public static function getIE8js()
    {
        return array(
            self::getPluginRootURL() . 'assets/components/html5shiv/dist/html5shiv.js',
            self::getPluginRootURL() . 'assets/components/Respond/dest/respond.min.js',
        );
    }

    /**
     * Returns full HTTP URL to plugin folder
     *
     * @param bool $withBaseUrl Set to true for complete url (http://example.com), Set to false for absolute URI
     * @return string URL
     */
    public static function getPluginRootURL($withBaseUrl = true)
    {
        $baseUrl = ($withBaseUrl === true) ? _SYSTEM_BASE_URL . _SRV_WEB : null;

        return $baseUrl . _SRV_WEB_PLUGINS . self::PLUGIN_NAME . '/';
    }

    /**
     * Check if the minified cache file must be refreshed
     *
     * @param array  $resources
     * @param string $miniFile
     * @param bool   $debug
     * @return bool true|false
     */
    public static function refreshMinify($resources, $miniFile, $debug = false)
    {
        if (!self::isDebugMode() || (empty($resources) && empty($miniFile)))
            return false;

        if (self::$forceRefreshMinifyCache === true)
            return true;

        foreach ($resources as $res) {
            if (@filemtime(_SRV_WEBROOT . 'cache' . DIRECTORY_SEPARATOR . $miniFile) < @filemtime(_SRV_WEBROOT . $res['source'])) {
                if ($debug) {
                    echo $res['source'] . ' has changes...<br />';
                    echo $miniFile . ' is refreshed...<br /><br />';
                }

                return true;
            }
        }

        return false;
    }

    /**
     * Checks debug mode config
     */
    public static function isDebugMode()
    {
        if (!defined('CONFIG_EW_EVELATIONS_PLUGIN_DEBUG_MODE') || empty(CONFIG_EW_EVELATIONS_PLUGIN_DEBUG_MODE))
            return false;

        if (trim(CONFIG_EW_EVELATIONS_PLUGIN_DEBUG_MODE) == '*')
            return true;

        $ipArray = array();
        foreach (explode(',', CONFIG_EW_EVELATIONS_PLUGIN_DEBUG_MODE) as $ip) {
            $ipArray[] = (($ip = @trim((string)$ip)) && @filter_var($ip, FILTER_VALIDATE_IP) !== false) ? $ip : null;
        }

        if (!in_array(self::getUserIP(), $ipArray))
            return false;

        return true;
    }

    /**
     * Returns the user IP address.
     *
     * @return string user IP address. Null is returned if the user IP address cannot be detected.
     */
    public static function getUserIP()
    {
        if (isset($_SERVER)) {
            if (isset($_SERVER["HTTP_X_FORWARDED_FOR"]))
                return $_SERVER["HTTP_X_FORWARDED_FOR"];

            if (isset($_SERVER["HTTP_CLIENT_IP"]))
                return $_SERVER["HTTP_CLIENT_IP"];

            return $_SERVER["REMOTE_ADDR"];
        }

        if (getenv('HTTP_X_FORWARDED_FOR'))
            return getenv('HTTP_X_FORWARDED_FOR');

        if (getenv('HTTP_CLIENT_IP'))
            return getenv('HTTP_CLIENT_IP');

        if (getenv('REMOTE_ADDR'))
            return getenv('REMOTE_ADDR');

        return null;
    }

    /**
     * PLUGIN STATUS
     */
    public static function status()
    {
        global $xtPlugin;

        defined('_VALID_CALL') or die('Direct Access is not allowed.');

        if(defined('MAIN_SLIM')) return false;

        if (strpos(_STORE_TEMPLATE, self::TEMPLATE_PREFIX) === false)
            return false;

        if (version_compare(_SYSTEM_VERSION, '6.4.0', '<')) {
            self::setPluginErrorMessage('xt 6.4.0 minimum required for "' . self::PLUGIN_NAME . '".');

            return false;
        }

        if (!isset($xtPlugin->active_modules[self::PLUGIN_NAME])) {
            self::setPluginErrorMessage('Please activate the plugin "' . self::PLUGIN_NAME . '".');

            return false;
        }

        if (!self::check_conf('CONFIG_' . strtoupper(self::PLUGIN_NAME) . '_STATUS')) {
            self::setPluginErrorMessage('Please activate the plugin "' . self::PLUGIN_NAME . '".');

            return false;
        }

        return true;
    }

    /**
     * GET CONFIGURATION SETTING
     *
     * @param    string $key Configuration Key / CONSTANT
     * @return    bool    Returns config value
     */
    public static function check_conf($key)
    {
        $erg = false;

        if (!is_string($key) || !defined($key))
            return $erg;

        $key = constant($key);

        switch (gettype($key)) {
            case 'boolean':
                $erg = $key;

                break;

            case 'integer':
                if ($key == 1)
                    $erg = true;

                break;

            case 'string':
                $key = strtolower(trim($key));
                if ($key == 'true')
                    $erg = true;
                if ($key == '1')
                    $erg = true;

                break;
        }

        return $erg;
    }

    /**
     * @return null
     */
    public static function getPluginErrorMessage()
    {
        return self::$pluginErrorMessage;
    }

    /**
     * @param null $pluginErrorMessage
     */
    public static function setPluginErrorMessage($pluginErrorMessage)
    {
        self::$pluginErrorMessage = $pluginErrorMessage;
    }

    /**
     * Manages the teaser display
     *
     * @return boolean
     */
    public static function show_teaser()
    {
        return self::is_index();
    }

    /**
     * HELPER TO FIND OUT IF CURRENT PAGE = INDEX
     */
    public static function is_index()
    {
        return (self::get_current_pagename() == 'index') ? true : false;
    }

    /**
     * HELPER TO GET CURRENT PAGE NAME
     */
    public static function get_current_pagename()
    {
        global $page;

        if (isset($page->page_name) && !empty($page->page_name))
            return $page->page_name;

        return (!empty($_GET) && isset($_GET['page']) && !empty($_GET['page'])) ? trim($_GET['page']) : false;
    }

    /**
     * Manages the usp display
     *
     * @return boolean
     */
    public static function show_usp()
    {
        return self::is_index();
    }

    /**
     * Manages the seotext display
     *
     * @return boolean
     */
    public static function show_seotext()
    {
        return self::is_index();
    }

    /**
     * Check if Frontend is currently shown
     *
     * @version 1.0.0 2014-06-27
     * @return bool true|false
     */
    public static function is_frontend()
    {
        global $xtPlugin, $script_name;

        if (!isset($script_name) || !isset($xtPlugin))
            return false;

        if (!is_string($script_name) || !is_object($xtPlugin))
            return false;

        if (empty($script_name) || empty($xtPlugin))
            return false;

        $script_filename = pathinfo($script_name, PATHINFO_BASENAME);

        if (empty($script_filename))
            return false;

        if ($script_filename == 'cronjob.php'
            || $script_filename == 'captcha.php'
            || $script_filename == 'login.php'
            || $script_filename == 'ejsadmin.php'
            || $script_filename == 'upload.php'
            || $script_filename == 'upload_process.php'
        ) {
            return false;
        }

        if ($script_filename != 'index.php')
            return false;

        if (!defined('USER_POSITION') || USER_POSITION != 'store')
            return false;

        if (isset($_GET['feed_id']))
            return false;

        return true;
    }

    /**
     * HELPER TO FIND OUT IF CURRENT PAGE = ew_search_stats
     */
    public static function is_plugin_page()
    {
        return (self::get_current_pagename() == self::PLUGIN_NAME) ? true : false;
    }

    /**
     * cleans html code
     *
     * @param string $content
     * @param bool   $remove_comments
     * @return string
     */
    public static function clean_html($content, $remove_comments = false)
    {
        if ($remove_comments)
            $content = self::remove_html_comments($content);

        $content = self::replace_tabs_newlines($content);

        return $content;
    }

    /**
     * strips html comments
     * !!! danger with inline JavaScript or CDATA !!!
     *
     * @param string $content
     * @return string
     */
    public static function remove_html_comments($content)
    {
        return preg_replace('/<!--(.|\s)*?-->/', '', $content);
    }

    /**
     * strips newline characters and tabs
     * !!! danger with inline JavaScript or CDATA !!!
     *
     * @param string $content
     * @return string
     */
    public static function replace_tabs_newlines($content)
    {
        return preg_replace('(\r|\n|\t)', '', $content);
    }

    /**
     * Returns full HTTP URL to currently active template folder
     *
     * @param bool $withBaseUrl Set to true for complete url (http://example.com), Set to false for absolute URI
     * @return string URL
     */
    public static function getTemplateRootURL($withBaseUrl = true)
    {
        $baseUrl = ($withBaseUrl === true) ? _SYSTEM_BASE_URL . _SRV_WEB : null;

        return $baseUrl . _SRV_WEB_TEMPLATES . _STORE_TEMPLATE . '/';
    }

    /**
     * Return if the request is sent via secure channel (https).
     *
     * @return boolean if the request is sent via secure channel (https)
     */
    public static function getIsSecureConnection()
    {
        return isset($_SERVER['HTTPS']) && (strcasecmp($_SERVER['HTTPS'], 'on') === 0 || $_SERVER['HTTPS'] == 1)
               || isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && strcasecmp($_SERVER['HTTP_X_FORWARDED_PROTO'], 'https') === 0;
    }

    /**
     * Returns the server ip.
     *
     * @return string server ip
     */
    public static function getServerIP()
    {
        return gethostbyname(self::getHostName());
    }

    /**
     * Returns the server host name.
     *
     * @return string server host name
     */
    public static function getHostName()
    {
        return isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : self::getServerName();
    }

    /**
     * Returns the server name.
     *
     * @return string server name
     */
    public static function getServerName()
    {
        return $_SERVER['SERVER_NAME'];
    }

    /**
     * Returns the server port number.
     *
     * @return integer server port number
     */
    public static function getServerPort()
    {
        return (int)$_SERVER['SERVER_PORT'];
    }

    /**
     * Returns the URL referrer, null if not present
     *
     * @return string URL referrer, null if not present
     */
    public static function getReferrer()
    {
        return isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : null;
    }

    /**
     * Returns the user agent, null if not present.
     *
     * @return string user agent, null if not present
     */
    public static function getUserAgent()
    {
        return isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : null;
    }

    /**
     * Returns the user host name, null if it cannot be determined.
     *
     * @return string user host name, null if cannot be determined
     */
    public static function getUserHost()
    {
        return isset($_SERVER['REMOTE_HOST']) ? $_SERVER['REMOTE_HOST'] : null;
    }

    /**
     * Name of this class
     *
     * @return string class name
     */
    public static function className()
    {
        return __CLASS__;
    }

    /**
     * Name of current active shop template
     *
     * @return string template name
     */
    public static function templateName()
    {
        return _STORE_TEMPLATE;
    }

    /**
     * Returns the largest decimal less than or equal to a number
     *
     * @version 1.0.0
     * @author  J. Albert
     * @param   {Number} value
     * @param int $precision
     * @return float {Number}
     */
    public static function floorPrecision($value, $precision = 0)
    {
        return floor($value * ($pow = pow(10, $precision))) / $pow;
    }

    /**
     * Adds new mysql table column if not exists and correct syntax
     *
     * @param string $table      The database tablename
     * @param string $column     The database table columnname
     * @param string $definition The mysql query
     * @return bool True or false on fail
     */
    public static function mysqlAddColumn($table, $column, $definition)
    {
        global $db;

        if (self::mysqlTableSchemaExists($table, $column))
            return false;

        if (($record = @$db->Execute("ALTER TABLE `$table` ADD `$column` $definition")) !== false && is_object($record)) {
            $record->Close();

            return true;
        }

        return false;
    }

    /**
     * Check if an table or its column(s) exists
     *
     * @param string $table  The database tablename
     * @param string $column The database table columnname (* by default, comma separated lists allowed)
     * @return bool True or false on fail
     */
    public static function mysqlTableSchemaExists($table, $column = '*')
    {
        $plugin = new \plugin();
        return $plugin->_FieldExists($table, $column);
    }

    /**
     * Adds new mysql table column if not exists and correct syntax
     *
     * @param string $table  The database tablename
     * @param string $column The database table columnname
     * @return bool True or false on fail
     */
    public static function mysqlDropColumn($table, $column)
    {
        global $db, $plugin;

        if (!self::mysqlTableSchemaExists($column, $table))
            return false;

        if (($record = @$db->Execute("ALTER TABLE `$table` DROP `$column`")) !== false && is_object($record)) {
            $record->Close();

            return true;
        }

        return false;
    }

    /**
     * formatInstallerLogMessage
     *
     * @param string $message
     * @param string $storage
     * @param string $type [success|error|warning|info] defaults to [success]
     */
    public static function formatInstallerLogMessage($message, &$storage, $type = 'success')
    {
        switch ($type) {
            case 'success':
                $message = "<span style=\"color: green; font-weight: bold;\">$message</span>";
                break;
            case 'error':
                $message = "<span style=\"color: red; font-weight: bold;\">$message</span>";
                break;
            case 'warning':
                $message = "<span style=\"color: orange; font-weight: bold;\">$message</span>";
                break;
            default:
                $message = "<span style=\"color: blue; font-weight: bold;\">$message</span>";
                break;
        }
        $storage = "<li class=\"plugin_script_add\">$message</li>$storage";
    }

    /**
     * Get all image types / image folders for this instance
     *
     * @param string $prefix Image folder prefix
     * @param bool   $removeNonAsciiChars Removes non ascii chars from folder name
     * @return array|null Image types array
     */
    public function getTemplateImageTypes($prefix = self::IMAGE_TYPE_PREFIX, $removeNonAsciiChars = true)
    {
        $config = $this->getConfig('image_type');

        if (empty($config) || !isset($config['type']) || empty($config['type']))
            return null;

        foreach ($config['type'] as $k => $i) {
            if (isset($i['dir']) && !empty($i['dir'])) {
                $dir = "{$prefix}{$i['dir']}";
                $config['type'][$k]['dir'] = $removeNonAsciiChars ? self::removeNonAsciiChars($dir) : $dir;
            } else {
                unset($config['type'][$k]);
            }
        }

        return $config['type'];
    }

    /**
     * Removes non ascii chars from input value
     *
     * @param $s string String to remove non ascii chars
     * @return mixed Returns string or input param, if not a string
     */
    public static function removeNonAsciiChars($s)
    {
        return (!empty($s) && is_string($s)) ? preg_replace('=[^\da-z]=is', '', $s) : $s;
    }

    /**
     * Loads configuration array from xml file
     *
     * @param string $key The config key (file name without extension)
     * @return array|Exception Configuration array or Exception if configuration file is not found
     * @throws Exception
     */
    public function getConfig($key)
    {
        if (!isset($this->_configurationArray[$key]) || empty($this->_configurationArray[$key])) {
            $filePath = EW_EVELATIONS_PLUGIN_CONFIG_DIR . DIRECTORY_SEPARATOR . (string)$key . '.xml';
            if (($fileContent = @file_get_contents($filePath)) === false) {
                throw new Exception("Could not load config file $filePath.");
            }

            return $this->_configurationArray[$key] = !empty($fileContent) ? self::xmlToArray($fileContent) : array();
        }

        return $this->_configurationArray[$key];
    }

    /**
     * Converts xml code to php array
     *
     * @param string $xmlString
     * @return mixed
     */
    public static function xmlToArray($xmlString)
    {
        return json_decode(json_encode(simplexml_load_string($xmlString)), true);
    }

    /**
     * @param array $block
     * @return bool
     */
    public static function addContentBlock(array $block)
    {
        global $db;

        $prefix = self::CONTENT_BLOCK_PREFIX;
        $tag = "{$prefix}{$block['tag']}";

        $exists = self::getRecordFromDB("SELECT `block_tag` FROM `" . TABLE_CONTENT_BLOCK . "` WHERE `block_tag` = '$tag'");

        if (!empty($exists))
            return false;

        $db->Execute("INSERT INTO `" . TABLE_CONTENT_BLOCK . "` (`block_id`, `block_tag`, `block_status`, `block_protected`) VALUES (NULL, '$tag', '".(int)$block['status']."', '".(int)$block['protected']."')");

        $block_id = $db->GetOne("SELECT `block_id` FROM `" . TABLE_CONTENT_BLOCK . "` WHERE `block_tag` = ?", [$tag]);

        //content den blöcken zuordnen
        if(defined('XT_WIZARD_STARTED') && XT_WIZARD_STARTED)
        {
            if(array_key_exists('content_to_block', $block) && is_countable($block['content_to_block']) && is_countable($block['content_to_block']['content_id']))
            {
                foreach($block['content_to_block']['content_id'] as $cid)
                {
                    if(!empty($cid))
                    {
                        $db->Execute("INSERT INTO `" . TABLE_CONTENT_TO_BLOCK . "` (`block_id`, `content_id`) VALUES (?, ?)", [$block_id, $cid] );
                    }
                }
            }
        }

        return !empty($block_id);
    }

    /**
     * Add an image type to database
     *
     * @param string $dir
     * @param string $class
     * @param string $width
     * @param string $height
     * @param string $processing
     * @return bool
     */
    public function addImageType($dir, $class, $width, $height, $processing = 'false')
    {
        global $db;

        $typeExists = self::getRecordFromDB("SELECT `folder` FROM `" . TABLE_IMAGE_TYPE . "` WHERE `folder` = '$dir'");

        if (!empty($typeExists))
            return false;

        $db->Execute(
            "
            INSERT INTO `" . TABLE_IMAGE_TYPE . "` (
                `id`, `folder`, `width`, `height`, `process`, `class`
            ) VALUES (
                NULL, '$dir', '$width', '$height', '$processing', '$class'
            )
        ");

        $typeExists = self::getRecordFromDB("SELECT `folder` FROM `" . TABLE_IMAGE_TYPE . "` WHERE `folder` = '$dir'");

        return !empty($typeExists);
    }

    /**
     * Get data from database with select query
     *
     * @example classname::getRecordFromDB("SELECT * FROM ".DB_PREFIX."_products WHERE products_master_model != ''
     *          LIMIT 999999")
     * @param string $query
     * @return array
     */
    public static function getRecordFromDB($query)
    {
        global $db;

        $data = array();

        if (($record = $db->Execute($query)) == 0)
            return $data;

        while (!$record->EOF) {
            $data[] = $record->fields;
            $record->MoveNext();
        }
        $record->Close();

        return $data;
    }

    /**
     * Remove an image type from database
     *
     * @param string $dir
     * @return bool
     */
    public static function removeImageType($dir)
    {
        global $db;

        if (empty($dir))
            return false;

        $db->Execute("DELETE FROM `" . TABLE_IMAGE_TYPE . "` WHERE `folder` = '$dir'");

        return true;
    }

    /**
     * Get all contents assigned to requested content block
     *
     * @param string  $blockTag The content block name
     * @param boolean $nested   Fetch nested contents defaults true
     * @return null|array Assigned contents
     */
    public function getContentsByBlock($blockTag, $nested = true)
    {
        global $_content;

        $cacheKey = ($nested === true) ? "{$blockTag}_nested" : $blockTag;

        if (($contentBlock = $this->getContentBlockStorage($cacheKey)) === null) {
            if (($blockID = self::getContentBlockIdByTag($blockTag)) !== null) {
                $contentBlock = $this->setContentBlockStorage($_content->getContentBox($blockID, $nested), $cacheKey);
            }
        }

        return $contentBlock;
    }

    /**
     * Get Slider ID since xt:C 5
     *
     * @param int $overwrite
     * @param int $fallback
     * @return int
     */
    public static function getSliderId($overwrite = 0, $fallback = 1)
    {
        $id = (int)$fallback;
        $overwrite = (int)$overwrite;
        $config = defined('CONFIG_EW_EVELATIONS_PLUGIN_TEASER_SLIDER_ID') ? (int)CONFIG_EW_EVELATIONS_PLUGIN_TEASER_SLIDER_ID : 0;

        if ($config !== 0) {
            $id = $config;
        }

        if ($overwrite !== 0) {
            $id = $overwrite;
        }

        return $id;
    }

    /**
     * Get content block cache
     *
     * @param string|int $storage
     * @return mixed
     */
    public function getContentBlockStorage($storage)
    {
        return $this->_contentsByBlock[$storage] ?? null;
    }

    /**
     * GET GLOBAL CACHE SETTING
     */
    public static function isSystemCacheEnabled()
    {
        return self::check_conf('SMARTY_USE_CACHE');
    }

    /**
     * Get the last modification timestamp of a config key
     *
     * @param string $key Constant Key / Configuration Key as string
     * @return null|string Null or timestamp
     */
    public static function get_config_update_time($key)
    {
        if (!is_string($key) || !defined($key))
            return null;

        if (($pluginId = self::get_config_plugin_id($key)) === null)
            return null;

        $time = _getSingleValue(
            array(
                'value'     => 'last_modified',
                'table'     => TABLE_ADMIN_ACL_TASK,
                'key'       => 'active_id',
                'key_val'   => $pluginId,
                'key_where' => " AND `class` = 'plugin_installed' AND `action` = 'save' ",
            ));

        return !empty($time) ? $time : null;
    }

    /**
     * Get the plugin id by config key
     *
     * @param string $key Constant Key / Configuration Key as string
     * @return null|int Null or id
     */
    public static function get_config_plugin_id($key)
    {
        if (!is_string($key) || !defined($key))
            return null;

        $id = (int)_getSingleValue(
            array(
                'value'   => 'plugin_id',
                'table'   => TABLE_PLUGIN_CONFIGURATION,
                'key'     => 'config_key',
                'key_val' => $key
            ));

        return ($id !== 0) ? $id : null;
    }

    /**
     * Get content block id
     *
     * @param string $blockTag The content block name
     * @return integer|null The id or null if not existend
     */
    public function getContentBlockIdByTag($blockTag)
    {
        global $_content;

        if (in_array($blockTag, $_content->BLOCKS)) {
            $id = array_keys($_content->BLOCKS, $blockTag);
            if (isset($id[0]) && !empty($id[0])) {
                return (int)$id[0];
            }
        }

        return null;
    }

    /**
     * Set content block cache
     *
     * @param mixed  $value
     * @param string $storage
     * @return mixed
     */
    public function setContentBlockStorage($value, $storage)
    {
        return $this->_contentsByBlock[$storage] = $value;
    }

    /**
     * Check if content block cache exists
     *
     * @param string|int $storage
     * @return bool
     */
    public function isContentBlockInStorage($storage)
    {
        $value = $this->getCategoriesStorage($storage);

        return !empty($value);
    }

    /**
     * Get categories cache
     *
     * @param string|int $storage
     * @return mixed
     */
    public function getCategoriesStorage($storage)
    {
        return $this->_categories_storage[$storage];
    }

    /**
     * Set categories cache
     *
     * @param mixed  $value
     * @param string $storage
     * @return mixed
     */
    public function setCategoriesStorage($value, $storage)
    {
        return $this->_categories_storage[$storage] = $value;
    }

    /**
     * Get categories once, or without cache
     *
     * @param bool $recursive            Enable, disable recursion
     * @param int  $max_navigation_level Max level for recursion
     * @param bool $cache                Disable or enable caching
     * @param int  $entryLevel           Show sub level (1 for first sub level)
     * @param int  $entryId              Show sub level category id
     * @param bool $forceOldMethod
     * @return array|bool
     */
    public function get_categories_array($recursive = false, $max_navigation_level = 1, $cache = true, $entryLevel = 0, $entryId = 0, $forceOldMethod = false)
    {
        if ((int)$max_navigation_level < 1) {
            $max_navigation_level = 1;
        }

        if ((int)$entryLevel === 0) {
            $entryLevel = 0;
        }

        if (($entryId = (int)$entryId) === 0) {
            $entryId = ($entryLevel !== 0) ? self::getParentCategoryIdByLevel($entryLevel) : 0;
        }
        $entryLevel = ((int)$entryId !== 0) ? $entryLevel : 0;

        if (!isset($this->_categories_array[$entryId][$max_navigation_level]) || !$cache) {
            $data = $this->build_categories_array($max_navigation_level, $entryId, $entryLevel, $cache, $recursive, $forceOldMethod);

            if (!is_array($data) || count($data) === 0)
                return $this->_categories_array[$entryId][$max_navigation_level] = false;

            return $this->_categories_array[$entryId][$max_navigation_level] = $data;
        }

        return $this->_categories_array[$entryId][$max_navigation_level];
    }

    /**
     * Get parent category by level
     *
     * @param int $level The category level of current category (1 Main Level Entry, fetches data from level 2)
     * @return int category id
     */
    public static function getParentCategoryIdByLevel($level = 1)
    {
        global $category;

        $level = ((int)$level < 1) ? 1 : (int)$level;

        return isset($category->level[$level]) ? $category->level[$level] : 0;
    }

    /**
     * @param int  $max_level
     * @param int  $cid
     * @param int  $level
     * @param bool $cache
     * @param bool $recursive
     * @param bool $forceOldMethod
     * @return array
     */
    protected function build_categories_array($max_level = 5, $cid = 0, $level = 0, $cache = true, $recursive = false, $forceOldMethod = false)
    {
        return $this->buildCategoriesArrayNew($max_level, $cid, $level, $cache, $recursive);
    }

    /**
     * @param int  $max_level
     * @param int  $cid
     * @param int  $__level obsolete
     * @param bool $__cache obsolete
     * @param bool $recursive
     * @return array
     */
    protected function buildCategoriesArrayNew($max_level = 5, $cid = 0, $__level = 0, $__cache = true, $recursive = false)
    {
        global $category;

        $data = $category->getCategoryBox($cid, $recursive, $max_level);

        if (!is_array($data) || empty($data)) {
            return [];
        }

        return $this->markCategoryData($data);
    }

    /**
     * @param $data
     * @return array
     */
    protected function markCategoryData($data)
    {
        global $category, $current_category_id;

        if (!is_array($data) || empty($data)) {
            return $data;
        }

        foreach ($data as $k => $i) {
            $data[$k]['active'] = '0';
            $data[$k]['current'] = '0';
            if ((int)$i['categories_id'] === (int)$current_category_id) {
                $data[$k]['current'] = '1';
            }
            if (in_array($i['categories_id'], $category->level)) {
                $data[$k]['active'] = '1';
            }
            if (isset($i['sub'])) {
                $data[$k]['sub'] = $this->markCategoryData($i['sub']);
            }
        }

        return $data;
    }

    /**
     * Check if cache exists
     *
     * @param string|int $storage
     * @return bool
     */
    public function isCategoryInStorage($storage)
    {
        $value = $this->getCategoriesStorage($storage);

        return !empty($value);
    }

    /**
     * REGISTER CLIENT SCRIPTS
     */
    public function registerAssets()
    {
        global $xtMinify;

        define('EW_EVELATIONS_TEMPLATE_RELATIVE_DIR', _SRV_WEB_TEMPLATES . _STORE_TEMPLATE);
        define('EW_EVELATIONS_TEMPLATE_ROOT_DIR', _SRV_WEBROOT . EW_EVELATIONS_TEMPLATE_RELATIVE_DIR);

        define('EW_EVELATIONS_TEMPLATE_RELATIVE_DIR_SYSTEM', _SRV_WEB_TEMPLATES . _SYSTEM_TEMPLATE);
        define('EW_EVELATIONS_TEMPLATE_ROOT_DIR_SYSTEM', _SRV_WEBROOT . EW_EVELATIONS_TEMPLATE_RELATIVE_DIR_SYSTEM);

        define('EW_EVELATIONS_PLUGIN_RELATIVE_DIR',  _SRV_WEB_PLUGINS . self::PLUGIN_NAME);

        $compiledStylesheet = self::get_unique_shop_string('style_', '_compiled_' . _STORE_TEMPLATE . '.css');

        $files = array(
            '../../cache/' . $compiledStylesheet,
            'assets/' . self::PLUGIN_NAME . '.js',
            '../../' . EW_EVELATIONS_TEMPLATE_RELATIVE_DIR . '/css/' . self::get_unique_shop_string('stylesheet_', '.css'),
            '../../' . EW_EVELATIONS_TEMPLATE_RELATIVE_DIR . '/javascript/' . self::get_unique_shop_string('script_', '.js'),
        );

        if (($assetConfig = $this->getConfig('assets')) !== null) {
            if (isset($assetConfig['file']) && !empty($assetConfig['file'])) {
                $files = array_merge($assetConfig['file'], $files);
            }
        }

        if (($lessConfig = $this->getConfig('lessCss')) !== null) {
            if ($lessConfig['config']['parseLess'] == 'true') {

                if (!is_dir(($compileDir = _SRV_WEBROOT . 'cache'))) {
                    throw new Exception("`$compileDir` does not exist.");
                }

                $compileToFile = $compileDir . DIRECTORY_SEPARATOR . $compiledStylesheet;

                if (self::isDebugMode() || !file_exists($compileToFile)) {

                    if (!is_dir(($compileDir = _SRV_WEBROOT . 'cache')) || !is_writeable($compileDir)) {
                        throw new Exception("Could not write `$compileToFile`. Folder is not writeable.");
                    }

                    $less = new LessConverter();
                    $less->sourceMap = self::getEnableLessSourceMap();
                    $less->compress = ($lessConfig['config']['compressCss'] == 'true') ? true : false;
                    $less->useCache = ($lessConfig['config']['useLessCache'] == 'true') ? true : false;
                    $less->memoryLimit = (($ml = (int)$lessConfig['config']['memoryLimitInMegabyte']) !== 0) ? "{$ml}M" : $less->memoryLimit;
                    $less->maxExecutionTime = (($met = (int)$lessConfig['config']['maxExecutionTimeInSeconds']) !== 0) ? $met : $less->maxExecutionTime;
                    $less->cacheDir = $compileDir;
                    $parseString  = ($lessConfig['config']['parsePluginSettings'] == 'true') ? self::buildLessColorFromConfig('@brand-primary', 'CONFIG_EW_EVELATIONS_PLUGIN_PRIMARY_COLOR') : null;
                    $parseString .= ($lessConfig['config']['parsePluginSettings'] == 'true') ? self::buildLessColorFromConfig('@brand-secondary', 'CONFIG_EW_EVELATIONS_PLUGIN_SECONDARY_COLOR') : null;
                    $less->parseString = $parseString;
                    $forceUpdate_primary = ($lessConfig['config']['parsePluginSettings'] == 'true') ? self::checkLessStringConfigUpdateTime($compileToFile, 'CONFIG_EW_EVELATIONS_PLUGIN_PRIMARY_COLOR') : false;
                    $forceUpdate_secondary = ($lessConfig['config']['parsePluginSettings'] == 'true') ? self::checkLessStringConfigUpdateTime($compileToFile, 'CONFIG_EW_EVELATIONS_PLUGIN_SECONDARY_COLOR') : false;
                    $less->forceUpdate = $forceUpdate_primary || $forceUpdate_secondary;
                    self::$forceRefreshMinifyCache = ($lessConfig['config']['parsePluginSettings'] == 'true') ? $less->forceUpdate : self::$forceRefreshMinifyCache;

                    $lessFiles = array();
                    foreach ($lessConfig['files']['path'] as $lessPath) {
                        $param_input = self::resolveStringToPath($lessPath['absoluteServerPath']);
                        $param_webFolder = self::resolveStringToPath($lessPath['relativePathFromOutputFile']);
                        $lessFiles[] = array(
                            'input'     => $param_input,
                            'webFolder' => $param_webFolder,
                        );
                    }

                    $less_init_files = array_merge(
                        $lessFiles,
                        [
                            [
                                'input'     => EW_EVELATIONS_TEMPLATE_ROOT_DIR . '/css/' . self::get_unique_shop_string('template_', '.less'),
                                'webFolder' => '../' . EW_EVELATIONS_TEMPLATE_RELATIVE_DIR . '/css/',
                            ]
                        ]
                    );

                    $less->init($less_init_files, $compileToFile);
                }
            }
        }

        $htmlOutput = null;

        if (($assetsNoMinify = $this->getConfig('assetsNoMinify')) !== null &&
            !empty($assetsNoMinify['file'])) {

            foreach ($assetsNoMinify['file'] as $k => $f) {
                $filePath = EW_EVELATIONS_PLUGIN_ROOT_DIR . DIRECTORY_SEPARATOR . $f;
                if (@is_file($filePath)) {
                    switch (pathinfo($f, PATHINFO_EXTENSION)) {
                        case 'css':
                            $htmlOutput .= '<link href="' . self::getPluginRootURL() . $f . '" media="all" rel="stylesheet" type="text/css" />' . PHP_EOL;
                            break;
                        case 'js':
                            if (($fileContent = @file_get_contents($filePath)) !== false) {
                                $fileContent = trim($fileContent);
                                $fileName = basename($filePath);
                                if (!empty($fileContent)) {
                                    $htmlOutput .= "
                                    <!-- FILE::{$fileName} -->
                                    <script type=\"text/javascript\">
                                        /* <![CDATA[ */
                                        {$fileContent}
                                        /* ]]> */
                                    </script>
                                    ";
                                }
                            }
                            break;
                    }
                }
            }
        }

        foreach ($files as $k => $f) {
            if (@is_file(EW_EVELATIONS_PLUGIN_ROOT_DIR . DIRECTORY_SEPARATOR . $f)) {
                $ext = pathinfo($f, PATHINFO_EXTENSION);
                if (isset($xtMinify) && is_object($xtMinify)) {
                    switch ($ext) {
                        case 'css':
                            $xtMinify->add_resource(EW_EVELATIONS_PLUGIN_RELATIVE_DIR . '/' . $f, $k);
                            break;
                        case 'js':
                            $xtMinify->add_resource(EW_EVELATIONS_PLUGIN_RELATIVE_DIR . '/' . $f, $k*2+5, 'footer');
                            break;
                    }
                } else {
                    switch ($ext) {
                        case 'css':
                            $htmlOutput .= '<link href="' . self::getPluginRootURL() . $f . '" media="all" rel="stylesheet" type="text/css" />' . PHP_EOL;
                            break;
                        case 'js':
                            $htmlOutput .= '<script type="text/javascript" src="' . self::getPluginRootURL() . $f . '"></script>' . PHP_EOL;
                            break;
                    }
                }
            }
        }

        echo $htmlOutput;

        return true;
    }

    /**
     * @param $path
     * @return string
     */
    public static function resolveStringToPath($path)
    {
        $return_path = @str_replace(
            array(
                'EW_EVELATIONS_PLUGIN_ROOT_DIR',
                'EW_EVELATIONS_PLUGIN_RELATIVE_DIR',
                'EW_EVELATIONS_TEMPLATE_ROOT_DIR',
                'EW_EVELATIONS_TEMPLATE_RELATIVE_DIR',
            ),
            array(
                EW_EVELATIONS_PLUGIN_ROOT_DIR,
                EW_EVELATIONS_PLUGIN_RELATIVE_DIR,
                EW_EVELATIONS_TEMPLATE_ROOT_DIR,
                EW_EVELATIONS_TEMPLATE_RELATIVE_DIR,
            ),
            $path
        );

        if(strpos($return_path,'/') === 0  // absolute path
            && !file_exists($return_path))
        {
            $return_path = @str_replace(
                array(
                    'EW_EVELATIONS_PLUGIN_ROOT_DIR',
                    'EW_EVELATIONS_PLUGIN_RELATIVE_DIR',
                    'EW_EVELATIONS_TEMPLATE_ROOT_DIR',
                    'EW_EVELATIONS_TEMPLATE_RELATIVE_DIR',
                ),
                array(
                    EW_EVELATIONS_PLUGIN_ROOT_DIR,
                    EW_EVELATIONS_PLUGIN_RELATIVE_DIR,
                    EW_EVELATIONS_TEMPLATE_ROOT_DIR_SYSTEM,
                    EW_EVELATIONS_TEMPLATE_RELATIVE_DIR_SYSTEM,
                ),
                $path
            );
        }

        return $return_path;
    }

    /**
     * Source map activation
     *
     * @see https://hacks.mozilla.org/2014/02/live-editing-sass-and-less-in-the-firefox-developer-tools/
     * @return bool
     */
    public static function getEnableLessSourceMap()
    {
        return self::check_conf('CONFIG_EW_EVELATIONS_PLUGIN_LESS_SOURCE_MAP');
    }

    /**
     * Build Less hex-color Code from plugin config
     *
     * @param string $lessVariable Less variable name for example @brand-primary
     * @param string $lessValue    Configuration key (constant)
     * @return null|string Null or String when new code is available
     */
    public static function buildLessColorFromConfig($lessVariable, $lessValue)
    {
        if (!is_string($lessValue))
            return null;

        if (@defined($lessValue)) {
            $lessValue = trim(constant($lessValue));
        }

        $lessCode = null;
        if (!empty($lessValue) && (self::isHexColorString($lessValue))) {
            $lessCode .= "$lessVariable: $lessValue;";
        }

        return !empty($lessCode) ? $lessCode : null;
    }

    /**
     * Checks timestamp of destination file and config to force an recompile
     *
     * @param string $checkFile Check timestamp of destination file for update
     * @param string $checkKey  Configuration key (constant)
     * @return bool
     */
    public static function checkLessStringConfigUpdateTime($checkFile, $checkKey)
    {
        if (!is_string($checkKey) || !defined($checkKey))
            return false;

        if (!@file_exists($checkFile))
            return false;

        return @filemtime($checkFile) < @strtotime(self::get_config_update_time($checkKey));
    }
}
