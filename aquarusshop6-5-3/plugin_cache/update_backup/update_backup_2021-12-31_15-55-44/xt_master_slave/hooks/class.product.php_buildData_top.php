<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

global $page;
if (isset($xtPlugin->active_modules['xt_master_slave']))
{
    include_once _SRV_WEBROOT . _SRV_WEB_PLUGINS . 'xt_master_slave/classes/class.xt_master_slave_products.php';

    global $msp, $page, $p_info, $current_product_id, $db, $ms_allow_add_cart;
    global $ms_cart_refresh;

    $ms_allow_add_cart = false;//$ms_cart_refresh;
    if (
        /**  never in categories */ //  $page->page_name != 'categorie' &&
        /**  never in cart       */ //
        $page->page_name != 'cart' &&
        ($this->data['products_master_flag'] || $this->data['products_master_model'])
        && (!isset($msp) || (isset($msp) && !$msp->flag_processingProductList))
        && ($page->page_name == 'product' || ($this->data['products_master_flag'] && $this->data['products_option_master_price']!='mp' && $this->data['products_option_master_price']!='np')
        )
    )
    {


        static $openFirstSlave = false;


        $masterModel = $this->data['products_master_flag'] ? $this->data['products_model'] : $this->data['products_master_model'];
        $m_data = false;
        $m_data_id = 0;
        $openFirstSlaveSetting = false;
        if($masterModel)
        {
            $m_data = xt_master_slave_functions::getMasterData($masterModel);
            $m_data_id = $m_data["products_id"];
            $openFirstSlaveSetting = xt_master_slave_functions::getOverrideSetting(XT_MASTER_SLAVE_OPEN_SLAVE, 'ms_open_first_slave', $this);
        }

        $openFirstSlave = $p_info == null
            && USER_POSITION == 'store'
            && $page->page_name === 'product'   // only when on products page
            && !$ms_cart_refresh
            && $openFirstSlaveSetting
            && empty($_POST['ms_attribute_id']) // only on first call to product
            && $_SESSION['ms_slave_redirect'] != $m_data_id // if not already redirecting
            && !$_SESSION['ms_slave_open_master']
            && (!isset($_POST['action']) && $_POST['action'] != "add_product")
            && empty($this->data['products_master_model'])
            && (!isset($msp) || !$msp->flag_processingProductList)
            // do not redirect when in creating products for options listing
            && empty((int)$_GET['dl_media'])
            // do not redirect when serving download

        ;

        if (!isset($msp))
        {
            $msp = new master_slave_products();
        }
        $msp->setProductID($current_product_id ? $current_product_id : $this->pID, $masterModel);

        $msp->clickedOption = $_POST['clicked_option'];

        $optionsOrder = $msp->getOptionsOrder($masterModel);

        $filter_count = is_array($_SESSION['select_ms'][$current_product_id]) ? count($_SESSION['select_ms'][$current_product_id]) : 0;

        /*
        if(!empty($_POST['clicked_option']) && !empty($optionsOrder[0]) && $optionsOrder[0] == $_POST['clicked_option'] && $page->page_name=='product' && count ($msp->possibleProducts) && !$ms_cart_refresh)
        TODO   OOR-990-59906
        bei browser-zurück verschwand mit diesem fix der cart button
          */
        if($_SERVER['REQUEST_METHOD'] != 'POST' && $this->pID == $current_product_id && !empty($_POST['clicked_option']) && $optionsOrder[0] == $_POST['clicked_option'] && $page->page_name=='product' && is_array($msp->possibleProducts) && count($msp->possibleProducts) && !$ms_cart_refresh)
        {
            global $xtLink;

            $sql = "SELECT products_id FROM ".TABLE_PRODUCTS." WHERE products_model = ?";
            $master_id = $db->GetOne($sql, $masterModel);
            $this->pID = $master_id;

            $this->sql_products = new getProductSQL_query();

            $this->sql_products->setPosition('product_info');

            $master_data = $this->getProductData('default', 'de');

            $link_array = array('page' => 'product', 'type' => 'product', 'name' => $master_data['products_name'], 'id' => $master_data['products_id'], 'seo_url' => $master_data['url_text']);
            $link = $xtLink->_link($link_array);

            $msp->unsetFilter();
            $_SESSION['select_ms'][$master_id]['id'][$_POST['clicked_option']] =  $_POST['ms_attribute_id'][$_POST['clicked_option']];

            $_SESSION['ms_slave_open_master'] = true;
            //error_log('redir 2');
            $xtLink->_redirect($link);
        }

        if($_POST['ms_attribute_id'])
        {
            $reset_arr = array();
            $reset_the_rest = false;
            foreach($optionsOrder as $k => $oo)
            {
                if($oo == $_POST['clicked_option'])
                {
                    $reset_the_rest = true;
                    continue;
                }
                if($reset_the_rest)
                {
                    $reset_arr[] = $oo;
                }
            }

            if (count($reset_arr))
            {
                foreach ($_POST['ms_attribute_id'] as $k => $attr)
                {
                    if (in_array($k, $reset_arr))
                    {
                        //unset($_POST['ms_attribute_id'][$k]);
                    }
                }
            }
            $msp->unsetFilter();
            $msp->setFilter($_POST['ms_attribute_id']);
        }
        else if($_SESSION['ms_slave_open_master'])
        {
            unset($_SESSION['ms_slave_open_master']);
        }
        else if($_SESSION['ms_slave_open_master'] != true && $p_info==null)
        {
            $msp->unsetFilter();
            $fad = $msp->getFullAttributesData($this->pID);
            if(is_array($fad) && count($optionsOrder)>1)
            {
                foreach ($fad as $ad)
                {
                    if ($optionsOrder[0] == $ad['option_id'])
                    {
                        $msp->setFilter(array($ad['option_id'] => $ad['option_value_id']));
                    }
                }
            }
        }


        if($p_info == null) {
            $msp->getMasterSlave();
        }

        if (
            ($msp->possibleProducts == null || ( is_array($msp->possibleProducts) && count($msp->possibleProducts) == 0))
            &&
            $_POST['ms_attribute_id']
        )
        {
            foreach ($_POST['ms_attribute_id'] as $valueId)
            {
                $valueData = $msp->getAttributesValueData($valueId);
                $str[] = $valueData['attributes_name'];
            }
            $msg = implode(' / ', $str) . ' '.TEXT_XT_MASTER_SLAVE_NO_STOCK;

            $msp->unsetFilter();

            foreach($msp->possibleProducts_primary as $ppp)
            {
                if ($ppp == $current_product_id) continue;
                $attrData = $msp->getAttributesData($ppp);
                $ad_found = false;
                foreach($attrData as $ad)
                {
                    if($ad['attributes_parent_id'] == $_POST['clicked_option']
                        && $ad['attributes_id'] == $_POST['ms_attribute_id'][$_POST['clicked_option']])
                    {
                        $ad_found = $attrData;
                        break;
                    }
                }
                if($ad_found)
                {
                    $filter = array();
                    foreach($ad_found as $ad)
                    {
                        $filter[$ad['attributes_parent_id']] = $ad['attributes_id'];
                    }

                    $str = array();
                    foreach ($filter as $valueId)
                    {
                        $valueData = $msp->getAttributesValueData($valueId);
                        $str[] = $valueData['attributes_name'];
                    }
                    $msg .='<p class="ms_variant_instead">'.implode(' / ', $str).' '. TEXT_XT_MASTER_SLAVE_NO_STOCK_SELECTED_INSTEAD .'</p>';

                    $msp->setFilter($filter);
                    $msp->getMasterSlave();
                    $openFirstSlave = true;

                    break;
                }
            }

            $_SESSION['master_slave_error'] = $msg;
        }

        if(is_array($msp->possibleProducts))
        {
            reset($msp->possibleProducts);
            $first_key = key($msp->possibleProducts);
        }

        if ($openFirstSlave)
        {
            global $xtLink;

            $first_data = $msp->possibleProducts[$first_key];

            $link_array = array('page' => 'product', 'type' => 'product', 'name' => $first_data['products_name'], 'params' => 'info='.$first_data['products_id'], 'seo_url' => $first_data['url_text']);
            $link = $xtLink->_link($link_array);
            $_SESSION['ms_slave_redirect'] = $m_data_id;
            //error_log('redir 2');
            $xtLink->_redirect($link);
        }

        $redirect = (!isset($p_info)) && $page->page_name === 'product'
            && !$ms_cart_refresh
            && count($msp->possibleProducts) == 1
            //&& count($msp->possibleProducts_primary) != 1
            && $_SESSION['ms_slave_redirect'] != $m_data_id
            && $_SESSION['ms_slave_open_master'] != true
            && (!isset($_POST['action']) || $_POST['action'] != "add_product")
            && (empty($this->data['products_master_model']) || count($msp->allProduct_ids)>1) // nicht umleiten, wenn nur ein slave vorhanden (unnötiger 302 bei direktaufruf)
        ;

        unset($_SESSION['ms_slave_redirect']);
        unset($_SESSION['ms_slave_open_master']);

        global $ms_allow_add_cart;
        $ms_allow_add_cart = ($ms_cart_refresh && $page->page_name== 'product')
            || (isset($_POST['action']) && $_POST['action'] == "add_product")
            || !empty($this->data['products_master_model'])
            || (is_array($msp->possibleProducts) && count($msp->possibleProducts)==1 && $page->page_name== 'product' && $this->pID == $current_product_id)
            //|| (count($msp->possibleProducts)==1 && ($page->page_name== 'product' || $page->page_name== 'categorie'))
            // feature request: bei nur einem slave: im listing slave anzeigen und addcart aktivieren
            ;

        if ($redirect)
        {
            global $xtLink;

            $first_data = $msp->possibleProducts[$first_key];

            if ($first_data['products_id'] != $this->pID)
            {
                $link_array = array('page' => 'product', 'type' => 'product', 'name' => $first_data['products_name'], 'params' =>'info='.$first_data['products_id'], 'seo_url' => $first_data['url_text']);
                $link = $xtLink->_link($link_array);
                $_SESSION['ms_slave_redirect'] = true;
                //error_log('redir 3');
                $xtLink->_redirect($link);
            }
        }
    }
}
