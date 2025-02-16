<?php


require_once _SRV_WEBROOT._SRV_WEB_PLUGINS.'xt_orders_invoices/classes/constants.php';

require_once _SRV_WEBROOT._SRV_WEB_PLUGINS.'xt_orders_invoices/classes/vendor/autoload.php';
require_once _SRV_WEBROOT . _SRV_WEB_PLUGINS . 'xt_orders_invoices/classes/class.xt_orders_invoices.php';

use horstoeko\zugferd\ZugferdDocumentBuilder;
use horstoeko\zugferd\ZugferdProfiles;
use horstoeko\zugferd\codelists\ZugferdPaymentMeans;
use horstoeko\zugferd\ZugferdDocumentPdfMerger;


class xt_electronic_invoice extends default_table {
	
	protected $_table = TABLE_ORDERS_INVOICES;
    protected $_table_products = TABLE_ORDERS_INVOICES_PRODUCTS;
    protected $_allowed_countries_buyer = ['DE'];
    protected $errors = [];



    function __construct() {


    	$this->invoice_id = $invoice_id;

    	$this->type_code = 380;

        $this->errors[1]='company name missing / no b2b invoice';
        $this->errors[2]='billing country code not in list';
        $this->errors[3]='e-invoice proile settings missing';
        $this->errors[4]='xml generation not active for this shop';

    }

    public function _getErrorMessage($code) {
        return $this->errors[$code];
    }

