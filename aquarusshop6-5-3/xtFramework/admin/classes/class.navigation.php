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


class class_dbNav extends ADOdb_Active_Record
{
	var $_table = TABLE_ADMIN_NAVIGATION;
}

class navigation {
	var $default_icon, $default_cls = array();
	function __construct() {
		$this->default_icon = array('N' => 'images/icons/folder.png', 'W' => 'images/icons/folder.png');
		$this->default_cls = array('N' => 'x-btn-text-icon', 'W' => '');
	}



	function getSysStatusNavData($position) {
		global $system_status;

		foreach ($system_status->values as $key => $val) {
			$db_status_array[$key] = $key;
		}

		$status_array = array_merge($db_status_array, $system_status->_defaultValues());

		$erg = array();
		foreach ($status_array as $key => $arr) {

			$leaf='1';
			$type='I';
			$icon ='images/icons/cog_edit.png';
			switch($key)
            {
                case 'stock_rule':
                    $url_h = 'https://xtcommerce.atlassian.net/wiki/spaces/MANUAL/pages/917642';
                    break;
                case 'shipping_status':
                    $url_h = 'https://xtcommerce.atlassian.net/wiki/spaces/MANUAL/pages/917605';
                    break;
                case 'base_price':
                    $url_h = 'https://xtcommerce.atlassian.net/wiki/spaces/MANUAL/pages/917591';
                    break;
                case 'order_status':
                    $url_h = 'https://xtcommerce.atlassian.net/wiki/spaces/MANUAL/pages/917592';
                    break;
                case 'zone':
                    $url_h = 'https://xtcommerce.atlassian.net/wiki/spaces/MANUAL/pages/917594';
                    break;
                case 'campaign':
                    $url_h = 'https://xtcommerce.atlassian.net/wiki/spaces/MANUAL/pages/917601';
                    break;
                default:
                    $url_h = false;
            }

			$url_d = 'adminHandler.php?load_section='.$arr;
			$arrTMP = Array('text' => (__define('TEXT_'.strtoupper($arr)))
			,'url_i' => ''
			,'url_d' => $url_d
            ,'url_h' => $url_h
			,'tabtext' => (__define('TEXT_'.strtoupper($arr)))
			,'id' => 'system_status_'.$arr
			,'type'=>$type
			,'leaf'=>$leaf
			,'icon'=>$icon
			);
			$erg[]=$arrTMP;
		}
		return $erg;

	}
	
	
	function getCatNavData($parent=0,$post_store='',$unassigned=false) {
		global $store_handler;
		
		$st ='';
		
		if (strstr($parent,'category_store')) {
			$tmp = str_replace('category_store',"",$parent);
			$st = (int)$tmp;
			$parent = 0;
		}
			
		if (strstr($parent,'subcat_')) {
			$tmp = explode('_',$parent);
			$parent = (int)$tmp[1];
			
		}
		if ($post_store!='') $st=$post_store;
		$categories = new category();
		$categories->_setAdmin();
		$list = $categories->getCategoryListingAdmin($parent,$st,$unassigned );
		
		if (is_array($list)) {
			foreach ($list as $key => $category)  {
				$subcat=$categories->category_has_subcategories($category['categories_id'],$st);

				$leaf = '';
				$type='G';
				$icon='images/icons/fa-folder-open.png';
				$textCls = '';
				if ($category['categories_status']==0) {
				    $icon ='images/icons/folder_delete.png';
                    $textCls = 'category-disabled';
                }
				if (!$subcat) {
					$leaf='1';
					$type='I';
					$icon ='images/icons/folder_add.png';
					if ($category['category_custom_link']==1) $icon ='images/icons/link.png';
					if ($category['categories_status']==0) $icon ='images/icons/folder_delete.png';
				}
				if ($category['category_custom_link']==1) $url_d ='';
				else $url_d = 'adminHandler.php?load_section=product&pg=overview&catID='.$category['categories_id'];

                $catName = $category['categories_name'];
				$arrTMP = Array('text' => $catName
				,'url_i' => ''
				,'url_d' => $url_d
				,'tabtext' => (__define('TEXT_PRODUCTS_IN_CATEGORY').' '.$category['categories_name'])
				,'id' => 'subcat_'.$category['categories_id'].'_catst_'.$st
				,'type'=>$type
				,'leaf'=>$leaf
				,'icon'=>$icon
                ,'textCls'=> $textCls
				);
				$erg[]=$arrTMP;
			}
			
		}
			
		return $erg;
	}

