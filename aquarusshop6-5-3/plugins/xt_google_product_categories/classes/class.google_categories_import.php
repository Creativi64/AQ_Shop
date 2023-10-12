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

define('TABLE_GOOGLE_CATEGORIES', DB_PREFIX.'_google_categories');

require_once _SRV_WEBROOT . 'xtFramework/admin/classes/class.adminDB_DataSave.php';

class google_categories_import
{
    const COUNTRIES = "de-DE,en-GB";
    const GOOGLE_BASE_URL = "https://www.google.com/basepages/producttype/";
    const FILE = "taxonomy-with-ids.xx-XX.txt";


    public function _run_cron($params = array())
    {
        global $db;

        $fileLines = array();
        $countries = explode(',',self::COUNTRIES);

        // download taxonomy files and save lines to array
        foreach ($countries as $c)
        {
            $arr = explode('-',$c,2);
            $lang = $arr[0];
            $country = $arr[1];

            $fileName = str_replace('xx-XX',$c, self::FILE);
            $file =  _SRV_WEBROOT._SRV_WEB_EXPORT . $fileName;

            $fp = fopen($file, 'w+');
            try {

                $downloaded = $this->download($fileName, $fp);
                if (!$downloaded)
                    continue;
                fclose($fp);
                $fileLines[$lang][$country] = file($file);
            }
            catch(Exception $e)
            {
                fclose($fp);
                throw $e;
            }
        }

        // delete existing db entries
        $db->Execute('TRUNCATE '.TABLE_GOOGLE_CATEGORIES);

        // process lines array
        foreach($fileLines as $lang => $language)
        {
            foreach($language as $country => $countryLines)
            {
                $inp_arr = array();
                $val_arr = array();
                foreach($countryLines as $k => $line)
                {
                    if ($k == 0)
                    {
                        continue;
                    } // skip first line which contains something like # Google_Product_Taxonomy_Version: 2015-02-19

                    self::parseLine($line, $id, $catPath);

                    $inp_arr[] = $id;
                    $inp_arr[] = $lang;
                    $inp_arr[] = $country;
                    $inp_arr[] = $k;
                    $inp_arr[] = $catPath;

                    $val_arr[] = "(?,?,?,?,?)";
                }
                $val_str = implode(',', $val_arr);
                $val_str = trim($val_str);
                if(!empty($val_arr))
                {
                    $db->Execute("
                    INSERT INTO " . TABLE_GOOGLE_CATEGORIES . "
                    (`google_category_id`,`language`,`country`,`sort_order`,`category_path`) VALUES $val_str;
                    ", $inp_arr);
                }
            }
        }
        $_REQUEST['timer_total'] = 0.1;
        return true;
    }

    static function parseLine($line, &$id, &$catPath = '')
    {
        $arr = explode('-', $line, 2 );
        $id = trim($arr[0]);
        $catPath = trim($arr[1]);
    }

    private function download($fileName, $fp)
    {
        $url = self::GOOGLE_BASE_URL.$fileName;
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 50);
        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_HEADER  , true);
        curl_setopt($ch, CURLOPT_NOBODY  , true);
        curl_exec($ch);
        if (curl_errno($ch))
        {
            $no = curl_errno($ch);
            $msg = curl_error($ch);
            curl_close($ch);
            throw new Exception('download: curl error: '.$no.'-'.$msg.'-'.$url);
        }
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if($httpcode > 302)
        {
            throw new Exception('download: HTTP-Response: '.$httpcode.' for URL '.$url);
        }

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 50);
        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_HEADER  , false);
        curl_exec($ch);
        if (curl_errno($ch))
        {
            $no = curl_errno($ch);
            $msg = curl_error($ch);
            curl_close($ch);
            throw new Exception('download: curl error: '.$no.'-'.$msg.'-'.$url);
        }

        return true;
    }
}