    public function getXMLDocument() {

        $id = (int) $this->url_data['id'];

        $xml = $this->generateXMLDocument($id);
        if ($xml===false) {
            if (isset($this->error_code)) {
                if ($this->error_code=='999') {
                    echo $this->error_message;
                } else {
                    echo $this->_getErrorMessage($this->error_code);
                }
            }
        } else {
            header('Content-Type: application/xml');
            header('Content-Disposition: attachment; filename="erechnung_'.$this->url_data['id'].'.xml"');
            header('Content-Length: ' . strlen($xml));

            echo $xml;
            exit;
        } 

    }

    
    public function generateXMLDocument($id) {
    	global $db;

    	

    	$rs = $db->Execute("SELECT * FROM ".$this->_table." WHERE invoice_id=?",[$id]);

    	$oi = new xt_orders_invoices();
    	$invoice_data = $oi->getInvoiceContent($id);



    	$shopId = $db->GetOne("SELECT shop_id from ". TABLE_ORDERS ." WHERE orders_id=?", array($rs->fields['orders_id']));

    	// mandant plugin config values
    	$rs_plugin_conf = $db->getAssoc("SELECT config_key,config_value FROM ".TABLE_PLUGIN_CONFIGURATION . " WHERE shop_id=?",[$shopId]);

        // load profile from customer group and check customer individual profile
        $customer = $db->getRow("SELECT * FROM ".TABLE_CUSTOMERS." WHERE customers_id=?",[$rs->fields['customers_id']]);


        $cstatus = $db->getRow("SELECT * FROM ".TABLE_CUSTOMERS_STATUS." WHERE customers_status_id=?",[$customer['customers_status']]);

        if ($rs_plugin_conf['XT_ELECTRONIC_INVOICE_ACTIVE']=='false') {
            $this->error_code = 4;
            return false;
        }

        // reveiver is a company ?
        if ($invoice_data['order']['order_data']['billing_company']=='') {
            $this->error_code=1;
           return false;
        }
        // invoice country is allowed for XML ?
        if (!in_array($invoice_data['order']['order_data']['billing_country_code'],$this->_allowed_countries_buyer)) {
            $this->error_code=2;
            return false;
        }


        if ($cstatus['profile_e_invoice']>0 || $customer['profile_e_invoice']>0) {

            $profile = 0;
            if ($cstatus['profile_e_invoice']==0 && $customer['profile_e_invoice']>0) {
               $profile =  $customer['profile_e_invoice'];
            } else {
                $profile = $cstatus['profile_e_invoice'];
            }

        } else {
            $this->error_code=3;
            return false;
        }

        try {

        switch ($profile) {
            case 1:
                $document = ZugferdDocumentBuilder::CreateNew(ZugferdProfiles::PROFILE_MINIMUM);
                break;
            case 2:
                $document = ZugferdDocumentBuilder::CreateNew(ZugferdProfiles::PROFILE_BASIC);
                break;
            case 3:
                $document = ZugferdDocumentBuilder::CreateNew(ZugferdProfiles::PROFILE_EN16931);
                break;
            case 4:
                $document = ZugferdDocumentBuilder::CreateNew(ZugferdProfiles::PROFILE_XRECHNUNG);
                break;

            
        }


    	$document->setDocumentInformation($rs->fields['invoice_number'], $this->type_code, \DateTime::createFromFormat("Y-m-d H:i:s", $rs->fields['invoice_issued_date']), $invoice_data['invoice']['invoice_currency']);

        if ($invoice_data['invoice']['invoice_comment']!='') {
            $document->addDocumentNote($invoice_data['invoice']['invoice_comment']);
        }

        // Leistungsdatum
        $timestamp = strtotime($rs->fields['invoice_issued_date']);
        $duedate= new DateTime();
        $duedate->setTimeStamp($timestamp);
        $document->setDocumentSupplyChainEvent($duedate);
         
        
        switch ($invoice_data['order']['order_data']['payment_code']) {
            case 'xt_prepayment':
            case 'xt_invoice':
                $document->addDocumentPaymentMeanToCreditTransfer($rs_plugin_conf['XT_ELECTRONIC_INVOICE_SELLER_IBAN'], $rs_plugin_conf['XT_ELECTRONIC_INVOICE_SELLER_ACCOUNT_HOLDER'], null, $rs_plugin_conf['XT_ELECTRONIC_INVOICE_SELLER_BIC'],$rs->fields['invoice_number']);

            break;

            default: // 4461_68 = Online payment Service
                $document->addDocumentPaymentMean(ZugferdPaymentMeans::UNTDID_4461_68, $invoice_data['order']['order_data']['payment_name']);
            break;

        }
        

 		/**
         * 
         * SELLER
         * 
         */ 

    	$document->setDocumentSeller($invoice_data['config']['_store_shopowner_company']);
    	$document->setDocumentSellerAddress($invoice_data['config']['_store_shopowner_streetaddress'],
    								 "",
    								 "",
    								 $invoice_data['config']['_store_shopowner_zip'], 
    								 $invoice_data['config']['_store_shopowner_city'], 
    								 $invoice_data['config']['_store_country']);

    	if ($rs_plugin_conf['XT_ELECTRONIC_INVOICE_SELLER_TC_NAME']) {
            $document->setDocumentSellerContact($rs_plugin_conf['XT_ELECTRONIC_INVOICE_SELLER_TC_NAME'], 
                                    $rs_plugin_conf['XT_ELECTRONIC_INVOICE_SELLER_TC_DEPARTMENT'], 
                                    $rs_plugin_conf['XT_ELECTRONIC_INVOICE_SELLER_TC_PHONE'], 
                                    "",
                                    $rs_plugin_conf['XT_ELECTRONIC_INVOICE_SELLER_TC_EMAIL']);
        }
    	

    	// SELLER Identification Numners

    	if ($invoice_data['config']['_store_shopowner_gln']!='') 
    		$document->addDocumentSellerGlobalId($invoice_data['config']['_store_shopowner_gln'], "0088");
    	if ($invoice_data['config']['_store_shopowner_duns']!='') 
    		$document->addDocumentSellerGlobalId($invoice_data['config']['_store_shopowner_duns'], "0066");
    	if ($invoice_data['config']['_store_shopowner_taxid']!='') 
    		$document->addDocumentSellerTaxRegistration("FC",$invoice_data['config']['_store_shopowner_taxid']);
    	if ($invoice_data['config']['_store_shopowner_vatid']!='') 
    		$document->addDocumentSellerTaxRegistration("VA",$invoice_data['config']['_store_shopowner_vatid']);


        $document->addDocumentNote($invoice_data['config']['_store_shopowner_company']. PHP_EOL . $invoice_data['config']['_store_shopowner_streetaddress'] . PHP_EOL . $invoice_data['config']['_store_shopowner_zip'].' '.$invoice_data['config']['_store_shopowner_city'] . PHP_EOL . $invoice_data['config']['_store_country'] . PHP_EOL . 'Geschäftsführer: '.$invoice_data['config']['_store_shopowner_ceo'] . PHP_EOL . 'Handelsregisternummer: '.$invoice_data['config']['_store_shopowner_registerid'] . PHP_EOL . PHP_EOL, null, 'REG');

        /**
         * 
         * BUYER
         * 
         */ 

    	// setDocumentBuyer (invoice receiver)
    	$document->setDocumentBuyer($invoice_data['order']['order_data']['billing_company'], $invoice_data['order']['order_data']['customers_cid']);

    	//->setDocumentBuyerReference("34676-342323")
    	$document->setDocumentBuyerAddress($invoice_data['order']['order_data']['billing_street_address'], 
    							$invoice_data['order']['order_data']['billing_address_addition'], 
    							"", 
    							$invoice_data['order']['order_data']['billing_postcode'], 
    							$invoice_data['order']['order_data']['billing_city'], 
    							$invoice_data['order']['order_data']['billing_country_code']);
    	if ($invoice_data['order']['order_data']['customers_vat_id']!='') 
    		$document->addDocumentBuyerTaxRegistration("VA",$invoice_data['order']['order_data']['customers_vat_id']);
    	if ($customer['customer_gln']!='') 
            $document->addDocumentBuyerGlobalId($customer['customer_gln'],"0088");


        if ($invoice_data['order']['order_data']['billing_street_address']!=$invoice_data['order']['order_data']['delivery_street_address'] && $invoice_data['order']['order_data']['delivery_street_address']!='') {
            if ($invoice_data['order']['order_data']['delivery_company']!='') {
                $document->setDocumentShipTo($invoice_data['order']['order_data']['delivery_company']);
            } else {
                $document->setDocumentShipTo($invoice_data['order']['order_data']['delivery_firstname'].' '.$invoice_data['order']['order_data']['delivery_lastname']);
            }
            
            $document->setDocumentShipToAddress($invoice_data['order']['order_data']['delivery_street_address'], $invoice_data['order']['order_data']['delivery_address_addition'], "", $invoice_data['order']['order_data']['delivery_postcode'], $invoice_data['order']['order_data']['delivery_city'], $invoice_data['order']['order_data']['delivery_country_code']);
        }

        // payment terms
        $timestamp = strtotime($invoice_data['invoice']['invoice_due_date']);
        $duedate= new DateTime();
        $duedate->setTimeStamp($timestamp);


        $document->addDocumentPaymentTerm("",$duedate);



        // buyer reference BT-10 (LEITWEG ID), TODO erweitern, wenn leer ustid ?
        if ($customer['customer_leitweg_id']!='') {
            $document->setDocumentBuyerReference($customer['customer_leitweg_id']);
        } else if ($invoice_data['invoice']['invoice_payment']!='') {
            $document->setDocumentBuyerReference($invoice_data['invoice']['invoice_payment']);
        } else if ($invoice_data['order']['order_data']['customers_vat_id']!='') {
            $document->setDocumentBuyerReference($invoice_data['order']['order_data']['customers_vat_id']);
        } else {
             $document->setDocumentBuyerReference($invoice_data['invoice']['invoice_number']);
        }
        

        
        
        $position = [];
        $tax_total = [];
        $tax_net_total = [];
        $pos = 1;


        foreach ($invoice_data['products'] as $id => $prod) {
            $position[$prod['db_data']['products_id']] = $prod['db_data'];
        }

        // positions
        foreach ($invoice_data['order']['order_products'] as $id => $prod) {

            //print_r($position);
           // die();

            $document->addNewPosition($pos);
            $document->setDocumentPositionProductDetails($prod['products_name'], "", $prod['products_model'], null, "0160", $position[$prod['products_id']]['products_ean']);
            /**
             * The unit price excluding sales tax before deduction of the discount on the item price. If the price is shown according to the net calculation, the price must also be shown according to the gross calculation.
             * BT-148
             */
            $document->setDocumentPositionGrossPrice($prod['cart_product']['_original_products_price']['plain_otax']);
            //$document->setDocumentPositionGrossPrice($prod['products_price']['plain_otax']);

            $discount = $prod['cart_product']['_original_products_price']['plain_otax']-$prod['products_price']['plain_otax'];
            $document->addDocumentPositionAllowanceCharge((float)$discount,false,null,$prod['products_price']['plain_otax'],95);
            /**
             * The price of an item excluding VAT after deduction of the discount on the item price.
             * __BT-146
             */
            $document->setDocumentPositionNetPrice($prod['products_price']['plain_otax']);
            $document->setDocumentPositionQuantity($prod['products_quantity'], "H87");
            $document->addDocumentPositionTax('S', 'VAT', $prod['products_tax_rate']);
            /*
             * __BT-131, From BASIC__ The total amount of the invoice item.
             * __Note:__ This is the "net" amount, that is, excluding sales tax, but including all surcharges and discounts applicable to the item level, as well as other taxes.
             */
            $document->setDocumentPositionLineSummation($prod['products_final_price']['plain_otax']);
            
            $pos++;

            // tax
            $tax_total[$prod['products_tax_rate']]+=$prod['products_final_tax']['plain'];
            $tax_net_total[$prod['products_tax_rate']]+=$prod['products_final_price']['plain_otax'];

        }

        $total_discount = 0;
        $total_charge = 0;


        
        foreach ($invoice_data['order']['order_total_data'] as $id => $total)  {

            // push positive amounts to positions
            if ($total['orders_total_price']['plain_otax']>0) {

                $_charge = $total['orders_total_price']['plain_otax'];

                $taxtype = ($total['orders_total_tax_rate']>0 ? "S" : "Z");
                $document->addDocumentAllowanceCharge($_charge, true, $taxtype, "VAT", $total['orders_total_tax_rate'], null, null, null, null, null, null, "Discount");

                $total_charge += $_charge;

                $tax_total[$total['orders_total_tax_rate']]+=$total['orders_total_final_tax']['plain'];
                $tax_net_total[$total['orders_total_tax_rate']]+=$total['orders_total_final_price']['plain_otax'];


            // set negative amounts as TotalAlowance
            } else {

                $_allowance = $total['orders_total_price']['plain_otax']*-1;

                $taxtype = ($total['orders_total_tax_rate']>0 ? "S" : "Z");
                $document->addDocumentAllowanceCharge($_allowance, false, $taxtype, "VAT", $total['orders_total_tax_rate'], null, null, null, null, null, null, "Discount");

                $total_discount += $_allowance;

                $tax_total[$total['orders_total_tax_rate']]+=$total['orders_total_final_tax']['plain'];
                $tax_net_total[$total['orders_total_tax_rate']]+=$total['orders_total_final_price']['plain_otax'];

            }


        }



        // shipping and surcharges as position

        /**
        * Document money summation
        *
        * @param  float      $grandTotalAmount     __BT-112, From MINIMUM__ Total invoice amount including sales tax
        * @param  float      $duePayableAmount     __BT-115, From MINIMUM__ Payment amount due
        * @param  float|null $lineTotalAmount      __BT-106, From BASIC WL__ Sum of the net amounts of all invoice items
        * @param  float|null $chargeTotalAmount    __BT-108, From BASIC WL__ Sum of the surcharges at document level
        * @param  float|null $allowanceTotalAmount __BT-107, From BASIC WL__ Sum of the discounts at document level
        * @param  float|null $taxBasisTotalAmount  __BT-109, From MINIMUM__ Total invoice amount excluding sales tax
        * @param  float|null $taxTotalAmount       __BT-110/111, From MINIMUM/BASIC WL__ if BT--6 is not null $taxTotalAmount = BT--111. Total amount of the invoice sales tax, Total tax amount in the booking currency
        * @param  float|null $roundingAmount       __BT-114, From EN 16931__ Rounding amount
        * @param  float|null $totalPrepaidAmount   __BT-113, From BASIC WL__ Prepayment amount
        * @return ZugferdDocumentBuilder
        */
        $tax_amount = $invoice_data['invoice']['invoice_total']-$invoice_data['invoice']['invoice_total_otax'];

        $document->setDocumentSummation(
            (float)$invoice_data['invoice']['invoice_total'],               // BT-112
            (float)$invoice_data['invoice']['invoice_total'],               // BT-115
            (float)$invoice_data['order']['order_total']['product_total_otax']['plain'],          // BT-106
            (float)$total_charge,                                                            // BT-108
            (float)$total_discount,                                         // BT-107
            (float)$invoice_data['invoice']['invoice_total_otax'],          // BT-109
            (float)$tax_amount,                                             // BT-110/111
            null,                                                           // BT-114
            0.0                                                             // BT-113
            );

        // tax breakdown
        foreach ($tax_total as $id => $tax) {
            if ($id==0) {
                $document->addDocumentTax("Z", "VAT", $tax_net_total[$id], $tax_total[$id], $id);
            } else {
                $document->addDocumentTax("S", "VAT", $tax_net_total[$id], $tax_total[$id], $id);
            }
        }

        } catch (Throwable $e) {
            $this->error_code = '999';
            $this->error_message = $e->getMessage();
            return false;
        }
        
  

    	return $document->getContent();
  
    }

 


}