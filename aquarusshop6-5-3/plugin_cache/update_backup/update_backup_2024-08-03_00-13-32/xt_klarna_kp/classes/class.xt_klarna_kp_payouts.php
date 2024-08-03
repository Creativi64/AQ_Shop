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

require_once _SRV_WEBROOT . _SRV_WEB_PLUGINS. 'xt_klarna_kp/classes/constants.php';


class xt_klarna_kp_payouts
{

    private $_master_key = 'fake_id';

    public $position = '';
    public $url_data = array();

    public $sql_limit;
    private $limit = 25;

    function __construct()
    {
        $this->sql_limit = "0,{$this->limit}";
    }

    function setPosition($position)
    {
        $this->position = $position;
    }

    function _getParams()
    {
        $header = array();
        $header[$this->_master_key] = array('type' => 'hidden', 'readonly'=>true);

        /** HEADER KEYS, see class.ExtFunctions.php for more
        *
        $header[SOME_HEADER_KEY] = array(
        'type' => 'textarea',
        'readonly'=>false,
        'value'=>'defVal',
        'text'=>'field_title',
        'width'=> '400px',
        'height' => '30px',
        'required' => true,
        'min' => 8, // min length
        'max' => 50 // max length        );
        */

        /** TYPES, see class.ExtFunctions.php
        *
        * default type => textfield
        * weiter ermittlung mit preg_match(header_key)
        *      desc,textarea => textarea
        *      price => price
        *      status && !adminActionStatus => status
        *      adminActionStatus => adminActionStatus
        *      shop,flag,permission,fsk18,startpage => status
        *      date,last_modified => date
        *      image => image
        *      file => file
        *      icon => icon
        *      url => url
        *      _html => htmleditor
        *      template => dropdown
        *      _permission_info => admininfo
        *      hidden => hidden
        */

        /** TYPE dropdown
        *
        $header['some_dropdown_field'] = array(
        'type' => 'dropdown',
        'url'  => 'DropdownData.php?get=some_dropdown_data','text'=>TEXT_SOME_DROPDOWN_FIELD);
        */
        /** TODO add hook admin_dropdown.php:dropdown, eg
        *
        case 'some_dropdown_data':
        $data=array();

        $data[] =  array(
        'id' => 'option_1',
        'name' => TEXT_SOME_DROPDOWN_FIELD_OPTION_1);
        $data[] =  array('
        id' => 'option_2',
        'name' => TEXT_SOME_DROPDOWN_FIELD_OPTION_2);
        $data[] =  array(
        'id' => 'option_3',
        'name' => TEXT_SOME_DROPDOWN_FIELD_OPTION_3);
        $result=$data;

        break;
        */

        $params = array();

        $params['include'] = array ('payment_reference','payout_datum',
            'payout_sale','payout_fee','payout_tax','payout_return','payout_payout',
            'currency_code');

        $params['PageSize'] = $this->limit;
        //$params['GroupField'] = 'currency_code';
        //$params['SortField'] = 'payout_datum';

        $params['header'] = $header;
        $params['master_key'] = $this->_master_key;
        $params['display_deleteBtn'] = false;
        $params['display_resetBtn'] = true;
        $params['display_editBtn'] = false;
        $params['display_newBtn'] = false;
        $params['display_searchPanel']  = false;
        $params['display_statusTrueBtn'] = false;
        $params['display_statusFalseBtn'] = false;

        $extF = new ExtFunctions();
        $window = $extF->_RemoteWindow3("dummy",
            "",
            "adminHandler.php?plugin=xt_klarna_kp&load_section=xt_klarna_kp_payouts&pg=getSummary",
            '',
            array('invoice_paid' => 1), 500, 400, '');
        $window->setModal(true);
        $window->setTitle(TEXT_KLARNA_PAYOUTS_SUMMERY. ' '.TEXT_KLARNA_PAYOUTS);
        $js = $window->getJavascript(false, "new_window").' new_window.show();';

        $UserButtons['upload'] = array('text' => 'TEXT_KLARNA_PAYOUTS_SUMMERY', 'style' => 'upload', 'icon' => 'report.png', 'acl' => 'edit', 'stm' => $js);
        $params['display_uploadBtn'] = true;
        $params['UserButtons'] = $UserButtons;

        $rowActionsFunctions = array();
        $rowActions = array();
        /** ROW ACTION JS
        *
        $url_backend = _SRV_WEB. "adminHandler.php?plugin=xt_klarna_kp_payouts&load_section=xt_klarna_kp_payouts&pg=xt_klarna_kp_payouts_row_fnc_1&row_fnc_1_edit_id=";
        $js_backend  = "
        var edit_id = record.data.id;
        addTab('".$url_backend."' + edit_id,'".TEXT_xt_klarna_kp_payouts_ROW_FNC_1." ' + record.data.id);
        ";
        $rowActionsFunctions['xt_klarna_kp_payouts_row_fnc_1'] = $js_backend;
        $rowActions[] = array('iconCls' => 'xt_klarna_kp_payouts_row_fnc_1', 'qtipIndex' => 'qtip1', 'tooltip' => TEXT_XT_KLARNA_KP_PAYOUTS_ROW_FNC_1);

        */

        if (count($rowActionsFunctions) > 0) {
            $params['rowActions'] = $rowActions;
            $params['rowActionsFunctions'] = $rowActionsFunctions;
        }

        if (count($rowActions) > 0) {
            $params['rowActions'] = $rowActions;
            $params['rowActionsFunctions'] = $rowActionsFunctions;
        }


        return $params;
    }

