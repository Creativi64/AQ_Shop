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

define('_DIR_SEPERATOR',DIRECTORY_SEPARATOR );

/**
* check if resource is writeable by webserver
* 
* @param mixed $resource
*/


function logError($message,$source) {


    if (is_array($message)) {
        $message = explode('|',$message);
    }

    $error_file = 'error_log/error.log';
    $fp = fopen($error_file, 'a');
    $time = @date('[d/M/Y:H:i:s]');
    fwrite($fp, "$time | $source | $message" . PHP_EOL);

    fclose($fp);
}


/**
* make a header redirect to given url
* 
* @param string $url
*/
function _redirect($url) {
	$url = preg_replace('/[\r\n]+(.*)$/im', '', $url);
	$url = html_entity_decode($url);
	header('Location: ' . $url);
	exit;
}


function _pageLink($page,$params='') {
	$link = 'index.php?page='.$page.'&'.session_name().'='.session_id().'&'.$params;
	return $link;
}

/**
* deacitave all countries, activate default country (requirement for trusted shops)
* 
* @param string $active_country
* @param mixed $prefix
*/
function _activateCountry($active_country,$prefix='') {
    global $idb;
    
    $active_country=substr($active_country,0,2);
    // deactivate all countries
    try {
        $query = "UPDATE ".$prefix."countries SET status = 0";
        $idb->Execute($query);
    } catch (exception $e) {
        // echo $val;
        return $e;
    }

    try {
        $query = "UPDATE ".$prefix."countries SET status = 1 WHERE countries_iso_code_2 ='".$active_country."'";
        $idb->Execute($query);
    } catch (exception $e) {
        // echo $val;
        return $e;
    }


    $query = "UPDATE ".$prefix."config_1 SET config_value = '".$active_country."' WHERE config_key ='_STORE_COUNTRY'";
    $idb->Execute($query);

    if ($active_country=='BR') {
        $query = "UPDATE ".$prefix."config_1 SET config_value = 'BRL' WHERE config_key ='_STORE_CURRENCY'";
        $idb->Execute($query);
    }

    // set as home country

    return -1;


}

