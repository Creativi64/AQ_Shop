<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

global $db;
if(!defined('DB_STORAGE_ENGINE'))
{
    define('DB_STORAGE_ENGINE', 'innodb');
}

$rc = $db->Execute("SELECT * FROM ".TABLE_ADMIN_NAVIGATION." WHERE text= 'xt_master_slave' ");
if ($rc->RecordCount()==0){
	$db->Execute("INSERT INTO ".TABLE_ADMIN_NAVIGATION." (`pid` ,`text` ,`icon` ,`url_i` ,`url_d` ,`sortorder` ,`parent` ,`type` ,`navtype`, `iconCls`) VALUES (NULL , 'xt_master_slave', 'images/icons/building_link.png', '&plugin=xt_master_slave&gridHandle=xt_master_slavegridForm', 'adminHandler.php', '5000', 'shop', 'I', 'W','far fa-list-alt');");
}

$db->Execute("
CREATE TABLE IF NOT EXISTS ".DB_PREFIX."_plg_products_attributes (
  attributes_id INT NOT NULL auto_increment,
  attributes_parent INT NULL DEFAULT '0',
  attributes_model varchar(255) default NULL,
  attributes_image varchar(255) default NULL,
  attributes_color varchar(2048) default NULL,
  sort_order INT default '0',
  status TINYINT UNSIGNED default '1',
  attributes_templates_id INT NOT NULL,
  PRIMARY KEY  (attributes_id)
) ENGINE=".DB_STORAGE_ENGINE."  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
");

$db->Execute("
CREATE TABLE IF NOT EXISTS ".DB_PREFIX."_plg_products_attributes_description (
  attributes_id INT NOT NULL,
  language_code char(2) NOT NULL,
  attributes_name varchar(255) default NULL,
  attributes_desc text,
  PRIMARY KEY  (attributes_id,language_code)
) ENGINE=".DB_STORAGE_ENGINE." DEFAULT CHARSET=utf8;
");

$db->Execute("
CREATE TABLE IF NOT EXISTS ".DB_PREFIX."_plg_products_to_attributes (
  products_id INT NOT NULL,
  attributes_id INT NOT NULL,
  attributes_parent_id INT NOT NULL,
  PRIMARY KEY  (products_id,attributes_id),
  KEY attributes_id (attributes_id)
) ENGINE=".DB_STORAGE_ENGINE." DEFAULT CHARSET=utf8;
");

// check for index
$sql = "SELECT count(*) FROM information_schema.statistics 
  WHERE table_schema = ? AND table_name = ?  AND column_name = 'products_model'";
$c = $db->GetOne($sql, array(_SYSTEM_DATABASE_DATABASE, TABLE_PRODUCTS));

if($c==0)
{
    $db->Execute("ALTER TABLE ".TABLE_PRODUCTS." ADD INDEX `products_model` (`products_model` ASC)");
}

if (!$this->_FieldExists('products_master_flag', TABLE_PRODUCTS))
{
	$db->Execute("ALTER TABLE ".TABLE_PRODUCTS." ADD products_master_flag TINYINT UNSIGNED DEFAULT 0 AFTER products_model");
}

if (!$this->_FieldExists('products_master_model', TABLE_PRODUCTS))
{
    $db->Execute("ALTER TABLE ".TABLE_PRODUCTS." ADD products_master_model VARCHAR(255) CHARACTER SET utf8 NULL AFTER products_master_flag");
    $db->Execute("ALTER TABLE ".TABLE_PRODUCTS." ADD INDEX `products_master_model` (`products_master_model`);");
}
if (!$this->_FieldExists('products_master_slave_order', TABLE_PRODUCTS))
{
    $db->Execute("ALTER TABLE ".TABLE_PRODUCTS." ADD products_master_slave_order INT NOT NULL AFTER products_master_model");
}
if (!$this->_FieldExists('products_option_master_price', TABLE_PRODUCTS))
{
    $db->Execute("ALTER TABLE ".TABLE_PRODUCTS." ADD products_option_master_price VARCHAR(3) NULL AFTER products_master_model");
}
if (!$this->_FieldExists('products_option_list_template', TABLE_PRODUCTS))
{
    $db->Execute("ALTER TABLE ".TABLE_PRODUCTS." ADD products_option_list_template VARCHAR(255) NULL AFTER products_master_model");
}
if (!$this->_FieldExists('products_option_template', TABLE_PRODUCTS))
{
    $db->Execute("ALTER TABLE ".TABLE_PRODUCTS." ADD products_option_template VARCHAR(255) NULL AFTER products_master_model");
}
if (!$this->_FieldExists('products_description_from_master', TABLE_PRODUCTS))
{
    $db->Execute("ALTER TABLE ".TABLE_PRODUCTS." ADD products_description_from_master TINYINT UNSIGNED DEFAULT 2 NULL AFTER products_master_model");
}
if (!$this->_FieldExists('products_short_description_from_master', TABLE_PRODUCTS))
{
    $db->Execute("ALTER TABLE ".TABLE_PRODUCTS." ADD products_short_description_from_master TINYINT UNSIGNED DEFAULT 2 NULL AFTER products_master_model");
}
if (!$this->_FieldExists('products_keywords_from_master', TABLE_PRODUCTS))
{
    $db->Execute("ALTER TABLE ".TABLE_PRODUCTS." ADD products_keywords_from_master TINYINT UNSIGNED DEFAULT 2 AFTER products_master_model");
}
if (!$this->_FieldExists('ms_load_masters_main_img', TABLE_PRODUCTS))
{
    $db->Execute("ALTER TABLE ".TABLE_PRODUCTS." ADD ms_load_masters_main_img TINYINT UNSIGNED DEFAULT 2 AFTER products_master_model");
}
if (!$this->_FieldExists('load_mains_imgs', DB_PREFIX.'_products'))
{
    $db->Execute('ALTER TABLE ' . DB_PREFIX . '_products ADD COLUMN `load_masters_img` TINYINT UNSIGNED DEFAULT 2 AFTER ms_load_masters_main_img;');
}
if (!$this->_FieldExists('ms_load_masters_free_downloads', TABLE_PRODUCTS))
{
    $db->Execute("ALTER TABLE ".TABLE_PRODUCTS." ADD ms_load_masters_free_downloads TINYINT UNSIGNED DEFAULT 2 AFTER products_master_model");
}
if (!$this->_FieldExists('products_image_from_master', TABLE_PRODUCTS))
{
	$db->Execute("ALTER TABLE ".TABLE_PRODUCTS." ADD products_image_from_master TINYINT UNSIGNED DEFAULT 2 AFTER products_master_model");
}
if (!$this->_FieldExists('ms_filter_slave_list_hide_on_product', TABLE_PRODUCTS))
{
    $db->Execute("ALTER TABLE " . TABLE_PRODUCTS . " ADD ms_filter_slave_list_hide_on_product TINYINT UNSIGNED DEFAULT 2 AFTER products_master_model");
}
if (!$this->_FieldExists('ms_filter_slave_list', TABLE_PRODUCTS))
{
    $db->Execute("ALTER TABLE " . TABLE_PRODUCTS . " ADD ms_filter_slave_list TINYINT UNSIGNED DEFAULT 2 AFTER products_master_model");
}
if (!$this->_FieldExists('ms_show_slave_list', TABLE_PRODUCTS))
{
    $db->Execute("ALTER TABLE ".TABLE_PRODUCTS." ADD ms_show_slave_list TINYINT UNSIGNED DEFAULT 2 AFTER products_master_model");
}
if (!$this->_FieldExists('ms_open_first_slave', TABLE_PRODUCTS))
{
    $db->Execute("ALTER TABLE ".TABLE_PRODUCTS." ADD ms_open_first_slave TINYINT UNSIGNED DEFAULT 2 AFTER products_master_model");
}


$db->Execute("
CREATE TABLE IF NOT EXISTS ".DB_PREFIX."_plg_products_attributes_templates (
  attributes_templates_id INT NOT NULL auto_increment,
  attributes_templates_name varchar(255) default NULL,
  PRIMARY KEY  (attributes_templates_id)
) ENGINE=".DB_STORAGE_ENGINE."  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
");

$rc = $db->GetOne("SELECT attributes_templates_id FROM ".DB_PREFIX."_plg_products_attributes_templates WHERE attributes_templates_name='select'");

if ((int)$rc == 0)
{
	$db->Execute("
	INSERT INTO ".DB_PREFIX."_plg_products_attributes_templates (`attributes_templates_id`, `attributes_templates_name`) VALUES
	(1, 'select'),
	(2, 'images');
	");
}

$db->Execute("DROP TABLE IF EXISTS ".DB_PREFIX."_tmp_products");
$db->Execute("DROP TABLE IF EXISTS ".DB_PREFIX."_tmp_plg_products_to_attributes");

$db->Execute("
CREATE TABLE IF NOT EXISTS ".DB_PREFIX."_tmp_plg_products_to_attributes (
  `products_id` INT NOT NULL,
  `attributes_id` INT NOT NULL,
  `attributes_parent_id` INT NOT NULL,
  `main` TINYINT UNSIGNED NOT NULL,
  PRIMARY KEY (`products_id`,`attributes_id`),
  KEY `attributes_id` (`attributes_id`)
) ENGINE=".DB_STORAGE_ENGINE." DEFAULT CHARSET=utf8;
");

$valExists = $db->GetOne("SELECT 1 FROM ".TABLE_CONFIGURATION." WHERE config_key='_SYSTEM_ADMIN_PAGE_SIZE_SLAVE_PRODUCT' ");
if (!$valExists)
    $db->Execute("INSERT INTO ".TABLE_CONFIGURATION." (`config_key`, `config_value`, `group_id`, `sort_order`, `type`, `url`) VALUES ('_SYSTEM_ADMIN_PAGE_SIZE_SLAVE_PRODUCT',       '100', 25, 30, NULL, NULL);");

if (!defined('XT_WIZARD_STARTED'))
{
    $output .= "<div style='border:1px solid #009900; background:#BDFFA9;padding:10px;'>";
    $output .=  'Bitte aktivieren Sie nun das Plugin und laden Sie dann das Browser-Fenster neu.<br />Please activate plugin and then reload browser window';
    $output .=  "</div>";
}

if (defined('XT_WIZARD_STARTED') && XT_WIZARD_STARTED === true)
{
    $installDemoData = getParamDefault('demodata', false);
    if($installDemoData)
    {
        WizardLogger::getInstance()->log('[' . date('H:i:s') . '] masterSlave: starting demo data install');
        try {
            WizardLogger::getInstance()->log('[' . date('H:i:s') . '] masterSlave: -- installing attributes ');
            _msDemoInstallSQL('demo_data_attributes.sql');
            WizardLogger::getInstance()->log('[' . date('H:i:s') . '] masterSlave: -- installing products');
            _msDemoInstallSQL('demo_data.sql');
            global $language;
            $languages = $language->_getLanguageList('admin');
            foreach($languages as $lng)
            {
                $code = $lng['id'];
                if($code=='de' || $code=='en')
                {
                    WizardLogger::getInstance()->log('[' . date('H:i:s') . '] masterSlave: -- installing lang data '.$code);
                    _msDemoInstallSQL('demo_data.sql', $code);
                }
            }
            $db->Execute("UPDATE ".TABLE_PRODUCTS." SET products_master_flag=1 WHERE products_id=16");
        }
        catch(Exception $e)
        {
            WizardLogger::getInstance()->log('[' . date('H:i:s') . '] masterSlave: demo data install exception: '.$e->getMessage());
        }
        finally
        {
            WizardLogger::getInstance()->log('[' . date('H:i:s') . '] masterSlave: demo data install finished');
        }
    }
}

function _msDemoInstallSQL($filename, $language_code = '')
{
    global $db;
    $idb = $db;
    $prefix = DB_PREFIX.'_';
    $query = '';
    // open sql

    if ($language_code == '')
    {
        $filename = _SRV_WEBROOT . _SRV_WEB_PLUGINS . 'xt_master_slave/installer/demodata/' . $filename;
    }
    else
    {
        $filename = _SRV_WEBROOT . _SRV_WEB_PLUGINS . 'xt_master_slave/installer/demodata/' . $language_code . '/' . $filename;
    }


    $sql_content = _msDemoGetFileContent($filename);
    // replace windows linefeeds
    $sql_content = str_replace("\r\n", "\n", $sql_content);
    $queries = array();
    $chars = strlen($sql_content);
    for ($i = 0; $i < $chars; $i++)
    {
        // check if char is ; and next \n
        if ($sql_content[$i] == ';' && $sql_content[$i + 1] == "\n")
        {
            $query .= $sql_content[$i];
            $queries[] = $query;
            $query = '';
            $i++;
        }
        else
        {
            if ($sql_content[$i] == '-' && $sql_content[$i + 1] == '-')
            {
                // skip to next \n
                for ($ii = $i; $ii < $chars; $ii++)
                {

                    if ($sql_content[$ii] == "\n")
                    {
                        break;
                    }
                    else
                    {
                        $i++;
                    }

                }
            }
            else
            {
                if (!isset($query))
                {
                    $query = '';
                }
                $query .= $sql_content[$i];
            }
        }
    }

    foreach ($queries as $key => $val)
    {
        $query = trim($val);
        $query = str_replace('##_', $prefix, $query);

        // ok, now search vor OTHER INSERT INTO statements, and break them up
        if (substr($query, 0, 6) == 'INSERT')
        {
            $check_qry = substr($query, 7);

            if (strstr($check_qry, 'INSERT'))
            {
                $qry = explode('INSERT', $check_qry);
                foreach ($qry as $k => $v)
                {
                    $queries[] = 'INSERT ' . $v;
                }
                unset ($queries[$key]);
            }
            else
            {
                $queries[$key] = $query;
            }
        }
        else
        {
            $queries[$key] = $query;
        }
    }

    foreach ($queries as $key => $val)
    {
        try
        {
            $idb->Execute($val);
        } catch (exception $e)
        {
            // echo $val;
            throw $e;
        }
    }
    return -1;
}

/**
 * Read file and return its conetents
 * @param unknown $filename
 * @return string
 */
function _msDemoGetFileContent($filename)
{
    $handle = fopen($filename, 'rb');
    $content = fread($handle, filesize($filename));
    fclose($handle);
    return $content;
}
