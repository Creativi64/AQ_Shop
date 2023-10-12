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

/**
 *
 * @global type $xtPlugin
 * @param type $user
 * @param type $pass
 * @param type $ProductModel
 * @param type $productExternalId
 * @param type $files
 * @return string
 */
function setArticleFiles($user = '', $pass = '', $ProductModel = '', $productExternalId ='',$files = '') {
      global $xtPlugin;

    // Hat user Zugriff?
    $hasAccess = SoapHelper::secured($user, $pass);
    if (!$hasAccess) {
        $result['result'] = false;
        $result['message'] = SoapHelper::MessageLoginFailed;
        $result['http_response_code'] = 401;
        return $result;
    }

    if( !empty($productExternalId) ){
         // Produkt ID anhand von ProductModel oder external_id ermitteln
	        $productID = SoapHelper::getProductID($productExternalId, $ProductModel);
    }
    else{
        // Produkt ID anhand von ProductModel anziehen
        $productID = SoapHelper::getProductIDByModel($ProductModel);
    }

    // $productID ein boolean UND false
    if (false === $productID) {
        // Fehlermeldung erzeugen und raus
        $result['result'] = false;
        //$result['message'] = SoapHelper::MessageFail . " product not exists externalID:" . $ProductItem['external_id'] . " productsModel:" . $ProductItem['products_model'] . " productsID:" . $ProductItem['products_id'];
        $result['message'] = SoapHelper::MessageFail . " product not exists externalID:" . $productExternalId . " productsModel:" . $ProductModel . " productsID:" . $ProductItem['products_id'];

        return $result;
    }

    //Hookpoint Top
    ($plugin_code = $xtPlugin->PluginCode('setArticleImages.php:setArticleFiles_top')) ? eval($plugin_code) : false;


    $Exception = "";

    SoapHelper::clearMediaFilesLinkFromProduct($productID);

    try { // product files

          if (is_array($files) && count($files)) {
              foreach ((array) $files as $index => $data) {

                $dl_type = 'files_free';
                if ($data['file_type']!='free') $dl_type = 'files_order';

                      SoapHelper::processFiles($data['file'],
                       $data['file_name'],
                       $productID,
                       $dl_type,
                       $data['file_url'],
                       $data['file_max_downloads'],
                       $data['file_max_download_days'],
                       $data['media_name'],
                       $data['media_description']
                     );
                  }

          }

          ($plugin_code = $xtPlugin->PluginCode('setArticleImages.php:setArticleImages_bottom')) ? eval($plugin_code) : false;

      } catch (Exception $ex) {
          // Eine Exception wurde gefangen, also Fehlermeldung schriben und raus
          $Exception.=SoapHelper::MessageFail . " Update processFiles: Execption Message:" . $ex->getMessage() . " in " . $ex->getFile() . "::" . $ex->getLine() . ") ";
          $result['result'] = false;
          $result['message'] = SoapHelper::MessageFail . " Message:" . $Exception;
          return $result;
      }

    // Erfolgsmeldung setzen
    $result['result'] = true;
    $result['message'] = SoapHelper::MessageSuccess;

    // Rückgabe
    return $result;
}
?>