function _taxSetup($country,$prefix='') {
       global $idb;


    switch ($country) {
        case 'BE': // Belgium
            setEUTax(21,12,$prefix);
            break;
        case 'BG': // Bulgaria
            setEUTax(20,9,$prefix);
            break;
        case 'DK': // Denmark
            setEUTax(25,0,$prefix);
            break;
        case 'DE': // Germany
            setEUTax(19,7,$prefix);
            break;
        case 'EE': // Estonia
            setEUTax(20,9,$prefix);
            break;
        case 'FI': // Finland
            setEUTax(23,13,$prefix);
            break;
        case 'FR': // France
            setEUTax(19.6,7,$prefix);
            break;
        case 'GR': // greece
            setEUTax(23,13,$prefix);
            break;
        case 'GB': // UK
            setEUTax(20,5,$prefix);
            break;
        case 'IE': // ireland
            setEUTax(23,13.5,$prefix);
            break;
        case 'LV': // Latvia
            setEUTax(21,12,$prefix);
            break;
        case 'LT': // Lithuania
            setEUTax(21,9,$prefix);
            break;
        case 'MT': // Malta
            setEUTax(18,5,$prefix);
            break;
        case 'LU': // Luxembourg
            setEUTax(15,12,$prefix);
            break;
        case 'NL': // Netherlands
            setEUTax(19,6,$prefix);
            break;
        case 'PL': // Poland
            setEUTax(23,8,$prefix);
            break;
        case 'PT': // Portugal
            setEUTax(23,8,$prefix);
            break;
        case 'RO': // Romania
            setEUTax(24,9,$prefix);
            break;
        case 'SE': // Sweden
            setEUTax(25,12,$prefix);
            break;
        case 'SI': // Slovenia
            setEUTax(20,8.5,$prefix);
            break;
        case 'ES': // Spain
            setEUTax(21,10,$prefix);
            break;
        case 'CZ': // Czech Republic
            setEUTax(20,14,$prefix);
            break;
        case 'HU': // Hungary
            setEUTax(27,18,$prefix);
            break;
        case 'CY': //
            setEUTax(17,8,$prefix);
            break;
        case 'CH': //
            setEUTax(8,2.5,$prefix);
            break;


        case 'BR':
            $query = "INSERT INTO ".$prefix."tax_class VALUES (1, 'No Tax', NULL, current_timestamp)";
            $idb->Execute($query);

            $query = "INSERT INTO ".$prefix."tax_rates VALUES (1, 31, 1, 0.0000, NULL, current_timestamp)";
            $idb->Execute($query);
            $query = "INSERT INTO ".$prefix."tax_rates VALUES (3, 6, 1, 0.0000, NULL, current_timestamp)";
            $idb->Execute($query);
            break;
        default:
            $query = "INSERT INTO ".$prefix."tax_class VALUES (1, 'Standardsatz', NULL, current_timestamp)";
            $idb->Execute($query);
            $query = "INSERT INTO ".$prefix."tax_class VALUES (2, 'Ermäßigter Steuersatz', NULL, current_timestamp)";
            $idb->Execute($query);

            $query = "INSERT INTO ".$prefix."tax_rates VALUES (1, 31, 1, 19.0000, NULL, current_timestamp)";
            $idb->Execute($query);
            $query = "INSERT INTO ".$prefix."tax_rates VALUES (2, 31, 2, 7.0000, NULL, current_timestamp)";
            $idb->Execute($query);
            $query = "INSERT INTO ".$prefix."tax_rates VALUES (3, 6, 1, 0.0000, NULL, current_timestamp)";
            $idb->Execute($query);
            $query = "INSERT INTO ".$prefix."tax_rates VALUES (4, 6, 2, 0.0000, NULL, current_timestamp)";
            $idb->Execute($query);


            break;
    }

    return -1;


}

function setEUTax($normal,$optional,$prefix) {
    global $idb;


    $query = "INSERT INTO ".$prefix."tax_class VALUES (1, 'Standardsatz', NULL, current_timestamp)";
    $idb->Execute($query);
    $query = "INSERT INTO ".$prefix."tax_class VALUES (2, 'Ermäßigter Steuersatz', NULL, current_timestamp)";
    $idb->Execute($query);

    $query = "INSERT INTO ".$prefix."tax_rates VALUES (1, 31, 1, '".$normal."', NULL, current_timestamp)";
    $idb->Execute($query);
    $query = "INSERT INTO ".$prefix."tax_rates VALUES (2, 31, 2, '".$optional."', NULL, current_timestamp)";
    $idb->Execute($query);
    $query = "INSERT INTO ".$prefix."tax_rates VALUES (3, 6, 1, 0.0000, NULL, current_timestamp)";
    $idb->Execute($query);
    $query = "INSERT INTO ".$prefix."tax_rates VALUES (4, 6, 2, 0.0000, NULL, current_timestamp)";
    $idb->Execute($query);


}

/**
* deactivate all languages, only activate standard language (requirement for trusted shops)
* 
* @param string $active_language
* @param mixed $prefix
*/
function _activateLanguage($active_language,$prefix='') {
     global $idb;
     
     $active_language=substr($active_language,0,2);

    try {
        $query = "UPDATE ".$prefix."languages SET language_status = 1";
        $idb->Execute($query);
    } catch (exception $e) {
        // echo $val;
        return $e;
    }
    /*
        try {
            $query = "UPDATE ".$prefix."languages SET language_status = 1 WHERE code ='".$active_language."'";
            $idb->Execute($query);
        } catch (exception $e) {
            // echo $val;
            return $e;
        }
    */
    $query = "UPDATE ".$prefix."config_1 SET config_value = '".$active_language."' WHERE config_key ='_STORE_LANGUAGE'";
    $idb->Execute($query);

    return -1;
    
}

