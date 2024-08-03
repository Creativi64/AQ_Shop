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

global $xtPlugin, $xtLink, $info, $page, $filter, $brotkrumen, $store_handler, $db, $current_product_id;

require_once(_SRV_WEBROOT._SRV_WEB_PLUGINS.'/xt_reviews/classes/class.xt_reviews.php');
$reviews = new xt_reviews();

if ($page->page_action == '' && $_POST['paction'] != '')
    $page->page_action = $_POST['paction'];

if (isset($page->page_action) && $page->page_action != '') {

    $rating_data = array(array('id' => '1', 'text' => '1'), array('id' => '2', 'text' => '2'), array('id' => '3', 'text' => '3'), array('id' => '4', 'text' => '4'), array('id' => '5', 'text' => '5'));

    switch ($page->page_action) {

        case 'write':
            ($plugin_code = $xtPlugin->PluginCode('reviews.php:write_top')) ? eval($plugin_code) : false;

            if (!isset($current_product_id) or $current_product_id == '') {
                $tmp_link = $xtLink->_link(array('page' => '404'));
                $xtLink->_redirect($tmp_link);
            }

            // 	check if logged in, if not, set snap and redirect
            if (!$_SESSION['registered_customer']) {
                $tmp_link = $xtLink->_link(array('page' => 'reviews', 'paction' => 'write', 'params' => 'info=' . $current_product_id));
                $brotkrumen->_setSnapshot($tmp_link);
                $info->_addInfoSession(TEXT_XT_REVIEWS_ERROR_LOGIN, 'error');
                $tmp_link = $xtLink->_link(array('page' => 'customer', 'paction' => 'login'));
                $xtLink->_redirect($tmp_link);
            }

            $form_data = array();

            $captcha_error = false;
            if ($_POST["action"] == 'add_review' && (!isset($_SESSION['registered_customer']) || $_POST['ignore_registered_customer']==1) )
            {
                include _SRV_WEBROOT._SRV_WEB_CORE.'/captcha_validate.php';
            }
            if($captcha_error)
            {
                $info->_addInfo(ERROR_CAPTCHA_INVALID, 'error');;
                $form_data = array('review_rating' => (int)$_POST['review_rating'], 'review_title' => $filter->_filter($_POST['review_title']), 'review_text' => $filter->_filter($_POST['review_text']));
            }

            $p_info = product::getProduct($current_product_id, 'full');

            if (!$p_info->is_product) {
                $tmp_link = $xtLink->_link(array('page' => '404'));
                $xtLink->_redirect($tmp_link);
            }

            // hat der Kunde den Artikel gekauft ?
            $verified_order_id = xt_reviews::writeReviewAllowed( $current_product_id, $_SESSION['registered_customer']);
            if(empty($verified_order_id))
            {
                $info->_addInfoSession(TEXT_XT_REVIEWS_NOT_ALLOWED, 'error');
                $tmp_link = $xtLink->_link(array('page' => 'product','params' => 'info=' . $current_product_id));
                $xtLink->_redirect($tmp_link);
            }
            $_POST['orders_id'] = $verified_order_id;

            ($plugin_code = $xtPlugin->PluginCode('reviews.php:write_action_add_review')) ? eval($plugin_code) : false;

            if (!$captcha_error &&  isset ($_POST['action']) && $_POST['action'] == 'add_review') {
                $result = $reviews->_addReview($_POST);
                if (!$result) {
                    $form_data = array('review_rating' => (int)$_POST['review_rating'], 'review_title' => $filter->_filter($_POST['review_title']), 'review_text' => $_POST['review_text']);
                    $info->_addInfo(TEXT_XT_REVIEW_FORM_ERROR, 'error');
                } else {
                    $info->_addInfoSession(XT_REVIEWS_ADD_SUCCESS, 'success');
                    $tmp_link = $xtLink->_link(array('page' => 'reviews', 'paction' => 'success', 'params' => 'info=' . $current_product_id));
                    $xtLink->_redirect($tmp_link);
                }
            }
            $p_info->getBreadCrumbNavigation();
            $brotkrumen->_addItem($xtLink->_link(array('page' => 'reviews', 'paction' => 'write', 'params' => 'info=' . $current_product_id)), TEXT_XT_REVIEWS_WRITE);

		$tpl_data = array(
			'message' => $info->info_content,
			'review_stars_rating' => $reviews->getStars($p_info->data['products_id']),
			'products_name' => $p_info->data['products_name'],
			'rating' => $rating_data,
            'show_write_reviews' => xt_reviews::writeReviewAllowed($p_info->data['products_id'], $_SESSION['registered_customer']) > 0,
			'products_id' => $current_product_id,
                'review_rating' => 5,
                'captcha_link' => $xtLink->_link(array('default_page' => 'captcha.php', 'conn' => 'SSL'))
		);

            $tpl_data = array_merge($tpl_data, $form_data);
            $tpl_data = array_merge($tpl_data, $p_info->data);
            $tpl = 'write_review.html';

            $template = new Template();
            $template->getTemplatePath($tpl, 'xt_reviews', '', 'plugin');

            $page_data = $template->getTemplate('xt_write_reviews_smarty', $tpl, $tpl_data);
            break;

        case 'success':
            if (!isset($current_product_id) or $current_product_id == '') {
                $tmp_link = $xtLink->_link(array('page' => '404'));
                $xtLink->_redirect($tmp_link);
            }

            $p_info = product::getProduct($current_product_id, 'full');

            if (!$p_info->is_product) {
                $tmp_link = $xtLink->_link(array('page' => '404'));
                $xtLink->_redirect($tmp_link);
            }

            // check if logged in, if not, set snap and redirect
            if (!$_SESSION['registered_customer']) {
                $tmp_link = $xtLink->_link(array('page' => 'reviews', 'paction' => 'write', 'params' => 'info=' . $current_product_id));
                $brotkrumen->_setSnapshot($tmp_link);
                $info->_addInfoSession(XT_REVIEWS_ERROR_LOGIN, 'error');
                $tmp_link = $xtLink->_link(array('page' => 'customer', 'paction' => 'login'));
                $xtLink->_redirect($tmp_link);
            }

            $tpl_data = array('message' => $info->info_content, 'product_data' => $p_info->data, 'products_name' => $p_info->data['products_name'], 'rating' => $rating_data);
            $tpl = 'success_review.html';

            $p_info->getBreadCrumbNavigation();
            $brotkrumen->_addItem($xtLink->_link(array('page' => 'reviews', 'paction' => 'write', 'params' => 'info=' . $current_product_id)), TEXT_XT_REVIEWS_SUCCESS);

            $template = new Template();
            $template->getTemplatePath($tpl, 'xt_reviews', '', 'plugin');

            $page_data = $template->getTemplate('xt_success_reviews_smarty', $tpl, $tpl_data);

            break;

        case 'show':

            global $db, $xtLink;

            if (!isset($current_product_id) or $current_product_id == '') {
                $tmp_link = $xtLink->_link(array('page' => '404'));
                $xtLink->_redirect($tmp_link);
            }

            $reviews = new xt_reviews();
            $data = $reviews->getReviewsListing($current_product_id, true);
            $reviews_data = $data['module_content'];
            $p_info = product::getProduct($current_product_id, 'full');

            if (XT_REVIEWS_ALL_REVIEWS_PAGE == 'false') {
                $tmp_link = $xtLink->_link(array('page' => 'product', 'type' => 'product', 'name' => $p_info->data['products_name'], 'id' => $p_info->data['products_id'], 'seo_url' => $p_info->data['url_text']));
                $xtLink->_redirect($tmp_link);
            }

            if (count($reviews_data) == 0) $info->_addInfo(TEXT_XT_REVIEWS_NO_REVIEWS, 'warning');

            $p_info->getBreadCrumbNavigation();
            $brotkrumen->_addItem($xtLink->_link(array('page' => 'reviews', 'paction' => 'show', 'params' => 'info=' . $current_product_id)), TEXT_XT_REVIEWS_HEADING_REVIEWS);

            $link_reviews_write = $xtLink->_link(array('page' => 'reviews', 'paction' => 'write', 'params' => 'info=' . $p_info->data['products_id']));

		$tpl_data = array(
			'show_product' => XT_REVIEWS_PRODUCT_ON_ALL_REVIEWS_PAGE,
			'message' => $info->info_content,
			'link_reviews_write' => $link_reviews_write,
			'review_stars_rating' => $reviews->getStars($p_info->data['products_average_rating']),
			'product_data' => $p_info->data,
			'reviews_data' => $reviews_data,
			'NAVIGATION_COUNT' => $reviews->navigation_count,
			'NAVIGATION_PAGES' => $reviews->navigation_pages,
            'store_domain' => $store_handler->domain,
            'reviewCount' => count($data['module_content']),
            'reviewTotalCount' => $data['total'],
            'show_write_reviews' => xt_reviews::writeReviewAllowed($p_info->data['products_id'], $_SESSION['registered_customer']) > 0
		);

            $tpl = 'list_reviews.html';
            $tpl_data = array_merge($tpl_data, $p_info->data);
            $template = new Template();
            $template->getTemplatePath($tpl, 'xt_reviews', '', 'plugin');

            $page_data = $template->getTemplate('xt_list_reviews_smarty', $tpl, $tpl_data);

            break;
    }

}