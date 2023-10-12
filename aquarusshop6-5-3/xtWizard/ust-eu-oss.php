<?php

error_reporting(E_ALL);
ini_set("display_errors", 1);
// Flag
define('XT_WIZARD_STARTED', true);
// Define short code for DIRECTORY_SEPARATOR
define('DS', DIRECTORY_SEPARATOR);
// Wizard root dir
define('ROOT_DIR_PATH', dirname(__FILE__) . DS);
// Needed for the logger. TRUE -> display in brower, FALSE -> log to file
define('DISPLAY_ERRORS', false);
date_default_timezone_set('Europe/Berlin');
$root_dir = str_replace('xtWizard', '', dirname(__FILE__));
$sys_dir = $_SERVER['SCRIPT_NAME'];
$sys_dir = substr($sys_dir, 0, strripos($sys_dir, '/') + 1);
$sys_dir = str_replace('xtWizard/', '', $sys_dir);

if (!defined('_SRV_WEBROOT')) define('_SRV_WEBROOT', $root_dir);
if (!defined('_SRV_WEB')) define('_SRV_WEB',  $sys_dir);

if(isset($_POST['_dbPrefix']))
{
    define('DB_PREFIX', $_POST['_dbPrefix']);
}

// Check if license file exists
$lic_file = _SRV_WEBROOT . "lic" . DIRECTORY_SEPARATOR . "license.txt";

$mainFile = _SRV_WEBROOT . 'xtCore' . DS . 'main.php';
if (file_exists($mainFile)) {
    require_once $mainFile;
}

spl_autoload_register(function ($class)
{
    global $is_pro_version;

    if(is_null($is_pro_version))
    {
        if(file_exists(_SRV_WEBROOT._SRV_WEB_FRAMEWORK.'functions/read_license.php'))
        {
            $is_pro_version = false;

            require_once _SRV_WEBROOT . _SRV_WEB_FRAMEWORK . 'functions/read_license.php';
            $lic_info = getLicenseFileInfo(['versiontype']);
            if (is_array($lic_info))
            {
                if ($lic_info['versiontype'] == 'PRO')
                {
                    $is_pro_version = true;
                }
                elseif ($lic_info['versiontype'] != 'FREE')
                {
                    die(' - license error - lic21');
                }
            }
            else
            {
                die(' license error - lic22');
            }
        }
    }

    if($is_pro_version)
    {
        // framework pro

        // admin pro
        $class_file = _SRV_WEBROOT.'xtPro/'._SRV_WEB_FRAMEWORK.'admin/classes/class.' . $class . '.php';
        if (file_exists($class_file))
        {
            include_once $class_file;
        }
        else
        {
            // framework free
            $class_file = _SRV_WEBROOT . _SRV_WEB_FRAMEWORK . 'classes/class.' . $class . '.php';

            if (file_exists($class_file))
            {
                include_once $class_file;
            }
            else
            {
                // admin free
                $class_file = _SRV_WEBROOT._SRV_WEB_FRAMEWORK.'admin/classes/class.' . $class . '.php';
                if (file_exists($class_file))
                {
                    include_once $class_file;
                }
            }
        }

    }
    else if(!is_null($is_pro_version))
    {
        // framework free

        // admin free
        $class_file = _SRV_WEBROOT._SRV_WEB_FRAMEWORK.'admin/classes/class.' . $class . '.php';
        if (file_exists($class_file))
        {
            include_once $class_file;
        }

    }
});


if (!file_exists($lic_file)) {
    header("Location: " . _SRV_WEB . "xtWizard/no-license.php?licerr=lic111");
    exit;
}
else if(version_compare(_SYSTEM_VERSION, '6.1.0', '>=') && !defined('IS_UPDATE_VERSIONINFO'))
{
    $fc = file_get_contents($lic_file);
    if (preg_match('/versiontype: PRO/m', $fc, $matches) !== 1
        &&
        preg_match('/versiontype: FREE/m', $fc, $matches) !== 1
    )  {
        header("Location: " . _SRV_WEB . "xtWizard/no-license.php?licerr=lic112");
        exit;
    }
}

$templates_c = _SRV_WEBROOT . "templates_c";

if (!file_exists($templates_c)){
    mkdir ($templates_c);
}

if (!is_writable($templates_c)) {
    chmod($templates_c, 755);
    if (!is_writable($templates_c)) {
        die ('- Benoetige Schreibrechte auf templates_c -');
    }
}


if($_POST['doit'] == 'yes, do it')
{
    GLOBAL $ADODB_THROW_EXCEPTIONS;
    $ADODB_THROW_EXCEPTIONS = true;
    // Include core wizard file, with autoloader
    require_once dirname(__FILE__) . DS . 'lib' . DS . 'Loader.php';
    // Run the wizard
    Wizard::getInstance()->run();

    //   test der steuersätze

    $install_eu_rates = !empty($_POST['install_eu_rates']); // die checkbox im wizard
    $sel_country = Wizard::getInstance()->getDatabaseObject()->GetOne("SELECT config_value FROM ".DB_PREFIX."_config_1 WHERE config_key = '_STORE_COUNTRY'");       // das gewählte land im wizard
    $class_default = !empty($_POST['class_default']) ? $_POST['class_default'] : false;
    $class_reduced = !empty($_POST['class_reduced']) ? $_POST['class_reduced'] : false;
    $class_digital = false; //!empty($_POST['class_digital']) ? $_POST['class_digital'] : false;

    $sps = new StartPageScript();
    $sps->_taxSetupJuly2021(Wizard::getInstance()->getDatabaseObject(), $sel_country,  $install_eu_rates, DB_PREFIX.'_', false, $class_default, $class_reduced, $class_digital);

    echo 'done';
    die();
}

$countries = new countries();

$country_data = array();
foreach ($countries->countries_list as $c ) {
    $country = array();
    $country['code']=$c['countries_iso_code_2'];
    $country['name']=$c['countries_name'];
    $country_data[]=$country;
}

$tpl_s = '
<form action="ust-eu-oss.php" method="post">
    <input type="checkbox" name="install_eu_rates" checked="checked">Ja, ich verkaufe auch Waren in andere EU-Länder für mehr als 10.000 € jährlich. Dadurch gelten die neuen Regelungen ab 1. Juli 2021 an.<br>
    <input type="text" name="class_default" >Steuer-Klasse normal.<br>
    <input type="text" name="class_reduced" >Steuer-Klasse ermäßigt<br>
    <!-- input type="text" name="class_digital" >Steuer-Klasse digital<br -->
    <input type="submit" name="doit" value="yes, do it">
</form>';

$tpl = new Template();
$tpl->content_smarty = new Smarty();
$tpl->content_smarty->assign('store_countries', $country_data);
$html = $tpl->content_smarty->display('string:'.$tpl_s);
echo $html;