/**
* insert content of sql file line per line into database
* 
* @param mixed $filename
* @param mixed $prefix
* @return exception
*/
function _installSQL($filename,$prefix='',$language_code='') {
	global $idb;

    $query = '';
	// open sql
    if ($language_code=='') {
        $filename = _SRV_WEBROOT.'xtInstaller/sql/'.$filename;
    } else {
        $filename = _SRV_WEBROOT.'xtInstaller/languages/'.$language_code.'/'.$filename;
    }

	$sql_content = _getFileContent($filename);

	// replace windows linefeeds
	$sql_content = str_replace("\r\n","\n",$sql_content);
	
	$queries = array();

	$chars = strlen($sql_content);

	for ($i=0;$i<$chars;$i++) {

		// check if char is ; and next \n
		if ($sql_content[$i]==';' && $sql_content[$i+1]=="\n") {
			$query.=$sql_content[$i];
			$queries[]=$query;
			$query = '';
			$i++;
		} else {

			if ($sql_content[$i]=='-' && $sql_content[$i+1]=='-') {

				// skip to next \n
				for ($ii=$i;$ii<$chars;$ii++) {

					if ($sql_content[$ii]=="\n") {
						break;
					} else {
						$i++;
					}

				}


			} else {
				if (!isset($query)) $query='';
				$query.=$sql_content[$i];
			}

		}

	}


	foreach ($queries as $key => $val) {

		$query = trim($val);
		$query = str_replace('##_',$prefix,$query);

		// ok, now search vor OTHER INSERT INTO statements, and break them up
		if (substr($query,0,6)=='INSERT') {
			$check_qry = substr($query,7);

			if (strstr($check_qry,'INSERT')) {
				$qry = explode('INSERT',$check_qry);
				foreach ($qry as $k => $v) {
					$queries[]='INSERT '.$v;
				}
				unset ($queries[$key]);
			} else {
				$queries[$key]=$query;
			}

		} else {
			$queries[$key]=$query;
		}

	}


	foreach ($queries as $key => $val) {
		try {
			$idb->Execute($val);
		} catch (exception $e) {
           // echo $val;
			return $e;
		}
	}

	return -1;

}

function _getFileContent($filename) {
	$handle = fopen($filename, 'rb');
	$content = fread($handle, filesize($filename));
	fclose($handle);
	return $content;

}

/**
* install mail templates from txt source
* 
* @param mixed $lng
* @param mixed $max_id
*/
function _installMailTemplates($lng,$max_id=0,$prefix) {
	global $idb;
	if ($max_id==0) return false;

	$mail_dir = _SRV_WEBROOT.'xtInstaller/languages/'.$lng.'/mails';
	for ($i=1;$i<$max_id+1;$i++) {

		if (file_exists($mail_dir.'/'.$i.'_'.$lng.'_txt.txt')) {

			$file_prefix = $i.'_'.$lng.'_';

			$html_content = _getFileContent($mail_dir.'/'.$file_prefix.'html.txt');
			$txt_content = _getFileContent($mail_dir.'/'.$file_prefix.'txt.txt');
			$subject = _getFileContent($mail_dir.'/'.$file_prefix.'subject.txt');

			$insert_array=array();
			$insert_array['tpl_id']=$i;
			$insert_array['language_code']=$lng;
			$insert_array['mail_body_html']=$html_content;
			$insert_array['mail_body_txt']=$txt_content;
			$insert_array['mail_subject']=$subject;
			try {
				$idb->AutoExecute($prefix.'mail_templates_content',$insert_array);
			} catch (exception $e) {
				return $e->msg;
			}

		}
	}

	return -1;

}