	function getMediaNavData($parent=0) {

		if ($parent=='MediaGallery') {
			$parent = 0;
		}
		if (strstr($parent,'media_subcat_')) {
			$tmp = explode('_',$parent);
			$parent = (int)$tmp[2];
		}
		require_once(_SRV_WEBROOT._SRV_WEB_FRAMEWORK.'classes/class.MediaGallery.php');

		$mg = new MediaGallery();
		$mg->_setAdmin();
		$list = $mg->getCategoryListing($parent);

		$erg = array();
		if (is_array($list)) {
			foreach ($list as $key => $media_category)  {
				$subcat=$mg->category_has_subcategories($media_category['mg_id']);
				$leaf = '';
				$type='G';
				$icon ='images/icons/folder_image.png';
				
				if (!$subcat) {
					$leaf='1';
					$type='I';
				}
				
				$galType = '&galType='.$media_category['class'];
				
				if(preg_match('/files/', $media_category['class'])){
					$icon ='images/icons/folder_brick.png';
				}
				
				$url_d = 'adminHandler.php?load_section=MediaList&pg=overview&mgID='.$media_category['mg_id'].$galType;
				$arrTMP = Array('text' => $media_category['name']
				,'url_i' => ''
				,'url_d' => $url_d
				,'tabtext' => (__define('TEXT_MEDIA_IN_CATEGORY').' '.$media_category['name'])
				,'id' => 'media_subcat_'.$media_category['mg_id']
				,'type'=>$type
				,'leaf'=>$leaf
				,'icon'=>$icon
				);
				$erg[]=$arrTMP;
			}

		}
		return $erg;
	}
	

