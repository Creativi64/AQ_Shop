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

 //  TODO  sämtliche sprach vars wenigsten lokal per include

defined ( '_VALID_CALL' ) or die ( 'Direct Access is not allowed.' );

include_once _SRV_WEBROOT . 'xtFramework/library/phpxml/xml.php';

defined('THEMES_VALIDATE_XML') ?: define('THEMES_VALIDATE_XML', false);
defined('THEMES_COMPRESS_CSS') ?: define('THEMES_COMPRESS_CSS', false);

class themes extends xt_backend_cls {

	var $master_id = 'code';

    // todo  nice to have? wenn <overrides> vorhanden sollten <less_import_directories> nicht nötig sein? auf schema 1.1 wechseln?
    // todo validierung auf basis der <version> ?
	private const _VALIDATE_XML = THEMES_VALIDATE_XML;

    private const _COMPRESS_CSS = THEMES_COMPRESS_CSS;

	private const _DEFAULT_THEME_DATA  = [
        'title' => false,
        'version' => false,
        'minimum_store_version' => false,
        'developer' => false,
        'url' => false,
        'marketplace_link' => false,
        'documentation_link' => false,
        'updatecheck_link' => false,
        'icon' => false,
        'less_support' => false,
        'less_folder' => false,
        'less_file' => false,
        'less_variable_file' => false,
        'css_file' => false,
        'less_import_directories' => false
    ];

    private const _HEADER_DEFAULT_ARRAY = ['theme_preview_icon','title', 'theme_version', 'code'];

    private const _TPL_OVERRIDE_MAIN_LESS = '
@import "../../__PARENT_TPL__/__LESS_DIR__/__MAIN_LESS_FILE__";
// local import of variables overrides the variables from parent __PARENT_TPL__
@import "__LESS_VARIABLE_FILE__";
';

	function _getParams() {
		global $language;

		$params = array ();
        $grouping = [];
        $header = [];
        $rowActions = $rowActionsFunctions = [];

		$params ['header'] = array ();
		$params ['master_key'] = $this->master_id;
		$params ['default_sort'] = 'code';

		$params ['SortField'] = 'code';
		$params ['SortDir'] = 'ASC';

		if($this->url_data['pg']=='overview' && !$this->url_data['edit_id'] && $this->url_data['new'] != true){
			$params['include'] = self::_HEADER_DEFAULT_ARRAY;

            $rowActions[] = array('iconCls' => 'generate_css', 'qtipIndex' => 'qtip1', 'tooltip' => __text('TEXT_THEME_GENERATECSS'));
            if ($this->url_data['edit_id'])
                $js = "var edit_id = ".$this->url_data['edit_id'].";";
            else
                $js = "var edit_id = record.id;";
            $js.= "Ext.Msg.confirm('".__text('TEXT_THEMES')."','".__text('TEXT_THEME_GENERATECSS_DIALOG')."',function(btn){doCssGeneration(edit_id,btn);})";
            $rowActionsFunctions['generate_css'] = $js;

            $rowActions[] = array('iconCls' => 'plus', 'qtipIndex' => 'qtip1', 'tooltip' => __text('TEXT_THEME_CREATE_OVERRIDE'));
            if ($this->url_data['edit_id'])
                $js = "var edit_id = ".$this->url_data['edit_id'].";";
            else
                $js = "var edit_id = record.id;";
            $js.= "Ext.Msg.confirm('".__text('TEXT_THEMES')."','".__text('TEXT_THEME_CREATE_OVERRIDE_DIALOG')."',function(btn){doCreateTemplateOverride(edit_id,btn);})";
            $rowActionsFunctions['plus'] = $js;

			$rowActions[] = array('iconCls' => 'copy', 'qtipIndex' => 'qtip1', 'tooltip' => __text('TEXT_THEME_COPY'));
			if ($this->url_data['edit_id'])
				$js = "var edit_id = ".$this->url_data['edit_id'].";";
			else
				$js = "var edit_id = record.id;";
			$js.= "Ext.Msg.confirm('".__text('TEXT_THEMES')."','".__text('TEXT_THEME_COPY_DIALOG')."',function(btn){doThemeCopy(edit_id,btn);})";
			$rowActionsFunctions['copy'] = $js;

            $rowActions[] = array('iconCls' => 'theme_history', 'qtipIndex' => 'qtip1', 'tooltip' => __text('TEXT_THEME_HISTORY'));
            if ($this->url_data['edit_id'])
                $js = "var edit_id = ".$this->url_data['edit_id']."; var edit_name = '".htmlentities($this->url_data['edit_id'])."';\n";
            else
                $js = "var edit_id = record.id; var edit_name=record.get('edit_id');\n";
            $js .= "addTab('adminHandler.php?load_section=themes_history&theme_dir='+edit_id,'".__text('TEXT_THEME_HISTORY')." ('+edit_id+')', 'theme_history'+edit_id)";
            $rowActionsFunctions['theme_history'] = $js;

			$extF = new ExtFunctions();

			$js = "function doThemeCopy(edit_id,btn){
	  		var edit_id = edit_id;
	  		if (btn == 'yes') {
	  		".$extF->_RemoteWindow("TEXT_COPY_THEME","TEXT_COPY_THEME","row_actions.php?type=copy_theme&theme='+edit_id+'", '', array(), 800, 600).' new_window.show();'."
			}
		};";

            $js .= "function doCreateTemplateOverride(edit_id,btn){
	  		var edit_id = edit_id;
	  		if (btn == 'yes') {
	  		".$extF->_RemoteWindow("TEXT_THEME_CREATE_OVERRIDE","TEXT_THEME_CREATE_OVERRIDE","row_actions.php?type=create_template_override&theme='+edit_id+'", '', array(), 800, 600).' new_window.show();'."
			}
		};";

