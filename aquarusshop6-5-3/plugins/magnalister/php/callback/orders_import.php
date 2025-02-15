<?php
/**
 * 888888ba                 dP  .88888.                    dP                
 * 88    `8b                88 d8'   `88                   88                
 * 88aaaa8P' .d8888b. .d888b88 88        .d8888b. .d8888b. 88  .dP  .d8888b. 
 * 88   `8b. 88ooood8 88'  `88 88   YP88 88ooood8 88'  `"" 88888"   88'  `88 
 * 88     88 88.  ... 88.  .88 Y8.   .88 88.  ... 88.  ... 88  `8b. 88.  .88 
 * dP     dP `88888P' `88888P8  `88888'  `88888P' `88888P' dP   `YP `88888P' 
 *
 *                          m a g n a l i s t e r
 *                                      boost your Online-Shop
 *
 * -----------------------------------------------------------------------------
 * $Id: orders_import.php 167 2013-02-08 12:00:00Z tim.neumann $
 *
 * (c) 2010 - 2013 RedGecko GmbH -- http://www.redgecko.de
 *     Released under the MIT License (Expat)
 * -----------------------------------------------------------------------------
 */
defined('_VALID_XTC') or die('Direct Access to this location is not allowed.');

require_once(DIR_MAGNALISTER_CALLBACK.'callbackFunctions.php');

function magnaInitOrderImport() {
    global $magnaInitOrderImportCalled, $_magnaLanguage;
    
    /* Prevent this funciton to be called twice. */
    if ($magnaInitOrderImportCalled === true) {
        return;
    }
    $magnaInitOrderImportCalled = true;
    define('MAGNA_ORDERS_DATERANGE_BEGIN', time() - 60 * 60 * 24 * 30);
}