    public function xt_klarna_kp_payouts_row_fnc_1($data)
    {
        return 'result of xt_klarna_kp_payouts_row_fnc_1<br />data:<br />'.print_r($data,true);
    }

    public function getSummary($data)
    {
        try
        {
            $start_date_s = $start_date =
                $end_date_s = $end_date = $currency = null;
            if (is_array($_SESSION['filters_xt_klarna_kp_payouts']))
            {
                if (isset($_SESSION['filters_xt_klarna_kp_payouts']['filter_date_from']))
                {
                    try
                    {
                        $start_date = DateTime::createFromFormat('Y/m/d', $_SESSION['filters_xt_klarna_kp_payouts']['filter_date_from']);
                        if ($start_date)
                        {
                            $start_date->setTime(0,0,0);
                            $start_date_s = $start_date->format(DATE_ATOM);
                        }
                    } catch (Exception $e)
                    {
                    }
                }
                if (isset($_SESSION['filters_xt_klarna_kp_payouts']['filter_date_to']))
                {
                    try
                    {
                        $end_date = DateTime::createFromFormat('Y/m/d', $_SESSION['filters_xt_klarna_kp_payouts']['filter_date_to']);
                        if ($end_date)
                        {
                            $end_date->add(new DateInterval('P1D'));
                            $end_date->setTime(0,0,0);
                            $end_date_s = $end_date->format(DATE_ATOM);
                        }
                    } catch (Exception $e)
                    {
                    }
                }
                if (isset($_SESSION['filters_xt_klarna_kp_payouts']['filter_currency']) &&
                    !empty($_SESSION['filters_xt_klarna_kp_payouts']['filter_currency'])
                )
                {
                    $currency = $_SESSION['filters_xt_klarna_kp_payouts']['filter_currency'];
                }
            }
            if($start_date_s == null)
            {
                $start_date = new DateTime();
                $start_date->setTime(0,0,0);
                $start_date = $start_date->sub(new DateInterval('P30D'));
                $start_date_s = $start_date->format(DATE_ATOM);
            }
            if($end_date_s == null)
            {
                $end_date = new DateTime();
                $end_date->add(new DateInterval('P1D'));
                $end_date->setTime(0,0,0);
                $end_date_s = $end_date->format(DATE_ATOM);
            }


            $payouts = klarna_kp::getPayoutSummary($start_date_s, $end_date_s, $currency);
            $payouts_data = array();
            foreach ($payouts as $payout_entry)
            {
                $payout_entry['formated'] = array(
                    'total_payouts' => number_Format($payout_entry['summary_total_settlement_amount'] / 100, 2, ',', '.'),
                    'total_sales' => number_Format($payout_entry['summary_total_sale_amount'] / 100, 2, ',', '.'),
                    'total_fees' => number_Format($payout_entry['summary_total_fee_amount'] / 100, 2, ',', '.'),
                    'total_tax' => number_Format($payout_entry['summary_total_tax_amount'] / 100, 2, ',', '.'),
                    'total_returns' => number_Format($payout_entry['summary_total_return_amount'] / 100, 2, ',', '.'),
                );
                $payouts_data[$payout_entry['summary_settlement_currency']] = $payout_entry;
            }

            $tplFile = 'payouts-summary.tpl.html';
            $template = new Template();
            $template->getTemplatePath($tplFile, 'xt_klarna_kp', 'admin', 'plugin');
            $tpl_data = array(
                'payouts_data' => $payouts_data,
                'date_start' => $start_date->format('Y-m-d'),
                'date_end' => $end_date->format('Y-m-d'),
                'total' => 234
            );
            $html = $template->getTemplate('', $tplFile, $tpl_data);
            echo $html;
        }
        catch (Exception $e)
        {
            echo $e->getMessage();
        }

        die();
    }


