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


class xt_minify
{

    var $resources;
    var $debugger = true;
    var $debug_flag = true;
    var $css_cache_time = _SYSTEM_CSS_CACHE_TIME;
    var $js_cache_time = _SYSTEM_JS_CACHE_TIME;
    var $css_minify_option = _SYSTEM_CSS_MINIFY_OPTION; // minifymerge, merge, single
    var $js_minify_option = _SYSTEM_JS_MINIFY_OPTION; // minifymerge, merge, single
    var $css_file='style';
    var $js_file='javascript';

    /**
     *
     * add js/css resource to stack
     *
     * @param $file string including directory
     * @param $sort_order int sort order
     */
    public function add_resource($file,$sort_order,$location='header', $params = array())
    {
        global $xtPlugin;

        ($plugin_code = $xtPlugin->PluginCode('class.xt_minify.php:add_resource_top')) ? eval($plugin_code) : false;
        if(isset($plugin_return_value))
            return $plugin_return_value;

        $type='js';
        if (substr($file, -4) == '.css')
        {
            $type = 'css';
        }
        $this->resources[$location][$type][]=array('sort_order'=>$sort_order,'source'=>$file,'type'=>$type, 'params' => $params);

        ($plugin_code = $xtPlugin->PluginCode('class.xt_minify.php:add_resource_bottom')) ? eval($plugin_code) : false;
    }

    /**
     * read file content
     *
     * @param $file
     * @return string
     */
    private function getFileContent($file) {

        $handle = fopen($file, "r");
        if (filesize($file)<=0) return '';
        $contents = fread($handle, filesize($file));
	fclose($handle);
        return $contents;
    }

    private function save_cache_file($feed, $filename)
    {
        $feedFile = fopen('cache/'.$filename, "w+");
        if ($feedFile) {
            fputs($feedFile, $feed);
            fclose($feedFile);

        } else {
            echo "<br /><b>Error creating css cache file, please check write permissions for cache.</b><br />";
        }
    }

    /**
     * generate css/js html tags
     *
     */
    public function serveFile($location='header') {

        $this->serveCSS($location);
        $this->serveJS($location);

    }

	private function checkCacheFile($file,$file_type='css') {
		
		$expires = $this->{$file_type.'_cache_time'};
		if (!isset($expires)) {echo "Not set"; $expires = 3600;}
		
        if (file_exists($file) AND (time() - filemtime($file) < (int)$expires)) {
            return true;
        }
        return false;

    }
	
	function sortResources (&$array, $key)
    {
	    if (!is_array($array)) {
            $array = array();
            return;
        }
	    $sorter=array();
	    $ret=array();
	    reset($array);
	    foreach ($array as $ii => $va) {
	        $sorter[$ii]=$va[$key];
	    }
	    asort($sorter);
	    foreach ($sorter as $ii => $va) {
	        $ret[$ii]=$array[$ii];
	    }
	    $array=$ret;
	}

    /**
     * serve CSS
     * @param $location
     */
    private function serveCSS($location) {
        global $store_handler,$xtPlugin;

        if (!isset($this->resources[$location]['css']) || !is_array($this->resources[$location]['css'])) return;
        
        $filename = $this->css_file.'_'.$store_handler->shop_id._STORE_TEMPLATE.'_'.$location.'.css';
		$this->sortResources($this->resources[$location]['css'],'sort_order');

        $baseUrl = _SYSTEM_BASE_URL._SRV_WEB;
		$baseUrlCache = $baseUrl.'cache/';
		$addFileTime = true;

		($plugin_code = $xtPlugin->PluginCode('class.xt_minify.php:serveCSS_top')) ? eval($plugin_code) : false;
        if(isset($plugin_return_value))
            return $plugin_return_value;
		
        if ($this->css_minify_option=='minifymerge') {

            if (!$this->checkCacheFile('cache/'.$filename,'css')) {

            $file_content=array();
            foreach ($this->resources[$location]['css'] as $key => $arr) {
                if (file_exists($arr['source'])) {
                    $content = $this->getFileContent($arr['source']);

                    // set current dir
                    $option['currentDir']=dirname ($arr['source']);
                    // set document root
                    $docRoot = preg_replace('/'. preg_quote(_SRV_WEB, '/') . '$/', '', _SRV_WEBROOT);
                    $option['docRoot'] = $docRoot;

                    $content = Minify_CSSmin::minify($content,$option);
                    $file_content[]=$content;
                }
            }
            $file_content=implode("",$file_content);
            $this->save_cache_file($file_content,$filename);

            }

            $filetime= filemtime ( 'cache/'.$filename);
            if ($addFileTime) $filename.='?'.md5($filetime);

            echo '<link rel="stylesheet" type="text/css" href="'.$baseUrlCache.$filename.'" />'."\n";

        } elseif ($this->css_minify_option=='merge') {

            $file_content='';
            foreach ($this->resources[$location]['css'] as $key => $arr) {
                if (file_exists($arr['source'])) {
                    $content = $this->getFileContent($arr['source']);

                    $currentDir = dirname ($arr['source']);
                    $docRoot = preg_replace('/'. preg_quote(_SRV_WEB, '/') . '$/', '', _SRV_WEBROOT);
                    $content = Minify_CSS_UriRewriter::rewrite($content, $currentDir, $docRoot);

                    $file_content.=$content;
                }
            }
            $this->save_cache_file($file_content,$filename);

            $filetime= filemtime ( 'cache/'.$filename);

            if ($addFileTime) $filename.='?'.md5($filetime);

            echo '<link rel="stylesheet" type="text/css" href="'.$baseUrlCache.$filename.'" />'."\n";

        } elseif ($this->css_minify_option=='single') {

            foreach ($this->resources[$location]['css'] as $key => $arr) {
                if (file_exists($arr['source'])) {
                    $filetime= filemtime ($arr['source']);
                    $parameters = $this->getParameterString($arr['params']);
                    if ($parameters=='') {
                        $parameters.='?'.md5($filetime);
                    } else {
                        $parameters.='&'.md5($filetime);
                    }

                    echo '<link rel="stylesheet" type="text/css" href="'.$baseUrl.$arr['source'].$parameters.'" />'."\n";
                }
            }

        }
        ($plugin_code = $xtPlugin->PluginCode('class.xt_minify.php:serveCSS_bottom')) ? eval($plugin_code) : false;
    }

