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
  
  // header redirect
  
  function ioncube_event_handler($err_code,$params) {

        switch ($err_code) {
            
            // license missing
            case '6':
            case '10':
                
                _getOrderLicensePage();
                break;
            
            // license expired
            case '8':
            
                echo 'License file expired, please contact helpdesk@xt-commerce.com';
                break;
                
            case '7':
            
                echo 'Licensefile corrupt, please contact helpdesk@xt-commerce.com';
                break;
                   
            case '11':
            
                echo 'Domain in Licensefile is not Matching the installed Domain, please contact helpdesk@xt-commerce.com';
                break;
       
            default:
                echo 'IonCube Error Code:'.$err_code;
            break;
            
        }    
  }
  

  function _getOrderLicensePage() {
      
      $source_url = 'http://addons.xt-commerce.com/no_license_info/no_license.html';
      // get page with curl and display
      
      $options = array(
        'CURLOPT_RETURNTRANSFER' => true,
        'CURLOPT_HEADER'         => false,
        'CURLOPT_USERAGENT'      => "veyton",
        'CURLOPT_AUTOREFERER'    => true,
        'CURLOPT_CONNECTTIMEOUT' => 60,
        'CURLOPT_TIMEOUT'        => 60,
        'CURLOPT_MAXREDIRS'      => 1,
        );
    if (function_exists('curl_init')){

        $ch      = curl_init( $source_url );
        @curl_setopt_array( $ch, $options );
        $content = curl_exec( $ch );
        $err     = curl_errno( $ch );
        $errmsg  = curl_error( $ch );
        $header  = curl_getinfo( $ch );
        curl_close( $ch );
    
        if ($err==0) {
            echo $content;
        } else {
            echo 'Curl Error: '.$errmsg.' code:'.$err;
        }
    } else {
     
     $response = file_get_contents($source_url, false); 
     echo $response;   
        
        
    }
      
  }
  
  
?>