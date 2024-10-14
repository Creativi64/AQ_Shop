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
  
class xt_canonical{
 		private $page_name; 
 		
 		/**
 		 * get Canonical Tag 
 		 * @param $page_section string
 		 * @return string
 		 * 
 		 * */
 		public function _getCanonicalUrl($page_section)
 		{
 			global $xtLink,$language,$xtPlugin;

            ($plugin_code = $xtPlugin->PluginCode('class.canonical.php:getCanonicalProductUrl')) ? eval($plugin_code) : false;
            if(isset($plugin_return_value)) return $plugin_return_value;

 			$this->page_name = $page_section;
 			switch($this->page_name)
 			{
 				case 'product':
                case 'reviews':
 					return $this->_getProductUrl();
 					break;
 				case 'content':
 					return $this->_getContentUrl();
 					break;	
 				case 'manufacturers':
 					return $this->_getManufacturersUrl();
 					break;
 				case 'categorie':
 					return $this->_getCategorieUrl();
 					break;
 				case '404':
 					return $this->_get404Url();
 					break;
 				case 'index':
 					return $this->_getIndexUrl();
 					break;
 				default:
                     ($plugin_code = $xtPlugin->PluginCode('class.canonical.php:getCanonicalUrl')) ? eval($plugin_code) : false;
                     if(isset($plugin_return_value)){
                         return $plugin_return_value;
                     }else{
						$link_url = $xtLink->_link(array('page' => $page_section),'',true);
                        return '<link rel="canonical" href="'.$link_url.'" />';
                     }
                     break;
 			}
 		}
 		/**
 		 * get Canonical Tag for Product
 		 * 
 		 * @return string
 		 * 
 		 * */
        public function _getProductUrl() {
            global $p_info, $xtLink, $db, $store_handler, $language;
            
            // check if product is slave product and either master setting is true or product setting is true
            if ($p_info->data['products_master_model']!='') {

                    $sql = "SELECT p.products_id, p.products_canonical_master, pd.products_name, seo.url_text FROM ".TABLE_PRODUCTS . " p ".
                        " INNER JOIN " . TABLE_PRODUCTS_DESCRIPTION . " pd USING(products_id)" .
                        " INNER JOIN " . TABLE_SEO_URL . " seo ON (p.products_id=seo.link_id and seo.link_type=1 AND seo.store_id=? AND pd.language_code=seo.language_code)" .
                        " WHERE p.products_model=? AND
                		pd.language_code=? AND 
                		pd.products_store_id=?
                		LIMIT 0,1";
                    $arr = array($store_handler->shop_id,$p_info->data['products_master_model'],$language->code,$store_handler->shop_id);

                $rs = $db->GetArray($sql,$arr);
                if (count($rs)!=1) return false;

                if (XT_CANONICAL_APPLY_TO_ALL_SLAVES==1 or $rs[0]['products_canonical_master']=='1') {
                    // count how many slaves are active
                    $slave_count = $db->GetOne("SELECT count(products_id) FROM xt_products WHERE products_master_model = ? and products_status=1",array($p_info->data['products_master_model']));

                    if ($slave_count==1) {
                        // show slave as canonical
                        $link_array = array('page'=> 'product', 'type'=>'product', 'name'=>$p_info->data['products_name'], 'id'=>$p_info->data['products_id'],'seo_url'=>$p_info->data['url_text']);
                        $link_url = $xtLink->_link($link_array,'',true);
                        return '<link rel="canonical" href="'.$link_url.'" />';
                    } else {
                        $link_array = array('page'=> 'product', 'type'=>'product', 'name'=>$rs[0]['products_name'], 'id'=>$rs[0]['products_id'],'seo_url'=>$rs[0]['url_text']);
                        $link_url = $xtLink->_link($link_array,'',true);
                        return '<link rel="canonical" href="'.$link_url.'" />';
                    }

                }
            }

            $link_array = array('page'=> 'product', 'type'=>'product', 'name'=>$p_info->data['products_name'], 'id'=>$p_info->data['products_id'],'seo_url'=>$p_info->data['url_text']);
            $link_url = $xtLink->_link($link_array,'',true);
            return '<link rel="canonical" href="'.$link_url.'" />';
            
        }
        /**
 		 * get Canonical Tag for Content
 		 * 
 		 * @return string
 		 * 
 		 * */
        public function _getContentUrl() {
            global $shop_content_data,$xtLink; 
            if(_SYSTEM_MOD_REWRITE == 'true' && $shop_content_data['url_text']!=''){
           	 	$link_array = array('page'=>'content', 'seo_url' => $shop_content_data['url_text']);
            }
            else {
            	$link_array = array('page'=>'content', 'params'=>'coID='.$shop_content_data['content_id'],'seo_url' => $shop_content_data['url_text']);
            }
            $link_url = $xtLink->_link($link_array,'',true);
            return '<link rel="canonical" href="'.$link_url.'" />';
        }
   		/**
 		 * get Canonical Tag for Manufacturer
 		 * 
 		 * @return string
 		 * 
 		 * */
 		public function _getManufacturersUrl() {
            global $manufacturer,$current_manufacturer_id,$xtLink; 
            $man = array('manufacturers_id' => $current_manufacturer_id);
			$man_data = $manufacturer->buildData($man);
            if(_SYSTEM_MOD_REWRITE == 'true'){
				$link_array = array('page'=>'manufacturers', '','seo_url' => $man_data['url_text']);
            }
            else{
            	$link_array = array('page'=>'manufacturers', 'params'=>'mnf='.$current_manufacturer_id,'seo_url' => $man_data['url_text']);
            }
            $link_url = $xtLink->_link($link_array,'',true);
            return '<link rel="canonical" href="'.$link_url.'" />';
        }
        /**
 		 * get Canonical Tag for Categorie
 		 * 
 		 * @return string
 		 * 
 		 * */
 		public function _getCategorieUrl() {
            global $category,$current_category_id,$xtLink;
            if(_SYSTEM_MOD_REWRITE == 'true'){
            	$link_array = array('page'=>'categorie', '','seo_url' => $category->current_category_data['url_text']);
            }
            else {
            	$link_array = array('page'=>'categorie', 'params'=>'cat='.$current_category_id,'seo_url' => $category->current_category_data['url_text']);
            }
            $link_url = $xtLink->_link($link_array,'',true);
            return '<link rel="canonical" href="'.$link_url.'" />';
        }
        
        /**
         * get Canonical Tag for 404
         *
         * @return string
         *
         * */
        public function _get404Url() {
        	global $xtLink,$language;
        	return '';//'<link rel="canonical" href="'.$xtLink->_link(array('page'=>'404','seo_url'=>strtolower($language->code).'/404',true),'',true).'" />';
        }
        /**
         * get Canonical Tag for 404
         *
         * @return string
         *
         * */
        public function _getIndexUrl() {
        	global $xtLink,$language,$page;
            if($language->code == $language->default_language)
                return '<link rel="canonical" href="'.$xtLink->_link(array('page'=>$page->page_name,'paction'=>$page->page_action,'',true),'',true).'" />';
            else
                return '<link rel="canonical" href="'.$xtLink->_link(array('page'=>$page->page_name,'paction'=>$page->page_action, 'seo_url'=>strtolower($language->code).'/index',true),'',true).'" />';
        }
        
        /**
         * get Canonical Tag for 404
         *
         * @return string
         *
         * */
        public function _getOtherUrl() {
        	global $xtLink,$language,$page;
        	return '<link rel="canonical" href="'.$xtLink->_link(array('page'=>$page->page_name,'paction'=>$page->page_action,true),'',true).'" />';
        }

  }