    /**
     * serve JS files
     * @param $location
     */
    private function serveJS($location) {
        global $store_handler,$xtPlugin;

        if (!is_array($this->resources[$location]['js'])) return;
        
        $filename = $this->js_file.'_'.$store_handler->shop_id._STORE_TEMPLATE.'_'.$location.'.js';
		$this->sortResources($this->resources[$location]['js'],'sort_order');
        // js minify

		$type = '';
		if(_STORE_META_DOCTYPE_HTML != "html5"){
			$type = 'type="text/javascript" ';
		}

        $baseUrl = _SYSTEM_BASE_URL._SRV_WEB;
        $baseUrlCache = $baseUrl.'cache/';
        $addFileTime = true;

		($plugin_code = $xtPlugin->PluginCode('class.xt_minify.php:serveJS_top')) ? eval($plugin_code) : false;
        if(isset($plugin_return_value))
            return $plugin_return_value;
		
        if ($this->js_minify_option=='minifymerge') {

            if (!$this->checkCacheFile('cache/'.$filename,'js')) {

            $file_content=array();
            foreach ($this->resources[$location]['js'] as $key => $arr) {
                if (file_exists($arr['source'])) {
                    $content = $this->getFileContent($arr['source']);

                    if (!strpos($arr['source'],'.min.')) {
                        $content = \Minify\JS\JShrink::minify($content);
                    } 

                    $file_content[]=$content;
                }
            }

            $file_content = implode("\n;",$file_content);

            $this->save_cache_file($file_content,$filename);
        }
            $filetime= filemtime ( 'cache/'.$filename);

            if ($addFileTime) $filename.='?'.md5($filetime);

            echo '<script '.$type.'src="'.$baseUrlCache.$filename.'"></script>'."\n";


        } elseif($this->js_minify_option=='merge') {

            $file_content=array();
            foreach ($this->resources[$location]['js'] as $key => $arr) {
                if (file_exists($arr['source'])) {
                    $content = $this->getFileContent($arr['source']);
                    $file_content[]=$content;
                }
            }
            $file_content = implode("\n;",$file_content);
            $this->save_cache_file($file_content,$filename);

            $filetime= filemtime ( 'cache/'.$filename);

            if ($addFileTime) $filename.='?'.md5($filetime);

            echo '<script '.$type.'src="'.$baseUrlCache.$filename.'"></script>'."\n";



        } elseif($this->js_minify_option=='single') {

            foreach ($this->resources[$location]['js'] as $key => $arr) {
                if (file_exists($arr['source'])) {
                    $filetime= filemtime ($arr['source']);
                    $parameters = $this->getParameterString($arr['params']);
                    if ($parameters=='') {
                        $parameters.='?'.md5($filetime);
                    } else {
                        $parameters.='&'.md5($filetime);
                    }
                    echo '<script '.$type.'src="'.$baseUrl.$arr['source'].$parameters.'"></script>'."\n";

                } else {
                    echo 'dont exists:'.$arr['source'].'<br>';
                }
            }
        }
        ($plugin_code = $xtPlugin->PluginCode('class.xt_minify.php:serveJS_bottom')) ? eval($plugin_code) : false;
    }


    function getParameterString($params)
    {
        $parameters = '';
        if (is_array($params) && count($params))
        {
            $key_val = array();
            foreach ($params as $k => $v)
            {
                $key_val[] = urlencode($k).'='.urlencode($v);
            }
            $parameters = '?'.implode('&', $key_val);
        }
        return $parameters;
    }

}