function _genPass() {
    $newpass = "";
    $laenge=6;
    $laengeS = 2;
    $string="ABCDEFGHIJKLMNPQRSTUVWXYZabcdefghijklmnpqrstuvwxyz123456789";
    $stringS = "!#$%&()*+,-./";

    mt_srand((double)microtime()*1000000);

    for ($i=1; $i <= $laenge; $i++) {
        $newpass .= substr($string, mt_rand(0,strlen($string)-1), 1);
    }
    for ($i = 1; $i <= $laengeS; $i++) {
        $newpass .= substr($stringS, mt_rand(0, strlen($stringS) - 1), 1);
    }
    $newpass_split = str_split($newpass);
    shuffle($newpass_split);
    $newpass = implode($newpass_split);
    return $newpass;
}

function _importLang($language_code, $install_prefix='xt') {
		global $idb,$filter;
		
		require_once _SRV_WEBROOT.'xtFramework/library/phpxml/xml.php';
		
		// $language, $currency
		// well, import language file

        $path = 'media/lang/';
        $file = _SRV_WEBROOT.$path.$language_code.'.xml';
        $cnt_file = _SRV_WEBROOT.$path.$language_code.'_content.yml';



        if (!file_exists($file)) return 'language file:'. $file.' not found';
        if (!file_exists($cnt_file)) return 'language file:'. $cnt_file.' not found';
        

        if (file_exists($file) && file_exists($cnt_file)) {
            
            // add language
            $xml = file_get_contents($file);
            $xml_data = XML_unserialize($xml);

          
           // check if language allready existing
            $code = $filter->_filter($xml_data['xtcommerce_language']['code'],'lng');
            $lng = _getLanguageList('admin','code', $install_prefix);
            $curr = $filter->_filter($xml_data['xtcommerce_language']['default_currency'],'cur');
            

            $_data = array();     
            if (is_array($lng[$code]))  $_data['languages_id']=$lng[$code]['languages_id'];
                
            $_data['name'] = $filter->_filter($xml_data['xtcommerce_language']['name']);  
            $_data['code'] = $code;
            $_data['content_language']=$code;
            
            $_data['default_currency'] = $curr;
            $_data['font'] = $filter->_filter($xml_data['xtcommerce_language']['font']);
            $_data['font_position'] = $filter->_filter($xml_data['xtcommerce_language']['font_position']);
            $_data['font_size'] = $filter->_filter($xml_data['xtcommerce_language']['font_size']);
            $_data['image'] = $filter->_filter($xml_data['xtcommerce_language']['image']);
            $_data['language_charset'] = 'utf-8';
            $_data['setlocale'] = $filter->_filter($xml_data['xtcommerce_language']['setlocale']);


            $rtn = _save_lang($_data, $install_prefix);
            if ($rtn!=-1) return $rtn;
            // import definitions
            $replace=false;
            if (isset($data['replace_existing'])) $replace=true;    
            //$language->_importXML($cnt_file,$code,$replace);
            _importYML($cnt_file,$code,$replace, $install_prefix);
            
            // check for currencies
            $cur = _getCurrencyList('admin','code', $install_prefix);
            if (!is_array($cur[$curr])) {
                // add currency
                $curr_data = array();
                $curr_data['code']=$curr;
                $curr_data['dec_point']=',';
                $curr_data['decimals']='2'; 
                $curr_data['prefix']=$curr; 
                $curr_data['suffix']=''; 
                $curr_data['thousands_sep']='.'; 
                $curr_data['title']=$curr; 
                $curr_data['value_multiplicator']='1';
                _save_currency($curr_data, $install_prefix);
            }
            // duplicate country definition
            // check if lng country list exists
            $country_file = _SRV_WEBROOT.$path.$code.'_countries.csv';
            if (!file_exists($country_file)) {
                // load english ones
                $country_file = _SRV_WEBROOT.$path.'en_countries.csv';
                _importCountries($country_file,$code, $install_prefix);
            } else {
                _importCountries($country_file,$code, $install_prefix);
            }

            // load stopwordlist
            $stopwords_file = _SRV_WEBROOT.$path.$code.'_stop_words.csv';
            if (file_exists($stopwords_file)) {
                // load english ones
                _importStopWords($stopwords_file,$code, $install_prefix);
            }
            // mail templates
           _installMailTemplates($code,15,$install_prefix);
            
            // deactivate for all stores
            return -1;
        }

}