	function getNavData($position, $parent = 0, $handler = '') {
		global $xtc_acl, $xtPlugin,$store_handler, $db;

		$imageRoot = '';
		$erg = array();
		if (($parent < 0) || ($parent == '')) $parent = 0;
		$dbNav = new class_dbNav();

		$param ='/[^a-zA-Z0-9_-]/';
		$parent=preg_replace($param,'',$parent);

        $add_to_sql = "";
		$arrRes = $dbNav->find("navtype = '".$position."' AND parent = '".$parent."' ".$add_to_sql." order by sortorder");

		/** @var ADODB_Active_Record $item */
		$item = null;
		foreach($arrRes as $item)
		{
            if(!isset($item->pid))
            {
                $fields = $db->GetArray("SELECT COLUMN_NAME FROM information_schema.COLUMNS WHERE TABLE_SCHEMA='" . _SYSTEM_DATABASE_DATABASE . "' AND TABLE_NAME=?", [ TABLE_ADMIN_NAVIGATION ]);
                foreach($fields as $field)
                {
                    $field = isset($field['column_name']) ? $field['column_name'] : $field['COLUMN_NAME'];
                    $to_upper_field = strtoupper($field);
                    $item->$field =  $item->$to_upper_field;
                    unset($item->$to_upper_field);
                }
            }

		    $area = $item->text;
			$check_read = $xtc_acl->checkPermission($area, 'read');
			if(!$check_read) continue;

			// skip unassigned categories on single store license
            if ($area=='unasigned_cats' && $store_handler->store_count==1) continue;

			// check if its an disabled plugin
            // since xt5.1 plugins should set their plugin_code in acl_nav
            $plugin_code = $item->plugin_code;
            if(empty($plugin_code))
            {
                // try to read it from url_i. ther should be something like plugin=xyz
                $url_parts = array();
                parse_str($item->url_i, $url_parts);
                $plugin_code = isset($url_parts['plugin']) ? $url_parts['plugin'] : "";
            }
			if(!empty($plugin_code))
			{
                $check_read = $xtPlugin->_getPluginStatus($plugin_code);
                if(!$check_read) continue;
			}

				$arrTMP = Array('text' => (__define('TEXT_'.strtoupper($item->text)))
				,'url_i' => $item->url_i
				,'url_d' => $item->url_d == 'adminHandler.php' ? $item->url_d.'?load_section='.$item->text : $item->url_d
                ,'url_h' => !empty(trim($item->url_h) && strpos($item->url_h,'xtcommerce.atlassian.net')) ? trim($item->url_h) : false
				,'tabtext' => (__define('TABTEXT_'.strtoupper($item->text)))
				//,'pid' => $item->pid
				,'draggable'=>'false'
				,'itemId' => $position . 'menuitem' . $item->pid
				,'pid' => $item->text
				);

				if ($item->icon != '') {
					$arrTMP['icon'] = $item->icon;
				} elseif ($this->default_icon[$position]) {
					$arrTMP['icon'] = $this->default_icon[$position];
				}

				if ($item->iconCls != '') {
					$arrTMP['iconCls'] = $item->iconCls;
				}
				if ($item->cls) {
					$arrTMP['cls'] = $item->cls;
				} elseif ($this->default_cls[$position]) {
					$arrTMP['cls'] = $this->default_cls[$position];
				}

				$tmp_pos = strpos($arrTMP['url_d'], 'adminHandler.php');

				if ($tmp_pos===false){}
				else{
					$arrTMP['url_d'] .= $arrTMP['url_i'];
					unset($arrTMP['url_i']);
				}

				switch ($position) {
					// only for position 'N'
					case "N":

						if ($parent == 0) {
							$arrTMP['xtype'] = 'tbbutton';
						}
						if ($item->type == 'G') {
							$arrTMP['menu'] = $this->getNavData($position, $item->pid, $handler);
						}
						if ($item->type == 'I') {
							if ($item->handler == '*') {
								// handler from parent item
								$arrTMP['handler'] = $handler;
							} else if ($item->handler != ''){
								// handler from current item
								$arrTMP['handler'] = $item->handler;
							}
						}
						break;
						// only for position 'N' end
						// only for position 'W'
					case "W":
						$arrTMP['type'] = $item->type;
						//$arrTMP['id'] = 'node_'.$item->pid;
						$arrTMP['id'] = 'node_'.$item->text;


						$arrTMP['leaf'] = ($item->type == 'G') ? false : true;
						if ($item->text=='category') $arrTMP['leaf'] = false;


						if ($parent == 0) {
							$arrTMP['title'] = $item->text;
						}
						break;
						// only for position 'W' end
				}

            // check if we have to change the type from G to I and remove the +
            // some sub menus are not read from acl_nav but created dynamically, ie categories / media galleries
            // we check only 'known' nodes
            $check_nodes = array('order');
            if(empty($plugin_code) && $item->type == 'G' && in_array($item->text, $check_nodes))
            {
                $subs = $this->getNavData($position, $item->text, $handler);
                if (count($subs) == 0)
                {
                    $arrTMP['leaf'] = true;
                }
            }

            $erg[] = $arrTMP;

		}
		return $erg;
	}

	function readNorthNavLevel($handler, $parent = 0) {
		return $this->getNavData('N', $parent, $handler);
	}


	function readWestNavLevel($parent = 0) {
		return $this->getNavData('W', $parent);
	}

	// recursive function
	function encodeMenuArray($a, $usenativejsonencode = false) {
		if($usenativejsonencode) return json_encode($a);
	    global $filter;
		$erg = '[';
		$trenner1 = '';
		if (is_array($a))
		foreach($a as $item) {
			$erg .= $trenner1.'{';
			$trenner2 = '';
			if (is_array($item))
			foreach($item as $key2 => $value2) {
				if ($key2 == 'handler') {
					$erg .= $trenner2."$key2 : $value2";
				} else {
					if (is_array($value2)) {
						$erg .= $trenner2."$key2 : ".$this->encodeMenuArray($value2);
					} else {
        			    $value2 =$filter->_filter($value2);
						$erg .= $trenner2."$key2 : '$value2'";
					}
				}
				$trenner2 = ', ';
			}
			$erg .= '}';
			$trenner1 = ', ';
		}
		$erg .= ']';
		return $erg;
	}
}

?>