function magnaImportAllOrders() {
    global $_MagnaShopSession, $magnaConfig;

    magnaInitOrderImport();

    $verbose = isset($_GET['MLDEBUG']) && ($_GET['MLDEBUG'] == 'true');

    /* Bitte nicht vor Lachen in Traenen ausbrechen, aber die naechsten Zeilen sollen so etwas wie ein "mutex" darstellen :-) */
    $doImports = false;
    usleep(rand(200, 800));
    $lockName = DIR_MAGNALISTER.'OrderImportLock';
    
    $aMutex = array(
        'time' => time(),
    );
    $sRandom = '';
    if (function_exists('random_bytes') && function_exists('bin2hex')) {
        try {
            $sRandom = bin2hex(random_bytes(32));
        } catch (Exception $oEx) {
        }
    } 
    if (empty($sRandom) && function_exists('openssl_random_pseudo_bytes') && function_exists('bin2hex')) {
        $sRandom = bin2hex(openssl_random_pseudo_bytes(32));
    }
    if (empty($sRandom)) {
        $sRandom = uniqid('', true);
    }
    $aMutex['unique'] = $sRandom;
    if (!file_exists($lockName)) {
        file_put_contents($lockName, json_encode($aMutex));
        chmod($lockName, 0666);
        $doImports = true;
    } else {
        $aGetted = json_decode(@file_get_contents($lockName), true);
        $time = (int)$aGetted['time'];
        if (($time + 1200) < time()) {
            file_put_contents($lockName, json_encode($aMutex));
            chmod($lockName, 0666);
            $doImports = true;
        }
    }
    usleep(rand(20000, 80000));
    $aGetted = json_decode(@file_get_contents($lockName), true);
    if ($aMutex['unique'] !== $aGetted['unique']) {
        $doImports = false;
    }

    /* {Hook} "PreOrderImport": Runs before the order import starts. */
    if (($hp = magnaContribVerify('PreOrderImport', 1)) !== false) {
        require($hp);
    }

    if ($verbose) {
        header('Content-Type: text/plain');
        echo var_dump_pre($doImports, '$doImports');
    }
    if(!$doImports && MAGNA_CALLBACK_MODE == 'STANDALONE') {
        echo "OrderImportLock set, other order import is already running. Not starting.\n";
    }
    if ($doImports) {
        $modules = magnaGetInvolvedMarketplaces();

        ini_set('memory_limit', '512M');
        MagnaConnector::gi()->setTimeOutInSeconds(
            MAGNA_DEBUG && defined('MAGNALISTER_PLUGIN') && MAGNALISTER_PLUGIN 
                ? 1 
                : 600
        );

        $break = false;
        foreach ($modules as $marketplace) {
            if ($break) break;
            $mpIDs = magnaGetInvolvedMPIDs($marketplace);
            if (empty($mpIDs)) continue;
            if ($verbose) echo print_m($mpIDs, $marketplace);
            foreach ($mpIDs as $mpID) {
                if ($break) break;
                $funcName = false;
                $className = false;
                
                $funcFile = DIR_MAGNALISTER_CALLBACK.'get_'.$marketplace.'_orders.php';
                $classFile = DIR_MAGNALISTER_MODULES.strtolower($marketplace).'/crons/'.ucfirst($marketplace).'ImportOrders.php';
                /*
                if (file_exists($funcFile)) {
                    require_once($funcFile);
                    $funcName = 'magnaImport'.ucfirst($marketplace).'Orders';
                    
                    if (!function_exists($funcName)) {
                        continue;
                    }
                } else
                //*/
                if (file_exists($classFile)) {
                    require_once($classFile);
                    $className = ucfirst($marketplace).'ImportOrders';
                    if (!class_exists($className)) {
                        if ($verbose) echo 'Class '.$className.' does not exist in '.$classFile."\n";
                        continue;
                    }
                } else if (file_exists($funcFile)) {
                    require_once($funcFile);
                    $funcName = 'magnaImport'.ucfirst($marketplace).'Orders';
                    
                    if (!function_exists($funcName)) {
                        if ($verbose) echo 'File '.$funcName.' does not exist in '.$funcFile."\n";
                        continue;
                    }
                } else {
                    if ($verbose) echo 'Neither '.$classFile.' nor '.$funcFile." do exist.\n";
                    continue;
                }

                $aGetted = json_decode(@file_get_contents($lockName), true);
                if ($aMutex['unique'] !== $aGetted['unique']) {
                    # Sollte ein anderer Prozess gestartet sein, hoere hier auf
                    # und vermerke dass nach doppelten Bestellungen geschaut werden soll
                    setDBConfigValue('deletedoubleorders', 0, 'true', true);
                    if ($verbose || (MAGNA_CALLBACK_MODE == 'STANDALONE')) echo 'Parallel ImportOrders detected.'."\n";
                    $break = true;
                    break;
                }
                if (!array_key_exists('db', $magnaConfig) || 
                    !array_key_exists($mpID, $magnaConfig['db'])
                ) {
                    loadDBConfig($mpID);
                }
                if (getDBConfigValue($marketplace.'.import', $mpID, 'false') != 'true') {
                    if ($verbose) echo $marketplace.': Import disabled.'."\n";
                    continue;
                }

                if ($className !== false) {
                    if ($verbose) echo print_m('Import :: new '.$className.'('.$mpID.', \''.$marketplace.'\')'."\n");
                    $ic = new $className($mpID, $marketplace);
                    $ic->process();
                } else {
                    if ($verbose) echo print_m('Import :: '.$funcName.'['.$mpID.']'."\n");
                    $funcName($mpID);
                }
            }
            #echo print_m($mpIDs, $marketplace);
            MagnaConnector::gi()->resetTimeOut();
        }
        @unlink($lockName);
    }
    magnaFixOrders();
    
    /* {Hook} "PostOrderImport": Runs after the order import ends. */
    if (($hp = magnaContribVerify('PostOrderImport', 1)) !== false) {
        require($hp);
    }
    if ($verbose) {
        die();
    }
}
