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

class it_recht_kanzlei
{
    // ----- Settings -----
    public $local_api_version = '1.0'; 									// only change when told to do so

    public $local_user_auth_token = '';
    public $local_supported_rechtstext_types = array('agb', 'datenschutz', 'widerruf', 'impressum');	// change to shop's requirements / supported legal texts, according to specification

    public $local_supported_rechtstext_languages = array('de');			    // only change when told to do so
    public $local_supported_actions = array('push');						// only change when told to do so

    public $local_rechtstext_pdf_required = array('agb' => false);			// true or false (set to true for each rechtstext type where you require a pdf-file) -  change to shop's requirements / supported legal texts, according to specification
    public $local_dir_for_pdf_storage = '';								    // directory where to store downloaded pdf-files, append with a slash, e.g. 'pdfs/'  - leave empty for pdf files being stored in same dir like this script
    public $local_limit_download_from_host = '';							// only change when told to do so, this will limit pdf downloads to a specific source host. e.g. 'www.it-recht-kanzlei.de'

    public $local_flag_multishop_system = true; 							// true or false (only set to true if your system is a multishop-system, this means that under one user/password login a user manages more than one shop)

    public $test_with_local_xml_file = true; 								// true or false (only set to true for testing, requires 'beispiel.xml' in same directory)

    public $shop_version = '';
    public $module_version = '';

    function __construct()
    {
        // ----- begin automatic dependant settings (do not change) -----
        // if your system is a multishop system, action 'getaccountlist' should be supported
         if($this->local_flag_multishop_system === true){ array_push($this->local_supported_actions, 'getaccountlist'); }
        // no host limit for downloading pdf when testing
        if($this->test_with_local_xml_file === true){ $this->local_limit_download_from_host = ''; }
        // ----- end automatic dependant settings (do not change) -----
    }


    function url_valid($url, $limit_to_host = '')
    {
        // $limit_to_host is obsolete and remains as a parameter for compatibility reasons

        $url_parts = parse_url($url);
        // check for allowed URLs
        if (in_array($url_parts['host'], _ITRK_ALLOWED_HOSTS) ||
            ($this->test_with_local_xml_file ===true && $this->local_limit_download_from_host == '')
        )
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    // check if a file is a pdf
    function check_if_pdf_file($filename)
    {
        $handle = @fopen($filename, "r");
        $contents = @fread($handle, 4);
        @fclose($handle);
        if ($contents == '%PDF')
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    function setResponseHeader()
    {
        header('Content-Type: application/xml; charset=utf-8');
    }
    function outXmlDeclaration()
    {
        echo "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>\n";
    }

    // return account list
    function return_account_list($array_account_list)
    {
        // return account-list
        $this->setResponseHeader();
        $this->outXmlDeclaration();

        echo "<accountlist>\n";
        foreach ($array_account_list as $xt_store)
        {
            if ($xt_store['status']==1)
            {
                echo "	<account>\n";
                echo "		<accountid>" . htmlspecialchars($xt_store['id'],ENT_QUOTES, 'UTF-8') . "</accountid>\n";
                echo "		<accountname>" . htmlspecialchars($xt_store['text'],ENT_QUOTES, 'UTF-8') . "</accountname>\n";
                echo "	</account>\n";
            }
        }
        echo "</accountlist>";

        // end script
        exit();
    }

    // send error
    function return_error($error_code, $msg = '')
    {
        // output error
        $this->setResponseHeader();
        $this->outXmlDeclaration();

        echo "<response>\n";
        echo "	<status>error</status>\n";
        echo "	<error>" . $error_code . "</error>\n";
        if (!empty($msg) && $this->test_with_local_xml_file)
        {
            echo "	<meta_message>" . $msg . "</meta_message>\n";
        }
        echo "	<meta_shopversion>" . $this->shop_version . "</meta_shopversion>\n";
        echo "	<meta_modulversion>" . $this->module_version . "</meta_modulversion>\n";
        echo "</response>";
    }

    // send success
    function return_success()
    {
        // output success
        $this->setResponseHeader();
        $this->outXmlDeclaration();

        echo "<response>\n";
        echo "	<status>success</status>\n";
        echo "	<meta_shopversion>" . $this->shop_version . "</meta_shopversion>\n";
        echo "	<meta_modulversion>" . $this->module_version . "</meta_modulversion>\n";
        echo "</response>";
    }

}