    public function getCsv($data)
    {
        try
        {
            $start_date_s = $start_date =
            $end_date_s = $end_date = $currency = null;

            if (!empty($data["filter_date_from"]))
            {
                try
                {
                    $start_date = DateTime::createFromFormat('Y/m/d', $data["filter_date_from"]);
                    if ($start_date)
                    {
                        $start_date->setTime(0,0,0);
                        $start_date_s = $start_date->format(DATE_ATOM);
                    }
                } catch (Exception $e)
                {
                }
            }
            if (!empty($data["filter_date_to"]))
            {
                try
                {
                    $end_date = DateTime::createFromFormat('Y/m/d', $data["filter_date_to"]);
                    if ($end_date)
                    {
                        $end_date->add(new DateInterval('P1D'));
                        $end_date->setTime(0,0,0);
                        $end_date_s = $start_date->format(DATE_ATOM);
                    }
                } catch (Exception $e)
                {
                }
            }

            if($start_date_s == null)
            {
                $start_date = new DateTime();
                $start_date->setTime(0,0,0);
                $start_date = $start_date->sub(new DateInterval('P30D'));
                $start_date_s = $start_date->format(DATE_ATOM);
            }
            if($end_date_s == null)
            {
                $end_date = new DateTime();
                $end_date->add(new DateInterval('P1D'));
                $end_date->setTime(0,0,0);
                $end_date_s = $end_date->format(DATE_ATOM);
            }


            $csv = klarna_kp::getPayoutsSummaryReport($start_date_s, $end_date_s);

            $file_name = $start_date->format('Y-m-d').'_'.$end_date->format('Y-m-d').'_'.time().'.csv';

            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="'.$file_name.'"');
            echo $csv;
        }
        catch (Exception $e)
        {
            echo $e->getMessage();
        }

        die();
    }


    public function _get($ID = 0)
    {
        if ($this->position != 'admin') return false;

        $data = array();
        $count_data = 0;
        if ($this->url_data['get_data'])
        {
            $offset = $this->url_data['start'];// / $this->limit;

            $start_date_s = $end_date_s = $currency = null;
            if(is_array($_SESSION['filters_xt_klarna_kp_payouts']))
            {
                if(isset($_SESSION['filters_xt_klarna_kp_payouts']['filter_date_from']))
                {
                    try
                    {
                        $start_date = DateTime::createFromFormat('Y/m/d', $_SESSION['filters_xt_klarna_kp_payouts']['filter_date_from']);
                        if ($start_date)
                            $start_date_s = $start_date->format(DATE_ATOM);
                    } catch (Exception $e)
                    {
                    }
                }
                if(isset($_SESSION['filters_xt_klarna_kp_payouts']['filter_date_to']))
                {
                    try
                    {
                        $end_date = DateTime::createFromFormat('Y/m/d', $_SESSION['filters_xt_klarna_kp_payouts']['filter_date_to']);
                        if ($end_date)
                            $end_date_s = $end_date->format(DATE_ATOM);
                    } catch (Exception $e)
                    {
                    }
                }
                if(isset($_SESSION['filters_xt_klarna_kp_payouts']['filter_currency']) &&
                    !empty($_SESSION['filters_xt_klarna_kp_payouts']['filter_currency'])
                )
                {
                    $currency = $_SESSION['filters_xt_klarna_kp_payouts']['filter_currency'];
                }
            }


            $payouts = klarna_kp::getPayouts($offset, $this->limit, $start_date_s, $end_date_s, $currency);
            $data = $payouts['payouts'];
            $count_data = $payouts["pagination"]["total"];


            /** für die view können hier werte angepasst/geändert werden */

            if (is_array($data) && sizeof($data)>0)
            {
                for($i=0; $i<sizeof($data); $i++)
                {
                    $data[$i]['fake_id'] = $offset + $i;

                    $data[$i]['payout_sale'] = number_Format($data[$i]['totals']['sale_amount']/100, 2, ',', '.');
                    $data[$i]['payout_fee'] = number_Format($data[$i]['totals']['fee_amount']/100, 2, ',', '.');
                    $data[$i]['payout_tax'] = number_Format($data[$i]['totals']['tax_amount']/100, 2, ',', '.');
                    $data[$i]['payout_return'] = number_Format($data[$i]['totals']['return_amount']/100, 2, ',', '.');
                    $data[$i]['payout_payout'] = number_Format($data[$i]['totals']['settlement_amount']/100, 2, ',', '.');



                    $date = new DateTime($data[$i]['payout_date']);
                    $data[$i]['payout_datum'] = $date->format('Y-m-d') ;
                }
            }

        }
        elseif($ID) {
            $data = $table_data->getData($ID);
            $count_data = 1;
        }
        else {
            $data = array(
                array('payment_reference')
            );
            $count_data = count($data);
        }

        if (!$this->url_data['get_data'] && false)
        {
            // rebuilding fields' order
            $defaultOrder = array(
                COL_KKP__ID_PK,
            );

            $orderedData = array();
            foreach ($defaultOrder as $key) {
                $orderedData[$key] = $data[0][$key];
            }
            $data = array($orderedData);
        }
        else if($ID==='new' && false)
        {

        }

        $obj = new stdClass;
        $obj->totalCount = $count_data;
        $obj->data = $data;

        return $obj;
    }


    function _set($data, $set_type = 'edit')
    {
        $o = new adminDB_DataSave($this->_table, $data, false, __CLASS__);
        $result = $o->saveDataSet();

        return $result;
    }


    function _unset($id = 0)
    {
        global $db;

        $delete = "DELETE FROM `".$this->_table. "` WHERE `".$this->_master_key. "`= '$id'";
        $db->Execute($delete);
        $affectedRows = $db->Affected_Rows();

        return $affectedRows >= 1;
    }

}