function _getLanguageList($list_type = '',$index='', $prefix){
	global $idb;

	if ($list_type!='all')
	$qry_where = " WHERE l.language_status = '1'";

	$record = $idb->Execute("SELECT * FROM " . $prefix . "languages l ".$qry_where." order by sort_order");
	while(!$record->EOF){
		$record->fields['id'] = $record->fields['code'];
		$record->fields['text'] = $record->fields['name'];
		$record->fields['icon'] = $record->fields['image'];
		$record->fields['edit'] = $record->fields['allow_edit'];

		if ($index=='') $data[] = $record->fields;
		if ($index=='code') $data[$record->fields['code']] = $record->fields;
		$record->MoveNext();
	}$record->Close();

	return $data;
}

function _save_lang($_data, $prefix)
{
	global $idb;
	
	try {
		$idb->AutoExecute($prefix.'languages',$_data);
	} catch (exception $e) {
		return $e->getMessage();
	}
    return -1;
}

function _importYML($file,$code,$replace=false, $prefix) {
	global $idb;

	if (!file_exists($file)) return;

	$lines = file ($file);

	// load language definitions
	$definitions = array();
	$rs = $idb->Execute("SELECT language_key FROM ".$prefix."language_content WHERE language_code='".$code."'");
	if ($rs->RecordCount()>0) {
		while (!$rs->EOF) {
			$definitions[$rs->fields['language_key']]='1';
			$rs->MoveNext();
		}
	}


	foreach ($lines as $line_num => $line) {
		// line nach = exploden
		$line_content = explode('=',$line);

		// 1teil aufsplitten
		$mod = explode('.',$line_content[0]);
		 
		if (!isset($definitions[$mod[2]]) && $mod[2]!='' && $mod[2]!='new')   {
			// key not existing
			$insert_data = array();
			$insert_data['language_key']=$mod[2];
			$insert_data['language_code']=$code;
			$insert_data['language_value']=trim(str_replace("\n",'',$line_content[1]));
			$insert_data['class']=$mod[1];
			$insert_data['plugin_key']=$mod[0];
			$insert_data['translated']='1';
			$idb->AutoExecute($prefix.'language_content',$insert_data);
		}
		 
	}

	// now get untranslated definitions and insert //TODO check if EN is existing
	$sql = "SELECT * FROM ".$prefix."language_content a WHERE a.language_code='en' and a.language_key NOT IN (SELECT language_key FROM ".$prefix."language_content b WHERE b.language_code='".$code."')";
	$rs = $idb->Execute($sql);
	if ($rs->RecordCount()>0) {
		while (!$rs->EOF) {
			$insert_data = array();
			$insert_data['language_key']=$rs->fields['language_key'];
			$insert_data['language_code']=$code;
			$insert_data['language_value']=$rs->fields['language_value'];
			$insert_data['class']=$rs->fields['class'];
			$insert_data['plugin_key']=$rs->fields['plugin_key'];
			$insert_data['translated']='0';
			$idb->AutoExecute($prefix."language_content",$insert_data);
			$rs->MoveNext();
		}
	}


}

function _getCurrencyList($list_type = 'store',$index='', $prefix){
	global $idb;

	$qry_where = " where c.currencies_id != '' ";

	$qry =  "SELECT * FROM " . $prefix."currencies c ".$qry_where." ";

	$record = $idb->Execute($qry);
	while(!$record->EOF){

		$record->fields['id'] = $record->fields['code'];
		$record->fields['text'] = $record->fields['title'];

		if ($index=='') $data[] = $record->fields;
		if ($index=='code') $data[$record->fields['code']] = $record->fields;
		$record->MoveNext();
	}$record->Close();

	return $data;
}

