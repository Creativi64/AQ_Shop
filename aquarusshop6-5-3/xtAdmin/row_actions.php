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

global $db, $xtc_acl, $xtLink, $xtPlugin;

if (!$xtc_acl->isLoggedIn()) {
	die('login required');
}


if (isset($_GET['type'])) {
	
	
	if ($_GET['type']=='export_language') {

			$lang_id = (int)$_GET['language_id'];	
			$lang_content = new language_content();
			$lang_content->_exportXML($lang_id);
	}
    
    if ($_GET['type']=='export_language_yml') {

        $lang_id = (int)$_GET['language_id'];
        $lang_content = new language_content();
        $r = $lang_content->_exportYML($lang_id);

        header('Content-Type: application/json');
        echo json_encode($r);
        die();
    }
    
    if ($_GET['type']=='export_nottranslated') {

        $lang_id = (int)$_GET['language_id'];
        $lang_content = new language_content();
        $r = $lang_content->_exportYML($lang_id,'untranslated');

        header('Content-Type: application/json');
        echo json_encode($r);
        die();
    }

    if ($_GET['type']=='export_lang_class_problems') {

        $lang_id = (int)$_GET['language_id'];
        $lang_content = new language_content();
        $r = $lang_content->_exportYML($lang_id,'lang_class_problems');

        header('Content-Type: application/json');
        echo json_encode($r);
        die();
    }
    
	
	// delete orders
	if ($_GET['type']=='delete_order') {
		// check permission
		if (!$xtc_acl->checkPermission('order','delete'))
        {
            $response = array('msg' => __text('TEXT_NO_SUCCESS') );
            echo json_encode($response);
            die();
        };
		
		$orders_id = (int)$_GET['orders_id'];
		$order = new order($orders_id,-1);
		$refill_stock = false;
		if ($_GET['fillup_stock']=='1') $refill_stock=true;

		$defaultMsg = constant('TEXT_ORDER_DELETE_SUCCESS');
		$resultMsg = $order->_deleteOrder($orders_id,$refill_stock);

		$response = array('msg' => !empty($resultMsg) ? $resultMsg : $defaultMsg );
		echo json_encode($response);
		die();
	}

    if ($_GET['type']=='delete_customer') {
        // check permission
        if (!$xtc_acl->checkPermission('customer','delete'))
        {
            $response = array('msg' => __text('TEXT_NO_SUCCESS') );
            echo json_encode($response);
            die();
        };

        $customers_id = (int)$_GET['customers_id'];
        $customer = new customer($customers_id);
        $delete_order = false;
        $refill_stock = false;
        if ($_GET['delete_order']=='1') {
            $delete_order = true;
            if ($_GET['fillup_stock']=='1') $refill_stock = true;
        }

        $defaultMsg = constant('TEXT_SUCCESS');
        $params =  [
            'delete_order' => $delete_order,
            'refill_stock' => $refill_stock
        ];
        $resultMsg = $customer->deleteCustomer($customers_id, $params);

        $response = array('msg' => !empty($resultMsg) ? $resultMsg : $defaultMsg );
        echo json_encode($response);
        die();
    }
	
	if ($_GET['type']=='image_processing') {

        if (constant('_SYSTEM_SECURITY_KEY')!=$_GET['seckey'])
        {
            echo constant('TEXT_WRONG_SYSTEM_SECURITY_KEY'); return false;
        }

		$params = 'imgProc=images&mgID='.$_GET['mgID'].'&seckey='.$_GET['seckey'];
	
		$iframe_target = $xtLink->_adminlink(array('default_page'=>'cronjob.php','conn'=>'SSL', 'params'=>$params));
		$iframe_target= str_ireplace("xtadmin/","",$iframe_target);
		echo '<p>Started... please wait.</p><iframe src="'.$iframe_target.'" frameborder="0" width="100%" height="500"></iframe>';
	}	
	
	if ($_GET['type']=='image_importing') {

        if (constant('_SYSTEM_SECURITY_KEY')!=$_GET['seckey'])
        {
            echo constant('TEXT_WRONG_SYSTEM_SECURITY_KEY'); return false;
        }

		$params = 'ImportImages=1&currentType='.$_GET['currentType'].'&mgID='.$_GET['mgID'].'&seckey='.$_GET['seckey'];
	
		$iframe_target = $xtLink->_adminlink(array('default_page'=>'cronjob.php','conn'=>'SSL', 'params'=>$params));
		$iframe_target= str_ireplace("xtadmin/","",$iframe_target);
		echo '<p>Started... please wait.</p><iframe src="'.$iframe_target.'" frameborder="0" width="100%" height="600"></iframe>';
	}	
	
	if ($_GET['type']=='export_manager') {
		$id = $_GET['feed_id'];
        if (constant('_SYSTEM_SECURITY_KEY')!=$_GET['seckey'])
        {
            echo constant('TEXT_WRONG_SYSTEM_SECURITY_KEY'); return false;
        }
		
		$params = 'feed_id='.$id.'&seckey='.$_GET['seckey'];
                
                $feed = $db->Execute("SELECT * FROM " . TABLE_FEED . " WHERE feed_id= ?", [$id]);
                
                if($feed->RecordCount()==1)
                {
                    $data = $feed->fields;

                    if ($data['feed_store_id'] > 0) {
                        $query = "SELECT * FROM " . TABLE_MANDANT_CONFIG . " where shop_id = ?";
                        $record = $db->Execute($query, array($data['feed_store_id']));

                        if ($record->RecordCount() == 1) {
                            $data['MANDANT'] = $record->fields;
                        }
                        else {
                            echo 'No store with id ' . $data['feed_store_id'];
                            exit;
                        }
                    }

                    if($feed->fields['feed_pw_flag'] == 1){
                        if(isset($feed->fields['feed_pw_user'])){
                            $params .= '&user='.$feed->fields['feed_pw_user'];
                        }
                        if(isset($feed->fields['feed_pw_pass'])){
                            $params .= '&pass='.$feed->fields['feed_pw_pass'];
                        }
                    }

                    $xtLink->setLinkURL('http://'.$data['MANDANT']['shop_ssl_domain']);
                    $xtLink->setSecureLinkURL('https://'.$data['MANDANT']['shop_ssl_domain']);

                    ($plugin_code = $xtPlugin->PluginCode('class.export.php:init_getData')) ? eval($plugin_code) : false;
                    if (isset($plugin_return_value))
                        return $plugin_return_value;

                    $iframe_target = $xtLink->_adminlink(array('default_page'=>'cronjob.php','conn'=>'SSL', 'params'=>$params));
                    echo '<p id="node_feed_'.$id.'">Started... please wait.</p><iframe src="'.$iframe_target.'" frameborder="0" width="100%" height="500"></iframe>';
                }else{
                    echo 'No feed with id ' . $id;
                    exit;
                }
	}
	
	if ($_GET['type'] == 'seo_regenerate') {
		$params = array();
		$params['store_id'] = $_GET['store_id'];
		$params['url_type'] = $_GET['url_type'];
		$params['offset'] = 0;
		$params['seo_regenerate'] = 1;
        $params['seckey'] = constant('_SYSTEM_SECURITY_KEY');
		
		$iframe_target = $xtLink->_adminlink(array('default_page'=>'cronjob.php','conn'=>'SSL', 'params'=>http_build_query($params)));
		echo '<p>Started... please wait.</p><iframe src="'.$iframe_target.'" frameborder="0" width="100%" height="500"></iframe>';
	}

	/** restore less theme file */
	if ($_GET['type']=='restore_theme') {
        $theme = new themes();
        $result = $theme->restore_theme($_GET['theme'],$_GET['id']);
    }

    /**
     * generate theme css
     */
    if ($_GET['type']=='generate_css') {
        $theme = new themes();
        $theme->generateCss($_GET['theme']);
    }
    /**
     * create an override template
     */
    if ($_GET['type']=='create_template_override') {
        $theme = new themes();
        $theme->createOverride($_GET['theme']);
    }
    /**
     * duplicate a theme (should be no reason for that)
     */
    if ($_GET['type']=='copy_theme') {
        $theme = new themes();
        $theme->copyTheme($_GET['theme']);
    }


	($plugin_code = $xtPlugin->PluginCode('row_actions.php:actions')) ? eval($plugin_code) : false;
		
}
