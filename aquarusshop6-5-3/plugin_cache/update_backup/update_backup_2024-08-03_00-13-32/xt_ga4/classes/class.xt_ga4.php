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

require_once 'class.xt_ga4_item.php';

class xt_ga4{

  protected $respect_dnd = XT_GA4_DO_NOT_TRACK;
  protected $show_global_tag = XT_GA4_GLOBALTAG;
  protected $show_gtm_tag = XT_GA4_GLOBALTAG_GTM;
  protected $measurement_id = XT_GA4_MEASUREMENT_ID;
  protected $gtm_id = XT_GA4_GTM_ID;
  protected $adwords_conversion = 'true';
  protected $adwords_conversion_id = XT_GA4_ADWORDS_CONVERSION_ID;
  protected $adwords_conversion_label = XT_GA4_ADWORDS_CONVERSION_LABEL;
  protected $adwords_conversion_shipping = XT_GA4_ADWORDS_CONVERSION_SHIPPING;
  protected $price_without_tax = XT_GA4_NET_PRICES;

  function __contstruct() {

    $this->dataLayer = [];

  }


  public function _showGlobalTag() {
    global $page;


    $js = '';

    switch ($page->page_name) {

      case 'product':
        $dataLayer = self::page_product();
      break;

      case 'cart':
        $dataLayer = self::page_cart();
      break;

      case 'checkout':
        switch ($page->page_action) {
          case 'shipping':
            $dataLayer = self::page_shipping();
          break;
          case 'payment':
            $dataLayer = self::page_payment();
          break;
          case 'confirmation':
            $dataLayer = self::page_confirmation();
          break;
          case 'success':
            $dataLayer = self::page_success();
          break;
        }

      break;

    }

    if (is_array($dataLayer)) $js = '<script> window.dataLayer = window.dataLayer || []; dataLayer.push('.json_encode($dataLayer).')</script>';

    if ($this->show_global_tag=='true') {

      $js .= '<!-- Global site tag (gtag.js) - Google Analytics -->
      <script async src="https://www.googletagmanager.com/gtag/js?id='.$this->measurement_id.'"></script>
      <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag(\'js\', new Date());
      gtag(\'config\', \''.$this->measurement_id.'\');
      </script>';

    }

    if ($this->show_gtm_tag=='true') {
        $js .='<!-- Google Tag Manager -->
        <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({\'gtm.start\':
        new Date().getTime(),event:\'gtm.js\'});var f=d.getElementsByTagName(s)[0],
        j=d.createElement(s),dl=l!=\'dataLayer\'?\'&l=\'+l:\'\';j.async=true;j.src=
        \'https://www.googletagmanager.com/gtm.js?id=\'+i+dl;f.parentNode.insertBefore(j,f);
      })(window,document,\'script\',\'dataLayer\',\''.$this->gtm_id.'\');</script>
        <!-- End Google Tag Manager -->';
    }
    echo $js;

  }

  public function _showGTMBodyTag() {
    if ($this->show_gtm_tag=='true') {
    echo '<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id='.$this->gtm_id.'"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->';

   }
  }


  /**
   * product page Datalayer
   * https://developers.google.com/tag-manager/ecommerce-ga4#measure_viewsimpressions_of_productitem_details
   * @return [type] [description]
   */
  private function page_product() {
    global $p_info,$brotkrumen;

    $data_layer = [];
    $data_layer['event'] = 'view_item';
    $data_layer['ecommerce']['items'][]=self::_getItem($p_info->data,$brotkrumen->krumen,'product');
    return $data_layer;

  }

  /**
   * purchase event, success page
   * @return [type] [description]
   */
  private function page_success() {
    global $success_order;

    if (!is_object($success_order)) {
    //  print_r($_SESSION);
    }

    if (is_object($success_order) && isset($success_order->oID)) {

          $data_layer = [];



          $data_layer['event'] = 'purchase';
          $data_layer['ecommerce']['transaction_id']=$success_order->oID;
          $data_layer['ecommerce']['affiliation']='Onlineshop';
          $data_layer['ecommerce']['value']=($this->price_without_tax == 'false' ? round($success_order->order_total['total']['plain'],2) : round($success_order->order_total['total_otax']['plain'],2));
          $tax = $success_order->order_total['total']['plain']-$success_order->order_total['total_otax']['plain'];
          if ($this->price_without_tax == 'true') $tax=0;
          $data_layer['ecommerce']['tax']=round($tax);
          $data_layer['ecommerce']['shipping']=($this->price_without_tax == 'false' ? round($success_order->order_total['data_total']['plain'],2) : round($success_order->order_total['data_total_otax']['plain'],2));
          $data_layer['ecommerce']['currency']='EUR';
          $data_layer['ecommerce']['coupon']='';



          $item_ids = array();
          $bb_items = array();
          foreach ($success_order->order_products as $key => $product) {

            $item = new xt_ga4_item();

            $item_ids[]=$product['products_id'];

            $_price = ($this->price_without_tax == 'false' ? round($product['products_price']['plain'],2) : round($product['products_price']['plain_otax'],2));

            $item->set_name($product['products_name']);
            $item->set_id($product['products_id']);
            $item->set_price($_price);
            $item->set_brand(self::_getItemManufacturer('',$product['products_id']));
            $item->set_quantity($product['products_quantity']);

            $cat = $this->_getCategoryTree($product['products_id']);
            if ($cat!==false) {
              $cat = explode(' => ',$cat);
              $item->set_category($cat[0]);
              if (isset($cat[1])) $item->set_category2($cat[1]);
              if (isset($cat[2])) $item->set_category3($cat[2]);
              if (isset($cat[3])) $item->set_category4($cat[3]);
            }

            $bb_items[]=array('sku'=>$product['products_model'],'name'=>$product['products_name'],'category'=>$cat[0],'price'=>$_price,'quantity'=>$product['products_quantity']);

            $data_layer['ecommerce']['items'][]=$item->getItemDataLayerArray();

          }


          // old BB variables
          $data_layer['transactionId'] = $success_order->oID;
          $data_layer['transactionAffiliation'] = 'Onlineshop';
          $data_layer['transactionTotal'] = $data_layer['ecommerce']['value'];
          $data_layer['transactionTax'] =   $data_layer['ecommerce']['tax'];
          $data_layer['transactionShipping'] = $data_layer['ecommerce']['shipping'];
          $data_layer['transactionProducts'] = $bb_items;
          $data_layer['google_tag_params'] = array('ecomm_pagetype'=>'purchase',
              'ecomm_prodid'=>$item_ids,
              'ecomm_totalvalue'=>$data_layer['ecommerce']['value']);

          if ($this->adwords_conversion=='true') {
              if ($this->price_without_tax=='false') {
                if ($this->adwords_conversion_shipping == 'true') {
                  $conversion_value = $success_order->order_total['total']['plain'];
                } else {
                  $conversion_value = $success_order->order_total['product_total']['plain'];
                }
              } else {
                if ($this->adwords_conversion_shipping == 'true') {
                  $conversion_value = $success_order->order_total['total_otax']['plain'];
                } else {
                  $conversion_value = $success_order->order_total['product_total_otax']['plain'];
                }
              }
              $data_layer['adwords_conversion']=$this->getAdwordsConversion($conversion_value);
          }

          return $data_layer;

    }

  }

  /**
   * adwords conversion
   * @param  [type] $conversion_value [description]
   * @return [type]                   [description]
   */
  private function getAdwordsConversion($conversion_value) {
    $conversion=[];
    $conversion['google_conversion_id']=$this->adwords_conversion_id;
    $conversion['google_conversion_label']=$this->adwords_conversion_label;
    $conversion['google_conversion_value']=round($conversion_value,2);
    return $conversion;
  }


  /**
   * add_payment_info on confirmation page
   * @return [type] [description]
   */
  private function page_confirmation() {

    if (is_array($_SESSION['cart']->show_content) && count($_SESSION['cart']->show_content)>0) {

      $data_layer = [];
      $data_layer['event'] = 'add_payment_info';
      foreach ($_SESSION['cart']->show_content as $key => $product) {

            $item = new xt_ga4_item();

            $item->set_name($product['products_name']);
            $item->set_id($product['products_id']);
            $item->set_price(($this->price_without_tax == 'false' ? round($product['products_price']['plain'],2) : round($product['products_price']['plain_otax'],2)));
            $item->set_brand(self::_getItemManufacturer('',$product['products_id']));
            $item->set_quantity($product['products_quantity']);

            $cat = $this->_getCategoryTree($product['products_id']);
            if ($cat!==false) {
              $cat = explode(' => ',$cat);
              $item->set_category($cat[0]);
              if (isset($cat[1])) $item->set_category2($cat[1]);
              if (isset($cat[2])) $item->set_category3($cat[2]);
              if (isset($cat[3])) $item->set_category4($cat[3]);
            }

            $data_layer['ecommerce']['items'][]=$item->getItemDataLayerArray();
      }

      $data_layer['ecommerce']['payment_type']=$_SESSION['selected_payment'];
      return $data_layer;

    }
  }

  /**
   * add_shipping_info on payment page
   * @return [type] [description]
   */
  private function page_payment() {

    if (is_array($_SESSION['cart']->show_content) && count($_SESSION['cart']->show_content)>0) {

      $data_layer = [];
      $data_layer['event'] = 'add_shipping_info';
      foreach ($_SESSION['cart']->show_content as $key => $product) {

            $item = new xt_ga4_item();

            $item->set_name($product['products_name']);
            $item->set_id($product['products_id']);
            $item->set_price(($this->price_without_tax == 'false' ? round($product['products_price']['plain'],2) : round($product['products_price']['plain_otax'],2)));
            $item->set_brand(self::_getItemManufacturer($product['manufacturers_id']));
            $item->set_quantity($product['products_quantity']);

            $cat = $this->_getCategoryTree($product['products_id']);
            if ($cat!==false) {
              $cat = explode(' => ',$cat);
              $item->set_category($cat[0]);
              if (isset($cat[1])) $item->set_category2($cat[1]);
              if (isset($cat[2])) $item->set_category3($cat[2]);
              if (isset($cat[3])) $item->set_category4($cat[3]);
            }

            $data_layer['ecommerce']['items'][]=$item->getItemDataLayerArray();
      }

      $data_layer['ecommerce']['shipping_tier']=$_SESSION['selected_shipping'];
      return $data_layer;

    }
  }

  /**
   * begin_checkout on shipping page
   * @return [type] [description]
   */
  private function page_shipping() {

    if (is_array($_SESSION['cart']->show_content) && count($_SESSION['cart']->show_content)>0) {

      $data_layer = [];
      $data_layer['event'] = 'begin_checkout';
      foreach ($_SESSION['cart']->show_content as $key => $product) {

            $item = new xt_ga4_item();

            $item->set_name($product['products_name']);
            $item->set_id($product['products_id']);
            $item->set_price(($this->price_without_tax == 'false' ? round($product['products_price']['plain'],2) : round($product['products_price']['plain_otax'],2)));
            $item->set_brand(self::_getItemManufacturer($product['manufacturers_id']));
            $item->set_quantity($product['products_quantity']);

            $cat = $this->_getCategoryTree($product['products_id']);
            if ($cat!==false) {
              $cat = explode(' => ',$cat);
              $item->set_category($cat[0]);
              if (isset($cat[1])) $item->set_category2($cat[1]);
              if (isset($cat[2])) $item->set_category3($cat[2]);
              if (isset($cat[3])) $item->set_category4($cat[3]);
            }

            $data_layer['ecommerce']['items'][]=$item->getItemDataLayerArray();
      }

      return $data_layer;

    }
  }

  /**
   * view_cart on cart page
   * @return [type] [description]
   */
  private function page_cart() {

    if (is_array($_SESSION['cart']->show_content) && count($_SESSION['cart']->show_content)>0) {

      $data_layer = [];
      $data_layer['event'] = 'view_cart';
      foreach ($_SESSION['cart']->show_content as $key => $product) {

            $item = new xt_ga4_item();

            $item->set_name($product['products_name']);
            $item->set_id($product['products_id']);
            $item->set_price(($this->price_without_tax == 'false' ? round($product['products_price']['plain'],2) : round($product['products_price']['plain_otax'],2)));
            $item->set_brand(self::_getItemManufacturer($product['manufacturers_id']));
            $item->set_quantity($product['products_quantity']);

            $cat = $this->_getCategoryTree($product['products_id']);
            if ($cat!==false) {
              $cat = explode(' => ',$cat);
              $item->set_category($cat[0]);
              if (isset($cat[1])) $item->set_category2($cat[1]);
              if (isset($cat[2])) $item->set_category3($cat[2]);
              if (isset($cat[3])) $item->set_category4($cat[3]);
            }

            $data_layer['ecommerce']['items'][]=$item->getItemDataLayerArray();
      }

      return $data_layer;

    }


  }

  private function _getCategoryTree($products_id) {
    global $db;

    $rs = $db->Execute(
        "SELECT categories_id FROM " . TABLE_PRODUCTS_TO_CATEGORIES . " WHERE master_link=1 and products_id=? and store_id = ?",
        array($products_id,'1')
    );

    if ($rs->RecordCount() >= 1) {
      $tree = $this->buildCAT($rs->fields['categories_id']);
      $tree = substr($tree, 0, -4);
      return $tree;
    }
    return false;



  }

  /**
   * get Category tree
   *
   * @param mixed $catID
   * @return mixed
   */
  function buildCAT ($catID)
  {
      if (isset($this->CAT[$catID])) {
          return $this->CAT[$catID];
      } else {
          $cat = array();
          $tmpID = $catID;

          while ($this->_getParent($catID) != 0 || $catID != 0) {
              $cat[] = $this->getCategory($catID);
              $catID = $this->_getParent($catID);
          }

          $catStr = '';

          for ($i = count($cat); $i > 0; $i--) {
              $catStr .= $cat[$i - 1] . ' => ';
          }

          $this->CAT[$tmpID] = $catStr;

          return $this->CAT[$tmpID];
      }
  }

  function _getParent ($catID)
  {
      global $db, $xtPlugin;


      if (isset($this->PARENT[$catID])) {
          return $this->PARENT[$catID];
      } else {
          $rs = $db->Execute("SELECT parent_id FROM " . TABLE_CATEGORIES . " WHERE categories_id=?", array($catID));
          $this->PARENT[$catID] = $rs->fields['parent_id'];

          return $rs->fields['parent_id'];
      }
  }

  function getCategory ($catID)
  {
      global $db, $xtPlugin,$language;


      if (isset($this->_CAT[$catID])) return $this->_CAT[$catID];

      $rs = $db->Execute(
          "SELECT categories_name FROM " . TABLE_CATEGORIES_DESCRIPTION . " WHERE categories_id=? and language_code=? AND categories_store_id=?",
          array($catID, $language->code, '1')
      );

      if ($rs->RecordCount() == 1) {
          $this->_CAT[$catID] = $rs->fields['categories_name'];


          return $this->_CAT[$catID];
      }
  }




  /**
   * format item array for product data object array
   * @param  [type] $pData         [description]
   * @param  array  $category_path [description]
   * @return [type]                [description]
   */
  private function _getItem($pData,$category_path = array(),$page='') {


    $item = new xt_ga4_item();
    $item->set_name($pData['products_name']);
    $item->set_id($pData['products_id']);

    $price = ($this->price_with_tax == true ? round($pData['products_price']['plain'],2) : round($pData['products_price']['plain_otax'],2));

    $item->set_price($price);
    $item->set_brand(self::_getItemManufacturer($pData['manufacturers_id']));
    $item->set_quantity(1);

    array_pop($category_path);


    if (count($category_path)<=1) {

      $item->set_category($category_path[1]['name']);
    } else {
      unset ($category_path[0]);
      $item->set_category($category_path[1]['name']);
      if (isset($category_path[2])) $item->set_category2($category_path[2]['name']);
      if (isset($category_path[3])) $item->set_category3($category_path[3]['name']);
      if (isset($category_path[4])) $item->set_category4($category_path[4]['name']);

    }

    return $item->getItemDataLayerArray();

  }

  /**
   * get manufacturers name
   * @param  [type] $manufacturers_id [description]
   * @return [type]                   [description]
   */
  private function _getItemManufacturer($manufacturers_id='',$products_id='') {
    global $db;

    if ($manufacturers_id=='') {
      return $db->GetOne("SELECT m.manufacturers_name FROM ".TABLE_MANUFACTURERS." m, ".TABLE_PRODUCTS." p WHERE p.products_id=? AND p.manufacturers_id=m.manufacturers_id",array($products_id));
    }

    return $db->GetOne("SELECT manufacturers_name FROM ".TABLE_MANUFACTURERS." WHERE manufacturers_id=?",array($manufacturers_id));
  }



}