			$js .= "function doCssGeneration(edit_id,btn){
	  		var edit_id = edit_id;
	  		if (btn == 'yes') {
	  		".$extF->_RemoteWindow("TEXT_THEME_GENERATECSS","TEXT_THEME_GENERATECSS","row_actions.php?type=generate_css&theme='+edit_id+'", '', array(), 800, 600).' new_window.show();'."
			}
		};";

			$params['rowActionsJavascript'] = $js;

		}else{
			// dynamic tabs ?
			$tab = $this->getLessConfig($this->url_data['edit_id'],true);
			foreach ($tab as $_group => $_fields) {
				foreach ($_fields as $_key => $_var) {
					$grouping[$_key]=array('position' => $_group,'text'=>$_group);
					$header[$_key]=array('text'=>'@'.$_key,'type'=>'textfield');
				}
			}
			// edit
			$params['exclude'] = array ('theme_preview_icon', 'configuration', 'file');
            $header['title']=array('readonly' => true);
            $header['version']=array('readonly' => true);
			$header['code']=array('type'=>'hidden');
			//$grouping['code']=array('position' => 'colors');
		}

		$params	['grouping']         = $grouping;
		$params ['header']           = $header;
		$params ['display_searchPanel'] = false;
		$params ['display_newBtn'] = false;
		$params ['display_editBtn'] = true;
		$params ['display_deleteBtn'] = false;

		$params ['rowActions'] = $rowActions;
		$params ['rowActionsFunctions'] = $rowActionsFunctions;

		return $params;
	}

	function _get($pID = 0)
    {
		if ($this->position != 'admin') return false;

		if ($pID === 'new') {
			$obj = $this->_set ( array (), 'new' );
			$pID = $obj->new_id;
		}

		if ($pID) {
			$data = $this->getLessConfig ( $pID );
			$data[0]['code']=$pID;
			// todo  titel ua sollten editerbar sein? in _set dann ins xml schreiben
            $theme_data = $this->readThemeXml($pID);
            $data[0]['title'] = $theme_data['title'];
            //$data[0]['version'] = $theme_data['version'];
            // usw // zZ readonly in _getParams
		}
		else if ($this->url_data['get_data']=='true')
        {
            $data = $this->getThemes ();
        }
		else {
            $data = [self::_HEADER_DEFAULT_ARRAY];
		}

		$obj = new stdClass;
		$obj->totalCount = count($data);

		if($obj->totalCount==0){
			$data[] =  array('icon'=>'', 'title'=>'', 'version'=>'', 'code'=>'', 'type'=>'');
		}

		$obj->data = $data;
		return $obj;
	}

	function _set($data, $set_type='edit') {

		global $logHandler;

		if ($this->position != 'admin') return false;

		$obj = new stdClass;

		if (!isset($data['code'])) {
			$obj->failed = true;
			return $obj;
		}

		$theme = $this->getThemes($data['code']);

		// duplicate current file
		$theme_dir = _SRV_WEBROOT . 'templates/' . $data['code'];
		$less_variable_file = $theme_dir.'/'.$theme[0]['less_folder'].'/'.$theme[0]['less_variable_file'];

		if (is_file ( $less_variable_file )) {

			$file_content = file_get_contents($less_variable_file);

			$updatedContent='';

			// save backup
			if (!copy($less_variable_file, str_replace('.less', '-'.time().'.org', $less_variable_file))) {
				$log_data = array();
				$log_data['error'] = 'cannot create duplicate of less file';
				$logHandler->_addLog('error', 'themes', '', $log_data);

				$obj->success = false;
				return $obj;
			}

			foreach (preg_split("/(\r\n|\n|\r)/", $file_content) as $ln) {

				if ($ln[0] != "@") {
					$updatedContent.=$ln."\n";
					continue;
				}

				$bits = explode(":", $ln);
				$key = substr(trim($bits[0]), 1);
				$value = trim($bits[1]);

				if (substr($value,-1)==';') $value=substr($value,0,strlen($value)-1);

				// check if value is in save data
				if (isset($data[$key]) &&  $data[$key]!=$value) {
					$updatedContent.='@'.$key.': '.$data[$key].';'."\n";
				} else {
					$updatedContent.=$ln."\n";
				}
			}
			if (is_writable($less_variable_file)) {
				file_put_contents($less_variable_file, $updatedContent);
				$obj->success = true;
				return $obj;
			} else {

				$log_data = array();
				$log_data['error'] = 'less file '.$less_variable_file.' not writeable';
				$logHandler->_addLog('error', 'themes', '', $log_data);

				$obj->success = false;
				return $obj;
			}
		} else {
			//echo 'file not exists';
			$obj->failed = true;
			$obj->message='file not exists'.$less_variable_file;
			return $obj;
		}
	}


    /**
     * load theme xml config file
     * @param string $theme
     * @param bool $return_tabs
     * @return array <multitype:, string>
     */
	private function getLessConfig($theme,$return_tabs=false) {

		$filess = _SRV_WEBROOT . 'templates/' . $theme . '/theme.xml';
		$theme_dir = _SRV_WEBROOT . 'templates/' . $theme;

        $xml = $this->readThemeXml($theme);

		$tabs = array();
		if (is_array ( $xml )) {

			// TODO validate XML file -- done in readThemeXml
            if (!isset($xml ['less_folder']) or
            !isset($xml ['less_file']) or
            !isset($xml ['less_variable_file']) or
            !isset($xml ['css_file'])) {
                die(__text('TEXT_THEME_ERROR_NO_CONFIG_FILE'));
            }

			if ($xml ['less_support']=='true') {
				$less_variable_file = $xml ['less_variable_file'];
				$less_variable_file = $theme_dir.'/'.$xml ['less_folder'].'/'.$less_variable_file;
				if (is_file ( $less_variable_file )) {

					$file_content = file_get_contents($less_variable_file);

					$cssVar[0] = [];
					$current_tab = '';
					foreach (preg_split("/(\r\n|\n|\r)/", $file_content) as $ln) {

						if ($ln[0] != "@") {
							if (substr($ln,0,3)=='//#') {
								$_ln = substr($ln,3,strlen($ln));
								$lnJson = json_decode($_ln);
								$current_tab = $lnJson->tab;
							}
							continue;
						}

						$bits = explode(":", $ln);
						$key = substr(trim($bits[0]), 1);
						$value = trim($bits[1]);
						if (substr($value,-1)==';') $value=substr($value,0,strlen($value)-1);

						$cssVar[0][$key] = $value;
						$tabs[$current_tab][$key]='';
					}

					if ($return_tabs==true) return $tabs;
					return $cssVar;

				} else {
					die(__text('TEXT_THEME_ERROR_NO_CONFIG_FILE'));
				}
			} else {
				die(__text('TEXT_THEME_ERROR_NO_LESS_SUPPORT'));
			}
		} else {
			die(__text('TEXT_THEME_ERROR_NO_CONFIG_FILE'));
		}
	}

    /**
     * load themes from template directory (backend themes overview)
     * @param string $theme_code to look for, which is the directories name, eg 'xt_responsive'.
     * @return array
     */
	private function getThemes($theme_code = '')
    {
        if (!empty($theme_code) && is_string($theme_code))
        {
            $dirs = [$theme_code];
        }
        else
        {
            $dirs = [];
            if ($dir = opendir(_SRV_WEBROOT . 'templates/'))
            {
                while (($f = readdir($dir)) !== false)
                {
                    if ((is_dir($f) || !stristr($f, ".")) && $f != "." && $f != "..")
                    {
                        if ($f != 'payment' && $f != '.svn' && $f != '__xtAdmin')
                        {
                            $dirs [] = $f;
                        }
                    }
                } // while
                closedir($dir);
            }
        }

        $themes = [];
		foreach ( $dirs as $key => $val )
		{
            $theme_data = $this->readThemeXml($val);
            if($theme_data)
            {
                $icon = '';
                if (!empty($theme_data['icon']))
                {
                    $icon = '../templates/' . $val . '/' . $theme_data['icon'];
                }
                $theme_data['theme_preview_icon'] = $icon;
                $theme_data['theme_version'] = $theme_data['version'];
                $theme_data['code'] = $val;

                $themes [] = $theme_data;
            }
		}
		return $themes;
	}

	private function readThemeXml($theme)
    {
        $theme_data = false;

        $file = _SRV_WEBROOT . 'templates/' . $theme . '/theme.xml';
        if (is_file ( $file ))
        {
            $theme_data = self::_DEFAULT_THEME_DATA;

            $theme_data['validation'] = ['required' => false, 'validated' => false, 'valid' => false, 'messages' => []];
            if (self::_VALIDATE_XML)
            {
                $theme_data['validation']['required'] = true;
                $xsd = _SRV_WEBROOT . 'templates/' . $theme . '/theme.xsd';
                if (is_file($xsd))
                {
                    $valid = $this->validateXML($file, $xsd);
                    $theme_data['validation']['validated'] = true;

                    foreach ($valid as $k => $v)
                    {
                        $theme_data['validation'][$k] = $v;
                    }
                    if($valid['valid'] != true)
                    {
                        $theme_data['validation']['messages'][] = ['message' => __text('TEXT_THEME_XML_INVALID'), 'type' =>'error'];
                    }
                }
                else {
                    $theme_data['validation']['messages'][] = ['message' => __text('TEXT_THEME_WARNING_XSD_MISSING'), 'type' =>'warning'];;
                }
            }

            $xml = $this->xmlToArray ( $file )['xtcommercetheme'];
            foreach($theme_data as $k => $v)
            {
                if($k == 'validation') continue;
                $theme_data[$k] = empty($xml[$k]) ? false : trim($xml[$k]);
            }
            if(!empty($xml['overrides'])) $theme_data['overrides'] = $xml['overrides'];

            // rewrite less import dirs
            if(!empty($theme_data['less_import_directories']))
            {
                $_dir = explode(',', $theme_data['less_import_directories']);
                $importDirectories = [];
                foreach ($_dir as $key => $val)
                {
                    $val = trim($val);
                    if (!empty($val))
                    {
                        $importDirectories[] = $val;
                    }
                }
                $theme_data['less_import_directories'] = $importDirectories;
            }
        }
        return $theme_data;
    }

    private function validateXML($xml, $xsd)
    {
        libxml_use_internal_errors(true);
        $dom = new DOMDocument();
        $dom->loadXML(file_get_contents($xml), LIBXML_NOBLANKS);
        $returnMsgs = [];
        $valid = false;
        if (($valid = $dom->schemaValidate($xsd)) != true)
        {
            $errors = libxml_get_errors();
            foreach ($errors as $error)
            {
                $msg = 'XML validation failed<br>'.$xml.'<br>'.$xsd.'<br><br>';
                switch ($error->level) {
                    case LIBXML_ERR_WARNING:
                        $msg .= "<b>Warning $error->code</b>: ";
                        $msgType = "warning";
                        break;
                    case LIBXML_ERR_ERROR:
                        $msg .= "<b>Error $error->code</b>: ";
                        $msgType = "error";
                        break;
                    case LIBXML_ERR_FATAL:
                        $msg .= "<b>Fatal Error $error->code</b>: ";
                        $msgType = "error";
                        break;
                    default:
                        $msg .= "<b>Error $error->code</b>: ";
                        $msgType = "error";
                }
                $msg .= trim($error->message);

                $returnMsgs[] = ['message' => $msg, 'type' => $msgType];
            }
            libxml_clear_errors();
        }
        return ['valid' => $valid, 'messages' => $returnMsgs];
    }

    private function processShowXmlValidationMsg($messages = [])
    {
        $hasErrors = false;
        foreach ($messages as $msg)
        {
            $this->msg($msg['message'], $msg['type']);
            if( $msg['type'] == 'error') $hasErrors = true;
        }
        return $hasErrors;
    }

    private function msg($msg, $type = 'success')
    {
        echo "<div class=\"alert alert-{$type}\" role=\"alert\" style=\"margin: 5px 0 10px 5px\">" . $msg . '</div>';
    }

    private function showCopyResult($theme, $new_theme, $output, $file_count)
    {
        $pi = pathinfo($new_theme);
        $msg_top =  '<b>Copied from '.$theme. '/ to '.$pi['basename'].'/</b><br>';
        $msg_top .=   $file_count . ' files copied<br />';
        $this->msg($msg_top);

        $output = '<div style="padding: 5px; border: 1px solid #777777; height: 100%; overflow:scroll;">' . $output . '</div>';
        echo '<div class="highlightbox">' . $output . '</div>';
    }

	/**
	 * unserialize XML to Standard Array
	 *
	 * @param String $xml
	 * @return Array
	 */
	private function xmlToArray($xml)
	{
		$xml = file_get_contents($xml);
		return XML_unserialize($xml);
	}

    /**
     * serialize Standard Array to XML
     *
     * @param $arr
     * @param $file
     * @return false|int
     */
    private function arrayToXml($arr, $file)
    {
        $xml = XML_serialize($arr);
        return file_put_contents($file, $xml);
    }

    /**
     * duplicate template
     * @param string $theme
     */
	public function copyTheme($theme)
    {
		$theme_path = $this->themeDirExists($theme);
        $newThemeFolder = $this->createNewThemeDirectory($theme_path);

        $result = $this->dircopy($theme_path, $newThemeFolder, true);
		$this->showCopyResult($theme, $newThemeFolder, $result['output'], $result['count']);
	}

    /**
     * something like createOverride, but copies only Variables.less and creates a special Templates.less
     * created Template.less is linked to the theme/template being overridden
     * font paths in Variables.less is modified to point to the original font dirs
     * theme.xml gets an extra entry <overrides>
     * @param $theme
     */
    public function createOverride($theme)
    {
        $output = '';
        $file_count = 0;

        $theme_path = $this->themeDirExists($theme);
        $theme_dir = pathinfo($theme_path)['basename'].DIRECTORY_SEPARATOR;
        $theme_data = $this->readThemeXml($theme);

        if(!empty($theme_data['overrides']))
        {
            $this->msg(__text('TEXT_THEME_ERROR_OVERRIDE_FROM_OVERRIDE'), 'error');
            die();
        }

        if($theme_data) // theme.xml exists
        {
            if($theme_data['less_support']
            ){
                if($theme_data['less_folder']
                    && $theme_data['less_file']
                    && $theme_data['less_variable_file']
                    && $theme_data['css_file']
                )
                {
                    $full_success = true;
                    $copy_entries = [];

                    $theme_path_new = $this->createNewThemeDirectory($theme_path);
                    $theme_dir_new = pathinfo($theme_path_new)['basename'].DIRECTORY_SEPARATOR;

                    $copy_entries[] = 'theme.xml';
                    $copy_entries[] = 'theme.xsd';
                    $copy_entries[] = 'css/stylesheet.css';

                    if($theme_data['icon']) $copy_entries[] = $theme_data['icon'];

                    // add original ccs file for immediate usage of them/template
                    $copy_entries[] = $theme_data['css_file'];

                    // add main less file to less folder
                    $copy_entries[] = $theme_data['less_folder'] . DIRECTORY_SEPARATOR . $theme_data['less_file'];

                    // add variables file to less folder
                    $copy_entries[] = $theme_data['less_folder'] . DIRECTORY_SEPARATOR . $theme_data['less_variable_file'];

                    $copy_entries = array_unique($copy_entries);

                    // copy all the stuff found to new theme/template
                    // todo var name aufräumen
                    foreach ($copy_entries as $entry)
                    {
                        $src = $theme_path . $entry;
                        if (is_dir($src))
                        {
                            $dst = $theme_path_new . $entry;
                            if (!is_dir($dst))
                            {
                                mkdir($dst, 0777, true);
                            }
                            $copy_dir_return = $this->dircopy($src, $dst, true);

                            $dst = $theme_dir_new . $entry;
                            $src = $theme_dir . $entry . '*';
                            $output .= '<br /><b>Copy ' . $src . ' > ' . $dst . '</b><br />';
                            $output .= $copy_dir_return['output'];
                            $file_count += $copy_dir_return['count'];
                        }
                        else
                        {
                            if (is_file($src))
                            {
                                $dst = $theme_path_new . $entry;
                                $destpath = pathinfo($dst);

                                if (!file_exists($destpath['dirname']))
                                {
                                    mkdir($destpath['dirname'], 0777, true);
                                }

                                $pi_dst = $theme_dir_new . $entry;
                                $pi_src = $theme_dir . $entry;
                                $output .= '<br /><b>Copy ' . $pi_src . ' > ' . $pi_dst . '</b>... ';

                                if (copy($src, $dst))
                                {
                                    touch($dst, filemtime($src));
                                    $output .= "OK";
                                    $file_count++;
                                }
                                else
                                {
                                    $full_success = false;
                                    $output .= "Error: File could not be copied!";
                                }
                                $output .= "<br />";
                            }
                        }
                    }

                    // override main less file
                    $less_main_file = $theme_path_new . $theme_data['less_folder'] . DIRECTORY_SEPARATOR . $theme_data['less_file'];
                    $s = str_replace(
                        ['__PARENT_TPL__', '__LESS_DIR__', '__MAIN_LESS_FILE__', '__LESS_VARIABLE_FILE__'],
                        [$theme, $theme_data['less_folder'], $theme_data['less_file'], $theme_data['less_variable_file']],
                        self::_TPL_OVERRIDE_MAIN_LESS
                    );
                    if (file_put_contents($theme_path_new . $theme_data['less_folder'] . DIRECTORY_SEPARATOR . $theme_data['less_file'], $s) != false)
                    {
                        $this->msg(__text('TEXT_THEME_OVERRIDE_LESS_FILE_CREATED'));
                    }
                    else
                    {
                        $full_success = false;
                        $this->msg(__text('TEXT_THEME_ERROR_CREATE_FILE') . ': ' . $less_main_file, 'warning');
                    }

                    // fix font pathes in variables file // todo evtl prüfung auf version wg ggf anderer @'s und pfade
                    $less_variable_file = $theme_path_new . $theme_data['less_folder'] . DIRECTORY_SEPARATOR . $theme_data['less_variable_file'];
                    $s = file_get_contents($less_variable_file);
                    $s = preg_replace(
                        ['/(@fa-font-path.*:.*[\'|"])(.*)([\'|"];)/m', '/(@icon-font-path.*:.*[\'|"])(.*)([\'|"];)/m'],
                        '$1../../$2$3',
                        $s);
                    if (file_put_contents($less_variable_file, $s) != false)
                    {
                        $this->msg(__text('TEXT_THEME_OVERRIDE_VARIABLES_FILE_CREATED'));
                    }
                    else
                    {
                        $full_success = false;
                        $this->msg(__text('TEXT_THEME_ERROR_CREATE_FILE') . ': ' . $less_variable_file, 'warning');
                    }

                    // add <override> to theme.xml
                    unset($theme_data['validation']);
                    $theme_data = ['overrides' => $theme] + $theme_data;
                    $theme_data['less_import_directories'] = implode(',', $theme_data['less_import_directories']);
                    $arr = ['xtcommercetheme' => $theme_data];
                    $this->arrayToXml($arr, $theme_path_new . 'theme.xml');

                    if($full_success) $this->msg('<b style="font-size: 1.1em; color: #000">'.__text('TEXT_THEME_REQUEST_CSS_GENERATION').'</b>', 'info');

                    // showcopy muss zZ als letztes kommen
                    $this->showCopyResult($theme, $theme_path_new, $output, $file_count);

                }
                else {
                    $this->msg(__text('TEXT_THEME_ERROR_MISSING_CONFIG'), 'warning');
                }
            }
            else {
                $this->msg(__text('TEXT_THEME_WARNING_NO_LESS_SUPPORT'), 'warning');
            }
        }
        else $this->msg(__text('TEXT_THEME_WARNING_THEME_XML_NOT_FOUND'), 'warning');
    }

    /**
     * something like copyTheme, but copies only the css/less relevant parts
     * relevant part are entries in theme.xml <less_import_directories>
     * @param $theme
     */
    public function createOverrideWithAllLess($theme)
    {
        // todo  bisher ungenutzt, um es vollständig zu machen, müssen die font-ordner aus varables.less extrahiert und kopiert werden
        $output = '';
        $file_count = 0;

        $theme_path = $this->themeDirExists($theme);
        $theme_data = $this->readThemeXml($theme);
        $newThemeFolder = $this->createNewThemeDirectory($theme_path);

        $copy_entries = [];
        if($theme_data) // theme.xml exists
        {
            $copy_entries[] = 'theme.xml';
            $copy_entries[] = 'theme.xsd';
            $copy_entries[] = 'css/stylesheets.css';

            if($theme_data['icon']) $copy_entries[] = $theme_data['icon'];

            if($theme_data['less_support']
            ){
                if($theme_data['less_folder']
                    && $theme_data['less_file']
                    && $theme_data['less_variable_file']
                    && $theme_data['css_file']
                ){
                    // add original ccs file for immediate usage of them/template
                    $copy_entries[] = $theme_data['css_file'];

                    // add main less folder
                    $copy_entries[] = $theme_data['less_folder'].DIRECTORY_SEPARATOR;

                    // add all less's import dirs
                    foreach ($theme_data['less_import_directories'] as $dir_or_file)
                    {
                        // to be able to skip files not needed for less we do allow file name in less_import_directories
                        // in generateCss() files found in less_import_directories are transformed to dirname
                        // why: eg animate.css: we dont want to copy all src's, but only the plain included css
                        $copy_entries[] = $dir_or_file;
                    }

                    $copy_entries = array_unique($copy_entries);

                    // copy all the stuff found to new theme/template
                    foreach($copy_entries as $entry)
                    {
                        $src = $theme_path.$entry;
                        if(is_dir($src))
                        {
                            $dst = $newThemeFolder.$entry;
                            if (!is_dir($dst)) {
                                mkdir($dst, 0777, true);
                            }
                            $copy_dir_return = $this->dircopy($src, $dst, true);

                            $pi_dst = pathinfo($newThemeFolder)['basename'].DIRECTORY_SEPARATOR.$entry;
                            $pi_src = pathinfo($theme_path)['basename'].$entry.'*';
                            $output .= '<br /><b>Copy '.$pi_src.' to: ' . $pi_dst . '</b><br />';
                            $output .= $copy_dir_return['output'];
                            $file_count += $copy_dir_return['count'];
                        }
                        else if (is_file($src))
                        {
                            $dst = $newThemeFolder.$entry;
                            $destpath = pathinfo($dst);

                            if (!file_exists($destpath['dirname'])) {
                                mkdir($destpath['dirname'], 0777, true);
                            }

                            $pi_dst = pathinfo($newThemeFolder)['basename'].DIRECTORY_SEPARATOR.$entry;
                            $pi_src = pathinfo($dst)['basename'];
                            $output .= '<br /><b>Copy '.$theme.DIRECTORY_SEPARATOR.$pi_src.' to: ' . $pi_dst . '</b><br />';

                            if (copy($src, $dst))
                            {
                                touch($dst, filemtime($src));
                                $output .= "OK<br />";
                                $file_count++;
                            }
                            else
                            {
                                $output .= "Error: File could not be copied!<br />";
                            }
                        }
                    }
                }
                else {
                    echo(__text('TEXT_THEME_ERROR_MISSING_CONFIG'));
                }
            }
            else {
                echo(__text('TEXT_THEME_WARNING_NO_LESS_SUPPORT'));
            }
        }
        else $this->msg(__text('TEXT_THEME_WARNING_THEME_XML_NOT_FOUND'), 'warning');

        $this->showCopyResult($theme, $newThemeFolder, $output, $file_count);
    }

    public function restore_theme($theme, $restore_file)
    {
        $theme_path = $this->themeDirExists($theme);

        $theme_settings = $this->getThemes($theme);

        $less_variable_file = $theme_path.$theme_settings[0]['less_folder'].'s/'.$theme_settings[0]['less_variable_file'];
        $less_restore_file  = $theme_path.$theme_settings[0]['less_folder'].'/'.$restore_file;

        if (file_exists($less_variable_file) && file_exists($less_restore_file)) {

            file_put_contents($less_variable_file,file_get_contents($less_restore_file));

            $this->msg(__text('TEXT_THEME_RESTORE_SUCCESS'));

        } else {
            $this->msg(__text('TEXT_THEME_RESTORE_ERROR'), 'warning');
        }
    }

    /**
     * generate css from less file
     * @param string $theme  theme name, eg 'xt_responsive'
     */
	public function generateCss($theme)
    {
		$theme_path = $this->themeDirExists($theme);
		$theme_data = $this->readThemeXml($theme);

		if(is_array($theme_data))
        {
            if($theme_data['validation']['required'] && $theme_data['validation']['valid'] == false)
            {
                $hasErrors = $this->processShowXmlValidationMsg($theme_data['validation']['messages']);
                if($hasErrors) die();
            }

            $less_file =  $theme_path.$theme_data['less_folder'].DIRECTORY_SEPARATOR.$theme_data['less_file'];
            $css_file =  $theme_data['css_file'];
            if ($less_file && $css_file)
            {
                try
                {
                    $directories = [];
                    foreach ($theme_data['less_import_directories'] as $key => $val)
                    {
                        /* xt6.2: its allowed to use file pathes in theme.xml <less_import_directories>
                         * that way we ommit copying too many files on createOverride
                         * BUT for most entries in parser.SetImportDirs are directories required
                         * (exceptions are file includes in less, eg animate.min.css would work as 'file-import-dir' directly)
                         * however, if an entry in less_import_directories is not a dir, the path to parent dir is constructed
                         * !only the parent! we dont travers up the directory tree
                         */
                        $dir = $theme_path.$val;
                        if(false && !is_dir($dir))
                        {
                            $pathinfo = pathinfo($dir);
                            $dir = $pathinfo['dirname'];
                            if(!is_dir($dir))
                            {
                                $this->msg(__text('TEXT_THEME_WARNING_INVALID_DIRECTORY') . '<br>' . $dir, 'warning');
                            }
                        }
                        $directories[$dir]='';
                    }

                    //error_log($less_file);
                    //error_log(print_r($directories, true));

                    $parser = new Less_Parser(array( 'compress'=> self::_COMPRESS_CSS ));
                    $parser->parseFile($less_file);
                    $parser->SetImportDirs($directories);
                    $css = $parser->getCss();

                    // write new css to file
                    $css_path = $theme_path . $css_file;
                    $destpath = pathinfo($css_path);
                    if (!file_exists($destpath['dirname'])) {
                        mkdir($destpath['dirname'], 0777, true);
                    }
                    file_put_contents($theme_path . $css_file, $css);

                    $this->msg(__text('TEXT_THEME_GENERATECSS_SUCCESS'));

                    if(file_exists(_SRV_WEBROOT.'plugins/xt_cleancache/classes/class.xt_cleancache.php'))
                    {
                        require_once _SRV_WEBROOT.'plugins/xt_cleancache/classes/class.xt_cleancache.php';

                        $cleanCache = new cleancache();
                        $cleanCache->cleanup('cache_css');
                        $cleanCache->cleanup('cache_javascript');
                        $cleanCache->cleanup('cache_dir_templates_c');
                    }

                } catch (Exception $e)
                {
                    $this->msg($e->getMessage(), 'error');
                }
            }
        }
		else {
            $this->msg(__text('TEXT_ERROR_THEME_DONT_EXIST').'<br>'.$theme, 'error');
		    die ();
        }
	}

    /**
     * @param $theme string theme name, eg 'xt_responsive'
     * @param bool $die  die if them does not exist
     * @return bool|string  the abs path to theme's dir, otherwise false; if $die == true will die if dir not exists
     */
    private function themeDirExists($theme, $die = true)
    {
        $param = '/[^a-zA-Z0-9_-]/';
        $theme = preg_replace($param, '', $theme);
        $theme_path = _SRV_WEBROOT . 'templates/' . $theme;
        if (!is_dir($theme_path)) {
            if($die)
            {
                $this->msg(__text('TEXT_ERROR_THEME_DONT_EXIST').'<br>'.$theme, 'error');
                die;
            }
            return false;
        }
        return $theme_path.DIRECTORY_SEPARATOR;
    }


    /**
     * @param $theme_path
     * @param string $name
     * @return string
     */
    private function createNewThemeDirectory($theme_path, $name = '')
    {
        $id = 0;
        do  {
            $id++;
            $name_insert = !empty($name) ? '_' . $name : '';
            $newThemeFolder = realpath($theme_path) . $name_insert . '_' . $id . DIRECTORY_SEPARATOR;

        } while(is_dir($newThemeFolder));

        if (!is_dir($newThemeFolder)) {
            mkdir($newThemeFolder);
            chmod($newThemeFolder, 0777);
        }

        return $newThemeFolder;
    }


    /**
     * @param $srcdir
     * @param $dstdir
     * @param bool $verbose
     * @return array :Ambigous <string, unknown> Ambigous <number, string, unknown>
     */
    private function dircopy($srcdir, $dstdir, $verbose = false)
    {
    	$num = 0;
    	if (!is_dir($dstdir))
    	{
    		mkdir($dstdir);
    		chmod($dstdir, 0777);
    	}

    	$output = '';
    	if ($curdir = opendir($srcdir))
    	{
    		while ($file = readdir($curdir))
    		{
    			if ($file != '.' && $file != '..' && $file != '.svn' && $file != '.DS_Store')
    			{
    				$srcfile = $srcdir . '/' . $file;
    				$dstfile = $dstdir . '/' . $file;

    				if (is_file($srcfile))
    				{
    					if (is_file($dstfile))
    					{
    						$ow = filemtime($srcfile) - filemtime($dstfile);
    					}
    					else
    					{
    						$ow = 1;
    					}
    					if ($ow > 0)
    					{
    						if ($verbose)
    						{
    							$output .= "  Copying " . str_replace(_SRV_WEBROOT, '', $srcfile) . "...";
    						}
    						if (copy($srcfile, $dstfile))
    						{
    							touch($dstfile, filemtime($srcfile));
    							$num++;
    							if ($verbose)
    							{
    								$output .= "OK<br />";
    							}
    						}
    						else
    						{
    							$output .= "Error: File " . str_replace(_SRV_WEBROOT, '', $srcfile) . " could not be copied!<br />";
    						}
    					}
    				}
    				elseif (is_dir($srcfile))
    				{
    					$data = $this->dircopy($srcfile, $dstfile, $verbose);
    					$num += $data['count'];
    					$output .= $data['output'];
    				}
    			}
    		}
    		closedir($curdir);
    	}

    	return array('count' => $num, 'output' => $output);
    }
}
