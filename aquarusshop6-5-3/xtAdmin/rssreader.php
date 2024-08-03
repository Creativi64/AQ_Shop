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

class rssreader
{

    private $timeout = 3600;
    private $newsfeed_filename = 'xt_newsfeed.xml';
    private $toppluginsfeed_filename = 'xt_toppluginsfeed.xml';
    private $newpluginfeed_filename = 'xt_newpluginfeed.xml';
    private $cached = true;
    private $newsfeed_url = 'https://www.xt-commerce.com/blog/feed';
    private $topplugins_url = 'https://addons.xt-commerce.com/index.php?page=rss_sale';
    private $newplugins_url = 'https://addons.xt-commerce.com/index.php?page=rss_new';

    /**
     * 
     * constructor
     * 
     * @param bool $cached
     * @param int $timeout
     */
    public function __construct($cached=true, $timeout=3600, $urllinks = array())
    {
        $this->cached = $cached;
        $this->timeout = $timeout;

        if (!empty($urllinks) && is_array($urllinks)) {
            $this->newsfeed_url = $urllinks['newsfeed_url'];
            $this->topplugins_url = $urllinks['topplugins_url'];
            $this->newplugins_url = $urllinks['newplugins_url'];
        }

        $params = 'version=' . _SYSTEM_VERSION;
        if (function_exists('ioncube_license_properties')) {
            $lic_parms = ioncube_license_properties();
            $params .= '&lic=' . $lic_parms['key']['value'] . '&domain=' . urlencode(_SYSTEM_BASE_HTTP);
        }
       // $this->newsfeed_url .= ((strpos($this->newsfeed_url, '?')) ? '&' : '?') . $params;
        $this->topplugins_url .= ((strpos($this->topplugins_url, '?')) ? '&' : '?') . $params;
        $this->newplugins_url .= ((strpos($this->newplugins_url, '?')) ? '&' : '?') . $params;

        $this->newsfeed_filename = _SRV_WEBROOT . 'cache/' . $this->newsfeed_filename;
        $this->toppluginsfeed_filename = _SRV_WEBROOT . 'cache/' . $this->toppluginsfeed_filename;
        $this->newpluginfeed_filename = _SRV_WEBROOT . 'cache/' . $this->newpluginfeed_filename;
        
        // Sending request to API server
        require_once _SRV_WEBROOT._SRV_WEB_FRAMEWORK.'classes/class.CurlRequest.php';
        //$license_key = $lic_parms['key']['value'];
    }

    /**
     *
     * get News RSSFeed
     * @param string $urlFeed feedlink
     * @param int $num number of feed
     * @return array of feed
     */
    public function getNewsFeed($num = 10)
    {
        $content = $this->getFeedContent($this->newsfeed_url, $this->newsfeed_filename);

        $x = XML_unserialize($content); // Parse XML using phpxml Class
        $rss_temp = array();
        if(is_array($x['rss']['channel']['item']))
        {
            foreach ($x['rss']['channel']['item'] as $entry)
            { // Fill the arrays with the rss feed
                $rss_temp[] = array(
                    'title' => $this->iTrunc($entry['title'], 100),
                    'link' => $entry['link'],
                    'description' => $this->iTrunc($entry['description'], 500),
                    'category' => $entry['category'],
                    'guid' => $entry['guid'],
                    'pubDate' => date('D, d M Y H:i', strtotime($entry['pubDate'])),
                );
            }
        }

        return $rss_temp;
    }

    /**
     *
     * get New Plugins RSSFeed
     * @param string $urlFeed feedlink
     * @param int $num number of feed
     * @return array of feed
     */
    public function getNewPluginsFeed($num = 5)
    {
        $content = $this->getFeedContent($this->newplugins_url, $this->newpluginfeed_filename);

        return $this->getFeed($content, $num);
    }

    /**
     *
     * get Top Plugins RSSFeed
     * @param string $urlFeed feedlink
     * @param int $num number of feed
     * @return array of feed
     */
    public function getTopPluginsFeed($num = 5)
    {
        $content = $this->getFeedContent($this->topplugins_url, $this->toppluginsfeed_filename);

        return $this->getFeed($content, $num);
    }

    public function getFeed($content, $num = 5)
    {
        $x = XML_unserialize($content); // Parse XML using phpxml Class
        $rss_temp = array();
        if (!empty($x)) {
            foreach ($x['rss']['channel']['item'] as $entry) { // Fill the arrays with the rss feed
                $cat = isset($entry['category']) ? $entry['category'] : '';
                $guid = isset($entry['guid']) ? $entry['guid'] : '';
                $pubDate = isset($entry['pubDate']) ? $entry['pubDate'] : '';
                $rss_temp[] = array('title' => $this->iTrunc($entry['title'], 100), 'link' => $entry['link'], 'description' => '', 'category' => $cat, 'guid' => $guid, 'pubDate' => $pubDate);
            }
        }

        return array_slice($rss_temp, 0, $num);
    }

    /**
     * 
     * get XML from File, if cached or from URL direct
     * @param string $urlFeed
     * @param string $filename
     * @return string $content
     * 
     */
    function getFeedContent($urlFeed, $filename)
    {
	
        if (file_exists($filename) AND (time() - filemtime($filename) < $this->timeout)) {
            $feedFile = fopen($filename, "r");
            $content = '';
            while (!feof($feedFile)) {
                $content .= fgets($feedFile, 4096);
            }
            fclose($feedFile);
        } else {

       		$ch = curl_init($urlFeed);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			$content = curl_exec($ch);
			curl_close($ch);
    
            $this->saveFeed($content, $filename);
        }
        return $content;
    }

    /**
     * 
     * save the Feed String into File
     * @param string $feed
     * @param string $filename
     * 
     */
    function saveFeed($feed, $filename)
    {
        $feedFile = fopen($filename, "w+");
        if ($feedFile) {
            fputs($feedFile, $feed);
            fclose($feedFile);
        } else {
            echo "<br /><b>Error creating feed file, please check write permissions.</b><br />";
        }
    }

    /**
     * 
     * get Substring of String with Length
     * @param string $string
     * @param int $length
     * @return string 
     */
    function iTrunc($string, $length)
    {
        if (strlen($string) <= $length) {
            return $string;
        }

        $pos = strrpos($string, ".");
        if ($pos >= $length - 4) {
            $string = substr($string, 0, $length - 4);
            $pos = strrpos($string, ".");
        }
        if ($pos >= $length * 0.4) {
            return substr($string, 0, $pos + 1) . " ...";
        }

        $pos = strrpos($string, " ");
        if ($pos >= $length - 4) {
            $string = substr($string, 0, $length - 4);
            $pos = strrpos($string, " ");
        }
        if ($pos >= $length * 0.4) {
            return substr($string, 0, $pos) . " ...";
        }

        return substr($string, 0, $length - 4) . " ...";
    }

}

?>