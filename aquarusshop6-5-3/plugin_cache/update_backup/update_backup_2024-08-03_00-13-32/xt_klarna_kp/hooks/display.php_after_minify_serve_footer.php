<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

require_once _SRV_WEBROOT . _SRV_WEB_PLUGINS. 'xt_klarna_kp/classes/class.xt_klarna_kp.php';

global $page, $db, $language, $xtLink, $currency, $tax, $countries, $customers_status, $info, $store_handler;

if($page->page_name == 'checkout' &&
    ($page->page_action == 'payment' || $page->page_action == 'confirmation') &&
    array_key_exists('kp_session', $_SESSION) && array_key_exists('client_token', $_SESSION['kp_session'])
)
{
    try
    {
        echo "<script>var klarna_kp_client_token = '".$_SESSION["kp_session"]["client_token"]."';</script>".PHP_EOL;

        $load_kp = false;

        if($page->page_action == 'payment')
        {
            unset($_SESSION['kp_finalize_required']);

            $pmcs = []; //['pay_now','pay_later',"pay_over_time"];
            $xt_kp = new xt_klarna_kp();
            $configured_pmcs = $xt_kp->get_saved_klarna_kp_payment_methods(['store_id' => $store_handler->shop_id], true);

            foreach ($_SESSION['kp_session']['payment_method_categories'] as $pmc)
            {
                if(!in_array($pmc['identifier'], $configured_pmcs)) continue;
                $pmcs[] =  $pmc['identifier'];
            }

            if(count($pmcs))
            {
                $load_kp = true;

                echo PHP_EOL . "<script>var klarna_kp_payment_method_categories = " . json_encode($pmcs) . ";</script>";
                echo PHP_EOL . "<script>var klarna_kp_do_load = true;</script>";

                $data_arr = [];
                klarna_kp::setCustomer(sessionCustomer(), $data_arr, $_SESSION['kp_session_b2b'] === true ? true:false);
                klarna_kp::setAddresses(sessionCustomer(), $data_arr);

                $auth_data = json_encode($data_arr);
                echo PHP_EOL . "<script>klarna_kp_auth_data = " . $auth_data . ";</script>";
                echo PHP_EOL . "<script>klarna_kp_auth_required = true;</script>" . PHP_EOL;
            }
        }

        else if($page->page_action == 'confirmation' && $_SESSION["selected_payment"] == 'xt_klarna_kp'/*&& $_SESSION['kp_finalize_required'] === 'true'*/)
        {
            $load_kp = true;

            $tplFile = 'klarna_kp_modal_pay_now_info.tpl.html';
            $template = new Template();
            $template->getTemplatePath($tplFile, 'xt_klarna_kp', '', 'plugin');
            $tpl_data_info = array(
                'kp_session_id' => $kp_session_id
            );
            $html = $template->getTemplate('', $tplFile, $tpl_data_info);
            echo $html;
            echo PHP_EOL . "<script>klarna_kp_selected = true;</script>";
            if($_SESSION['kp_finalize_required'] === 'true')
            {
                echo PHP_EOL . "<script>klarna_kp_finalize_required = true;</script>";
            }
            echo PHP_EOL."<script>klarna_kp_do_load = false;</script>".PHP_EOL;

            global $kp_reauthorize_required;
            if(!empty($kp_reauthorize_required))
            {
                echo PHP_EOL . '<script>klarna_kp_reauth_required_method ="'.$kp_reauthorize_required.'" ;</script>';
            }
        }

        if($load_kp)
        {
            echo '<script src="https://x.klarnacdn.net/kp/lib/v1/api.js" async></script>'.PHP_EOL;
        }
    }
    catch(Exception $e)
    {
        // check if error because no SSL is activated
        if (strpos($e->getMessage(),'must be a valid https URI')!==false) {
            error_log($e->getMessage());
            echo'<img src="plugins/xt_klarna_kp/images/kp_no_ssl_desktop.png" style="opacity: 0.5"/>';

        } else {
            error_log($e->getMessage());
            $html = '<div><p>'.TEXT_KLARNA_GENERAL_ERROR.'</p><p style="font-size:0.8em">'.$e->getMessage().'</p></div>';
            $info->_addInfo($html, 'error');

        }
    }
}
else if($page->page_name == 'checkout' && $page->page_action == 'success')
{
    if(!empty($_SESSION['kp_auto_capture_xt_order_id'])) // set in checkout_process_bottom if auto_capture = true
    {
        $callback_url = $xtLink->_link(array('page' => 'callback', 'paction' => 'xt_klarna_kp', 'conn'=>'SSL'));
        echo PHP_EOL.'
<script>
jQuery(document).ready(function()
{
    kp_fetch_order("'.$_SESSION['kp_auto_capture_xt_order_id'].'", "'.$callback_url.'");
});
</script>'.PHP_EOL;


    }
}