function _save_currency($curr_data, $prefix)
{
	global $idb;
	
	try {
		$idb->AutoExecute($prefix.'currencies',$curr_data);
	} catch (exception $e) {
        echo 'Error in importing currency '.$e->msg;
		return $e->msg;
	}	
}

function _importCountries($file,$code, $prefix) {
	global $idb;

    if (!file_exists($file)) return;

	$handle = fopen ($file,"r");
	$idb->Execute("DELETE FROM ".$prefix."countries_description WHERE language_code='".$code."'");
	while ( ($data = fgetcsv ($handle, 1000, ";",'"')) !== FALSE ) {

		$insert_array=array();
		$insert_array['language_code']=$code;
		$insert_array['countries_name']=$data[1];
		$insert_array['countries_iso_code_2']=$data[2];

		$idb->AutoExecute($prefix."countries_description",$insert_array);
	}

	fclose ($handle);


}

function _importStopWords($file,$code, $prefix) {
    global $idb;

    if (!file_exists($file)) return;

    $handle = fopen ($file,"r");
    $idb->Execute("DELETE FROM ".$prefix."seo_stop_words WHERE language_code='".$code."'");
    while ( ($data = fgetcsv ($handle, 1000, ";",'"')) !== FALSE ) {

        $insert_array=array();
        $insert_array['language_code']=$code;
        $insert_array['stopword_lookup']=$data[0];
        $insert_array['stopword_replacement']=$data[1];
        $insert_array['replace_word']=$data[2];

        $idb->AutoExecute($prefix."seo_stop_words",$insert_array);
    }

    fclose ($handle);


}

function xtErrorHandler($errno, $errstr, $errfile, $errline)
{

    // dedect diffrent hoster issues

    // session_start issue on Strato
    if (strpos($errstr,'session_start')!==false) {

        echo 'STRATO als Hoster erkannt.....<br />';
        echo 'Installer versucht session_start() Problem des STRATO Servers zu fixen...<br />';

        $ini_folders = array();
        $ini_folders[]='';
        $ini_folders[]='xtInstaller';
        $ini_folders[]='xtAdmin';

        $line = 'session.save_path='._SRV_WEBROOT.'cache/';

        foreach ($ini_folders as $key=>$dir) {
           $resp =  generatePhPIniFile($dir,$line,'session.save_path');
            if ($resp==false) {
                echo 'Verzeichnis '._SRV_WEBROOT.$dir.'<br />';
            } else {
                echo 'Datei '.$dir.'/php.ini wurde angelegt<br />';
            }
        }
        echo ('<br /><br />Bitte klicken Sie <a href="index.php">[Hier]</a> um die Fehlerbehebung zu tesen.');



    }

    return true;
}


/**
 * @param $dir
 * @return array|bool
 */
function generatePhPIniFile($dir,$line,$lookup='') {

    $dir = _SRV_WEBROOT.$dir;


    if (@is_writeable($dir)) {
        // check if file exists
        if (file_exists($dir._DIR_SEPERATOR.'php.ini')) {
            $content = file_get_contents($dir._DIR_SEPERATOR.'php.ini');
            // ioncube line there ?

            // check if writeable
            if (!is_writeable($dir._DIR_SEPERATOR.'php.ini')) {
                return false;
            } else {
                if (!strstr($content,$lookup)) {
                    $fh = @fopen($dir._DIR_SEPERATOR.'php.ini',"a+");
                    if ($fh !== false) {
                            fwrite($fh,$line . PHP_EOL);

                    }
                    fclose($fh);
                    return true;
                }
            }
            return true;
        } else {
            $fh = @fopen($dir._DIR_SEPERATOR.'php.ini',"wb");
            if ($fh !== false) {
                    fwrite($fh,$line . PHP_EOL);
            }
            fclose($fh);
            return true;
        }
    } else {
        return false;
    }


}



?>