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

if(!class_exists('GuzzleHttp\Client'))
{
    require_once _SRV_WEBROOT. 'xtWizard/lib/library_optional/vendor/autoload.php';
}

require_once _SRV_WEBROOT. 'xtWizard/lib/library_required/vendor/autoload.php';

require_once _SRV_WEBROOT. 'xtWizard/lib/xt_dump_export.php';

use GuzzleHttp\Exception\GuzzleException;
use Rah\Danpu\Dump;

if (!defined('_APS_INSTALL_PACKAGE')) define('_APS_INSTALL_PACKAGE', false);

class StartPageScript extends ExecutableScript {
	
	const MYSQL_MIN_VERSION = "5.6";

    public function __construct()
    {
        set_exception_handler(array($this, 'handleException'));
    }

    /**
     * @param $ex Exception
     */
    public function handleException($ex)
    {
        $time = date('Y-m-d h:i:s');

        WizardLogger::getInstance()->log("[$time] ".print_r($ex, true));

        $r = [
            "HasNext" => true,
            "Offset" => $this->getNextOffset(),
            "CurrentMessage" => "[$time][<strong class=\"msg-error font-weight-bold\">ERROR (".__CLASS__.")</strong>] ".$ex->getMessage(). "<br/>",
            "LogMessages" => "[$time][<strong class=\"msg-error font-weight-bold\">ERROR (".__CLASS__.")</strong>] ".$ex->getMessage(). "<br/><pre>".$ex->getTraceAsString()."</pre>",
            "Progress" => '',
            "ExtraParams" =>  [],
            "NextUrl" =>  $this->getNextPageUrl(),
            "hasError" => true

        ];
        echo json_encode($r);
        die();
    }

    /**
	 * (non-PHPdoc)
	 * @see IExecutableScript::getWizardType()
	 */
	public function getWizardType() {
		return null;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see IExecutableScript::getMinimumShopVersion()
	 */
	public function getMinimumShopVersion() {
		return null;
	}
	
    public function getTargetShopVersion() {
        return '6.2.0';
    }

	/**
	 * (non-PHPdoc)
	 * @see IExecutableScript::getUniqueId()
	 */
	public function getUniqueId() {
		return get_class($this);
	}

    /**
     * @param WizardPage $page
     * @throws GuzzleException
     * @throws WizardException
     */
	public function installPage(WizardPage $page)
    {

        // notification
        $this->pageNotification('system');

        // directory
        $check_writeable = array();
        $check_writeable[] = _SRV_WEBROOT . 'cache';
        $check_writeable[] = _SRV_WEBROOT . 'cache/adodb';
        $check_writeable[] = _SRV_WEBROOT . 'export';
        $check_writeable[] = _SRV_WEBROOT . 'plugin_cache';
        $check_writeable[] = _SRV_WEBROOT . 'templates_c';
        $check_writeable[] = _SRV_WEBROOT . 'xtLogs';
        $check_writeable[] = _SRV_WEBROOT . 'xtWizard/logs';

        if (_SYSTEM_VERSION != '4.2.00')
        {
            $check_writeable[] = _SRV_WEBROOT . 'cronjobs';
        }

        // files
        $check_writeable[] = _SRV_WEBROOT . 'conf/config.php';

        // media
        $check_writeable[] = _SRV_WEBROOT . 'media/files';
        $check_writeable[] = _SRV_WEBROOT . 'media/lang_downloads';
        $check_writeable[] = _SRV_WEBROOT . 'media/files_public';
        $check_writeable[] = _SRV_WEBROOT . 'media/images/icon';
        $check_writeable[] = _SRV_WEBROOT . 'media/images/info';
        $check_writeable[] = _SRV_WEBROOT . 'media/images/org';
        $check_writeable[] = _SRV_WEBROOT . 'media/images/thumb';

        // template
        if (Wizard::getInstance()->isSystemInstalled() === false)
        {
            $check_writeable[] = _SRV_WEBROOT . 'templates/xt_responsive/less/Variables.less';
            $check_writeable[] = _SRV_WEBROOT . 'templates/xt_responsive/less';
        }
        	
		// plugins
		if (file_exists(_SRV_WEBROOT.'plugins/magnalister'))
			$check_writeable[] = _SRV_WEBROOT.'plugins/magnalister';

		$permissions = array();
		foreach ($check_writeable as $key => $val) {
			$class = 'success';
			if (!is_writable($val))
			{
				$class='error';
				$permissions[]=array('path'=>$val,'class'=>$class);
			}
		
		}

		
		if (count($permissions) > 0) {
			$page->assignTemplateVar('permissions', $permissions);
			$page->assignTemplateVar('permissions_failed','1');
		}
		else {
            $page->assignTemplateVar('permissions', array());
            $page->assignTemplateVar('permissions_failed','0');
        }
		
		$setting = $this->_checkServerSettings();
		$page->assignTemplateVar('settings',$setting);
		$page->assignTemplateVar('lang', Wizard::getInstance()->getCurrentLanguage());
		// Set the correct wizard type
		Wizard::getInstance()->setWizardType(Wizard::WIZARD_TYPE_INSTALL);
		$page->setTemplate('templates/install_checks.html')->assignTemplateVar('show_language', true);
	}

    /**
     * License page
     * @param WizardPage $page
     * @throws GuzzleException
     * @throws WizardException
     */
	public function licensePage(WizardPage $page) {
        $page->assignTemplateVar('error', false);
		if (isset($_POST['send'])) {
			if (!isset($_POST['LicenseRead'])) {
				$page->assignTemplateVar('error', constant('_ERROR_LICENSE'));
			} else {
				$url = Wizard::getInstance()->buildUrl(array(
					Wizard::PARAM_SCRIPT => 'StartPageScript',
					Wizard::PARAM_PAGE => $page->getPageId(),
					Wizard::PARAM_NEXT => 1,
				));
				// Redirect to info page
				Wizard::getInstance()->redirect($url);
			}
		}
		
		if (isset($_GET['error'])) {
			$page->assignTemplateVar('error',constant('_ERROR_LICENSE'));
		}

        // notification
        $this->pageNotification('license');
		
		$license = 'LICENSE_'.Wizard::getInstance()->getCurrentLanguage().'.txt';
		if (!file_exists($license))
			$license = 'LICENSE_' . Wizard::WIZARD_DEFAULT_LANGUAGE .'.txt';
		$text = file_get_contents(ROOT_DIR_PATH . 'licenses' . DS . 'install' . DS . $license);
		$text = str_replace("\n",'<br />',$text);
		$page->assignTemplateVar('LICENSE_CONTENT',$text);
		$page->setTemplate('templates/license.html');
	}

    /**
     * Render language page
     * @param WizardPage $page
     * @return void
     * @throws GuzzleException
     * @throws WizardException
     */
	public function languagePage(WizardPage $page) {
		// Set persist params for ajax action url
		Wizard::getInstance()
		->addPersistParam('lng')
		->addPersistParam('sel_country')
		->addPersistParam('sel_language')
		->addPersistParam('demodata')
        ->addPersistParam('install_eu_rates');
		
		$lng = getParamDefault('lng', '');
		$languages = explode('.',$lng);

        // notification
        $this->pageNotification('language');
		
		foreach ($languages as $lng_var) {
		// Controller command for installing default content
			$this->addControllerCommand(ControllerCommand::factory()
					->setAction(function(ControllerCommand $command) use ($lng_var) {
							
						$lng = getParamDefault('lng', '');
						$languages = explode('.',$lng);
							
						$sel_country = getParamDefault('sel_country', '');
						$sel_language = getParamDefault('sel_language', '');
						$demodata = getParamDefault('demodata', '');
						$install_prefix = DB_PREFIX.'_';

						/** @var Exception|int $res */
						$res = $command->getExecutableScript()->_installSQL(Wizard::getInstance()->getDatabaseObject(), 'default_content.sql', $install_prefix, $lng_var);
							
						if ($res != '-1' ) {
						    throw new WizardException($res->getMessage() .' | Install default content sql '.$lng_var, WizardException::ERROR_LEVEL_FATAL);
						}
							
						return true;
					})
					->setCondition(function(ControllerCommand $command){
						$lng = getParamDefault('lng', '');
						$languages = explode('.',$lng);
						return (count($languages)>0 && $languages[0]!='');
					})
					->setDescription(constant('TEXT_INSTALLING_DEFAULT_CONTENT') . ' [' . strtoupper($lng_var) . '] ')
					->setAbortOnError(true)
			);
		}
		
		foreach ($languages as $lng_var) {
			// Controller command for installing system content
			$this->addControllerCommand(ControllerCommand::factory()
					->setAction(function(ControllerCommand $command) use($lng_var) {
			
						$lng = $_GET['lng'];
						$languages = explode('.',$lng);
			
						$sel_country = getParamDefault('sel_country', '');
						$sel_language = getParamDefault('sel_language', '');
						$demodata = getParamDefault('demodata', '');
						$install_prefix = DB_PREFIX.'_';

                        /** @var Exception|int $res */
						$res = $command->getExecutableScript()->_installSQL(Wizard::getInstance()->getDatabaseObject(), 'system_content.sql', $install_prefix, $lng_var);

						if ($res != '-1') {
						    throw new WizardException($res->getMessage() .' | Install default content sql '.$lng_var, WizardException::ERROR_LEVEL_FATAL);
						}
			
						return true;
					})
					->setCondition(function(ControllerCommand $command){
						$lng = getParamDefault('lng', '');
						$languages = explode('.',$lng);
						return (count($languages)>0 && $languages[0]!='');
					})
					->setDescription(constant('TEXT_INSTALLING_SYSTEM_CONTENT') . ' [' . strtoupper($lng_var) . '] ')
					->setAbortOnError(true)
			);
		}
		
		require_once _SRV_WEBROOT . 'xtFramework/classes/class.language_sync.php';
		// Download latest translations
		foreach ($languages as $lng_var) {
			// Controller command for installing language
			$this->addControllerCommand(ControllerCommand::factory()
					->setAction(function(ControllerCommand $command) use($lng_var) {
						
						if (function_exists('curl_version')) {
							$lang_sync = new language_sync();
							$lang_sync->_set(array('language' => $lng_var));
						}
							
						return true;
					})
					->setDescription(constant('TEXT_DOWNLOADING_LATEST_TRANSLATIONS') . ' [' . strtoupper($lng_var) . '] ')
					->setAbortOnError(false)
			);
		}

		foreach ($languages as $lng_var) {
			// Controller command for installing language
			$this->addControllerCommand(ControllerCommand::factory()
				->setAction(function(ControllerCommand $command) use($lng_var) {

					$lng = getParamDefault('lng', '');
					$languages = explode('.',$lng);

					$sel_country = getParamDefault('sel_country', '');
					$sel_language = getParamDefault('sel_language', '');
						$demodata = getParamDefault('demodata', '');
					$install_prefix = DB_PREFIX.'_';

                    /** @var Exception|int $res */
					$res = $command->getExecutableScript()->_importLang(Wizard::getInstance()->getDatabaseObject(), $lng_var, $install_prefix, $command);

					if ($res != '-1' ) {
						throw new WizardException($res->getMessage().' | Install default content sql '.$lng_var, WizardException::ERROR_LEVEL_FATAL);
					}

					// update TEXT_CAT_STORE1 as long it is still in language server for 4.2 compatibility
					global $store_handler;
					$idb = Wizard::getInstance()->getDatabaseObject();
					$store_name = $store_handler->getStoreName(1);
					$rs = $idb->GetOne("SELECT 1 FROM ".$install_prefix."language_content WHERE language_key='TEXT_CAT_STORE1' and class='admin' and language_code=?", array(strtolower($lng_var)));
					if ($rs){
						$idb->Execute("UPDATE ".$install_prefix."language_content SET language_value=? WHERE language_key='TEXT_CAT_STORE1' and class='admin' and language_code=?", array($store_name, strtolower($lng_var)));
					}else {
						$insert['translated']=1;
						$insert['language_code']=strtolower($lng_var);
						$insert['language_key']='TEXT_CAT_STORE1';
						$insert['language_value']=$store_name;
						$insert['class']='admin';
						$idb->AutoExecute($install_prefix.'language_content',$insert);
					}

					return true;
				})
				->setCondition(function(ControllerCommand $command){
					$lng = getParamDefault('lng', '');
					$languages = explode('.',$lng);
					return (count($languages)>0 && $languages[0]!='');
				})
				->setDescription(constant('TEXT_INSTALLING_LANGUAGE_CONTENT') . ' [' . strtoupper($lng_var) . '] ')
				->setAbortOnError(true)
			);
		}

		$demodata = getParamDefault('demodata', 0);
		if($demodata == 1)
		{
			foreach ($languages as $lng_var)
			{
				// Controller command for installing demo data
				$this->addControllerCommand(ControllerCommand::factory()
					->setAction(function (ControllerCommand $command) use ($lng_var)
					{

						$lng = getParamDefault('lng', '');
						$languages = explode('.', $lng);

						$sel_country = getParamDefault('sel_country', '');
						$sel_language = getParamDefault('sel_language', '');
						$demodata = getParamDefault('demodata', '');
						$install_prefix = DB_PREFIX . '_';

                        /** @var Exception|int $res */
						$res = $command->getExecutableScript()->_installSQL(Wizard::getInstance()->getDatabaseObject(), 'demo_data.sql', $install_prefix, $lng_var);

						if ($res != '-1')
						{
							throw new WizardException($res->getMessage().' | Install default content sql ' . $lng_var, WizardException::ERROR_LEVEL_FATAL);
						}

						return true;
					})
					->setCondition(function (ControllerCommand $command)
					{
						$lng = getParamDefault('lng', '');
						$languages = explode('.', $lng);
						return (count($languages) > 0 && $languages[0] != '');
					})
					->setDescription(constant('TEXT_INSERTING_DEMO_DATA') . ' [' . strtoupper($lng_var) . '] ')
					->setAbortOnError(true)
				);
			}
		}

        // Controller command for activating store country
        $this->addControllerCommand(ControllerCommand::factory()
            ->setAction(function(ControllerCommand $command){

                $sel_country = getParamDefault('sel_country', '');
                $install_prefix = DB_PREFIX.'_';

                /** @var Exception|int $res */
                $res = $command->getExecutableScript()->_activateStoreCountry(Wizard::getInstance()->getDatabaseObject(), $sel_country, $install_prefix);
                if ($res != '-1' ) {
                    throw new WizardException($res->getMessage().' |  Activate store country ', WizardException::ERROR_LEVEL_FATAL);
                }

                return true;
            })
            ->setDescription(constant('TEXT_ACTIVITING_COUNTRIES'))
            ->setAbortOnError(true)
        );

        // Controller command for activating all eu countries
        $this->addControllerCommand(ControllerCommand::factory()
            ->setAction(function(ControllerCommand $command){

                $eu_countries = array(
                    'AT', 'BE', 'HR', 'BG', 'CY', 'CZ', 'DK', 'EE', 'FI', 'FR', 'DE', 'GR', 'HU', 'IE',
                    'IT', 'LV', 'LT', 'LU', 'MT', 'NL', 'PL', 'PT', 'RO', 'SK', 'SI', 'ES', 'SE', 'GB'
                );
                $install_prefix = DB_PREFIX.'_';

                /** @var Exception|int $res */
                $res = $command->getExecutableScript()->_activateCountries(Wizard::getInstance()->getDatabaseObject(), $eu_countries, $install_prefix);
                if ($res != '-1' ) {
                    throw new WizardException($res->getMessage().' |  Activate EU countries ', WizardException::ERROR_LEVEL_FATAL);
                }

                return true;
            })
            ->setDescription(constant('TEXT_ACTIVITING_COUNTRIES'). ' EU')
            ->setAbortOnError(true)
        );
		
		// Controller command for activating language
		$this->addControllerCommand(ControllerCommand::factory()
				->setAction(function(ControllerCommand $command){
		
					$lng = getParamDefault('lng', '');
					$languages = explode('.',$lng);
		
					$sel_country = getParamDefault('sel_country', '');
					$sel_language = getParamDefault('sel_language', '');
					$demodata = getParamDefault('demodata', '');
					$install_prefix = DB_PREFIX.'_';

                    /** @var Exception|int $res */
					$res = $command->getExecutableScript()->_activateLanguage(Wizard::getInstance()->getDatabaseObject(), $sel_language, $install_prefix);
					if ($res != '-1' ) {
					throw new WizardException($res->getMessage().' |  Install default content sql '.$sel_language, WizardException::ERROR_LEVEL_FATAL);
					}
		
					return true;
				})
				->setCondition(function(ControllerCommand $command){
					$lng = getParamDefault('lng', '');
					$languages = explode('.',$lng);
					return (count($languages)>0 && $languages[0]!='');
				})
				->setDescription(constant('TEXT_ACTIVITING_LANGUAGE'))
				->setAbortOnError(true)
		);
		
		// Controller command for taxt setup
		$this->addControllerCommand(ControllerCommand::factory()
				->setAction(function(ControllerCommand $command){
		
					$lng = getParamDefault('lng', '');
					$languages = explode('.',$lng);
		
					$sel_country = getParamDefault('sel_country', '');
					$sel_language = getParamDefault('sel_language', '');
					$demodata = getParamDefault('demodata', '');
					$install_prefix = DB_PREFIX.'_';

                    $install_eu_rates = getParamDefault('install_eu_rates', '');

                    /** @var Exception|int $res */
					$res = $command->getExecutableScript()->_taxSetupJuly2021(Wizard::getInstance()->getDatabaseObject(), $sel_country, $install_eu_rates,  $install_prefix);
					if ($res != '-1' ) {
					throw new WizardException($res->getMessage().' |  Install default content sql '.$sel_language, WizardException::ERROR_LEVEL_FATAL);
					}
		
					return true;
				})
				->setCondition(function(ControllerCommand $command){
					$lng = getParamDefault('lng', '');
					$languages = explode('.',$lng);
					return (count($languages)>0 && $languages[0]!='');
				})
				->setDescription(constant('TEXT_SETTING_UP_TAXES'))
				->setAbortOnError(true)
		);
		
		// Display the page
		$this->execAsyncAction($page);
	}

    /**
     * Plugins page
     * @param WizardPage $page
     * @return void
     * @throws GuzzleException
     * @throws WizardException
     */
	public function pluginsPage(WizardPage $page)
    {
        global $is_pro_version;

		// Add persis param
		Wizard::getInstance()->addPersistParam('demodata');

        // notification
        $this->pageNotification('plugins');
		
		require_once _SRV_WEBROOT . 'xtFramework/classes/class.plugin.php';
		$default_plugins = array();
        if (file_exists(_SRV_WEBROOT . 'xtWizard/default_plugins.php'))
        {
            include_once _SRV_WEBROOT . 'xtWizard/default_plugins.php';
        }
        else
        {
            // DEFAULT
            include_once _SRV_WEBROOT . 'xtWizard/lib/plugins_default.php';

            if($is_pro_version)
            {
                // PRO
                include_once _SRV_WEBROOT . 'xtWizard/lib/plugins_pro.php';
            }
            else {
                // FREE
                include_once _SRV_WEBROOT . 'xtWizard/lib/plugins_free.php';
            }

        }
        if (file_exists(_SRV_WEBROOT . 'xtWizard/custom_plugins.php'))
        {
            include_once _SRV_WEBROOT . 'xtWizard/custom_plugins.php';
        }
		
		// Add controller command for each plugin
		foreach ($default_plugins as $id => $data) {
			// Controller command for installing plugins
			$this->addControllerCommand(ControllerCommand::factory()
					->setAction(function(ControllerCommand $command) use ($id, $default_plugins){
						$plugin = new plugin();
						$plugin->setPosition('installer');

						$command->setAbortOnError(false);
						
						$msg = array();
						$xml = _SRV_WEBROOT.'plugins/'.$default_plugins[$id]['plugin'].'/installer/'.$default_plugins[$id]['plugin'].'.xml';
						if (file_exists($xml)) {
						try
						{
							$plugin->InstallPlugin($xml);
						}
						catch(Exception $e)
						{
							$msg[] = '<span class="error">Error installing ' . $default_plugins[$id]['plugin'].'</span>: '.$e->getMessage();
							throw new WizardException($default_plugins[$id]['plugin']. ': '.$e->getMessage());
						}
							// TODO activate plugin
							if ($default_plugins[$id]['active']=='1') {
								Wizard::getInstance()->getDatabaseObject()->Execute("UPDATE ".TABLE_PLUGIN_PRODUCTS." SET plugin_status='1' WHERE code='".$default_plugins[$id]['plugin']."'");
								
								Wizard::getInstance()->getDatabaseObject()->Execute("UPDATE ".TABLE_PLUGIN_CODE." SET plugin_status='1' WHERE plugin_code='".$default_plugins[$id]['plugin']."'");
								
								// Demo data
								if ($default_plugins[$id]['plugin'] == 'xt_startpage_products' && getParamDefault('demodata', false)) {
								Wizard::getInstance()->getDatabaseObject()->Execute("TRUNCATE " . DB_PREFIX . "_startpage_products");
									Wizard::getInstance()->getDatabaseObject()->Execute("INSERT INTO " . DB_PREFIX . "_startpage_products (shop_id, products_id, startpage_products_sort) VALUES (1,10, 1)");
									Wizard::getInstance()->getDatabaseObject()->Execute("INSERT INTO " . DB_PREFIX . "_startpage_products (shop_id, products_id, startpage_products_sort) VALUES (1,9, 2)");
									Wizard::getInstance()->getDatabaseObject()->Execute("INSERT INTO " . DB_PREFIX . "_startpage_products (shop_id, products_id, startpage_products_sort) VALUES (1,8, 3)");
									Wizard::getInstance()->getDatabaseObject()->Execute("INSERT INTO " . DB_PREFIX . "_startpage_products (shop_id, products_id, startpage_products_sort) VALUES (1,4, 4)");
									Wizard::getInstance()->getDatabaseObject()->Execute("INSERT INTO " . DB_PREFIX . "_startpage_products (shop_id, products_id, startpage_products_sort) VALUES (1,12, 5)");
									Wizard::getInstance()->getDatabaseObject()->Execute("INSERT INTO " . DB_PREFIX . "_startpage_products (shop_id, products_id, startpage_products_sort) VALUES (1,13, 6)");
									Wizard::getInstance()->getDatabaseObject()->Execute("INSERT INTO " . DB_PREFIX . "_startpage_products (shop_id, products_id, startpage_products_sort) VALUES (1,6, 7)");
									Wizard::getInstance()->getDatabaseObject()->Execute("INSERT INTO " . DB_PREFIX . "_startpage_products (shop_id, products_id, startpage_products_sort) VALUES (1,5, 8)");
									Wizard::getInstance()->getDatabaseObject()->Execute("INSERT INTO " . DB_PREFIX . "_startpage_products (shop_id, products_id, startpage_products_sort) VALUES (1,1, 9)");
									Wizard::getInstance()->getDatabaseObject()->Execute("INSERT INTO " . DB_PREFIX . "_startpage_products (shop_id, products_id, startpage_products_sort) VALUES (1,2, 10)");
									Wizard::getInstance()->getDatabaseObject()->Execute("INSERT INTO " . DB_PREFIX . "_startpage_products (shop_id, products_id, startpage_products_sort) VALUES (1,3, 11)");
									Wizard::getInstance()->getDatabaseObject()->Execute("INSERT INTO " . DB_PREFIX . "_startpage_products (shop_id, products_id, startpage_products_sort) VALUES (1,11, 12)");
									
								}
								// demo data
								if ($default_plugins[$id]['plugin'] == 'xt_cross_selling' && getParamDefault('demodata', false)) {
								Wizard::getInstance()->getDatabaseObject()->Execute("TRUNCATE " . DB_PREFIX . "_products_cross_sell");
									$min_pID = 1;
									$max_pID = 13;
									$pIdRange=range($min_pID,$max_pID);
									foreach ($pIdRange as $number) {
										
										$cross_sell = array_rand($pIdRange,6);
										foreach ($cross_sell as $crossId) {
											if ($crossId!=$number)
												Wizard::getInstance()->getDatabaseObject()->Execute("INSERT INTO " . DB_PREFIX . "_products_cross_sell (products_id,products_id_cross_sell) VALUES (".$number.",".$crossId.")");
										}
										
									}
									
								}
								// demo data
                                if ($default_plugins[$id]['plugin'] == 'xt_prepayment' && getParamDefault('demodata', false)) {
                                    Wizard::getInstance()->getDatabaseObject()->Execute("UPDATE " . DB_PREFIX . "_payment SET status = 1 WHERE payment_code = 'xt_prepayment'");
                                }
								
							}
							$msg[] = 'Installed ' . $default_plugins[$id]['plugin'];
						}
						
						
						$command->setErrorMessage(join("<br/>", $msg));
					})
					->setCondition(function(ControllerCommand $command){
						return true;
					})
					->setDescription(constant('TEXT_INSTALLING_PLUGINS').' ['.$data["plugin"].']')
					->setAbortOnError(true)
			);
		}
		
		// Display the page
		$this->execAsyncAction($page);
	}

    /**
     * Shop owner page
     * @param WizardPage $page
     * @throws GuzzleException
     * @throws WizardException
     */
	public function shopownerPage(WizardPage $page) {
        // notification
        $this->pageNotification('owner');
        $fields = array('store_name','ceo', 'company', 'streetaddress', 'city', 'zip', 'vatid','registerid','register_court', 'country', 'telephone', 'fax', 'email_from', 'email_to');
        foreach ($fields as $key)
        {
            $page->assignTemplateVar($key, '');
        }
        $page->assignTemplateVar('error', []);

		if (Wizard::getInstance()->isPost()) {
			$idb = Wizard::getInstance()->getDatabaseObject();
			$_error = array();

			foreach ($fields as $key)
			{
				$page->assignTemplateVar($key, $_POST[$key]);
		
				// Check required fields
				if (in_array($key, array('store_name', 'company', 'ceo', 'streetaddress', 'city', 'zip', 'email_from', 'email_to'), TRUE) && empty($_POST[$key]))
				{
		
					if ($key === 'streetaddress') {
						$_error[] = array('text' => constant('_ERROR_STREET_ADDRESS'));
					}else{
						$_error[] = array('text' => constant('_ERROR_'.strtoupper($key)));
					}
				}
				
				if ($key == 'email_from' || !empty($_POST['email_from'])) {
					if (false === filter_var ($_POST['email_from'], FILTER_VALIDATE_EMAIL)) {
						$_error[] = array('text' => constant('_ERROR_'.strtoupper($key)));
					}
				}
				
				if ($key == 'email_to' || !empty($_POST['email_to'])) {
					if (false === filter_var ($_POST['email_to'], FILTER_VALIDATE_EMAIL)) {
						$_error[] = array('text' => constant('_ERROR_'.strtoupper($key)));
					}
				}
			}
		
			if (empty($_error))
			{
				foreach ($fields as $key)
				{
					if (strpos($key, 'email', 0) === 0) continue;
					elseif ($key === 'store_name')
                    {
					    $config_key = '_STORE_NAME';
					    $sql = "UPDATE ".TABLE_CONFIGURATION_LANG_MULTI." SET language_value = '".addslashes(trim($_POST[$key]))."' WHERE config_key = '".$config_key."'";
                        $idb->Execute($sql);
					    continue;
                    }
					else $config_key = '_STORE_SHOPOWNER_'.strtoupper($key);
		
                    $sql = "UPDATE ".TABLE_CONFIGURATION_MULTI."1 SET config_value = '".addslashes(trim($_POST[$key]))."' WHERE config_key = '".$config_key."'";
                    $idb->Execute($sql);
				}
		
                $sql = "UPDATE ".TABLE_CONFIGURATION_MULTI."1 SET config_value = ? WHERE config_key = '_STORE_CONTACT_EMAIL'";
                $idb->Execute($sql,array($_POST['email_to']));

                // set default email addresses
                $sql = "UPDATE ".TABLE_MAIL_TEMPLATES." SET email_from = '".addslashes(trim($_POST['email_from']))."', email_reply='".addslashes(trim($_POST['email_from']))."'";
                $idb->Execute($sql);
                $sql = "UPDATE ".TABLE_MAIL_TEMPLATES." SET email_from_name = '".addslashes(trim($_POST['company']))."', email_reply_name='".addslashes(trim($_POST['company']))."'";
                $idb->Execute($sql);
                $sql = "UPDATE ".TABLE_MAIL_TEMPLATES." SET email_from = '".addslashes(trim($_POST['email_to']))."', email_reply='".addslashes(trim($_POST['email_to']))."'  WHERE tpl_id = 6";
                $idb->Execute($sql);
		
				// Add email footers
				$languages = $this->_getLanguageList(Wizard::getInstance()->getDatabaseObject(), '', '', DB_PREFIX.'_');
				foreach ($languages as $lang)
				{
					$ef_text = $_POST['company']."\n"
							.$_POST['streetaddress']."\n"
									.$_POST['zip'].' '.$_POST['city']."\n"
											.'E-mail: '.$_POST['email_to'];
		
					if ( ! empty($_POST['vatid']))
					{
					    /** @var ADORecordSet $rs */
						$rs = $idb->Execute("SELECT `language_value` FROM `".TABLE_LANGUAGE_CONTENT."` WHERE `language_code` = '".$lang['code']."' AND `language_key` = 'TEXT_VAT_ID' AND `class` IN('store', 'both')");
						$_vat_id_translation = ($rs->RecordCount() == 0) ? '' : $rs->fields['language_value'].': ';
		
						$ef_text .= "\n\n".$_vat_id_translation.$_POST['vatid'];
					}
		
                    $idb->Execute("INSERT INTO ".TABLE_CONFIGURATION_MULTI."1 (`config_key`, `config_value`, `group_id`, `sort_order`, `type`, `url`) VALUES ('_store_email_footer_txt_".$lang["code"]."', '".addslashes($ef_text)."', 12, 8, 'textarea', '')");
		
					$ef_html = str_replace("\n", "\n<br />", $ef_text);
                    $idb->Execute("INSERT INTO ".TABLE_CONFIGURATION_MULTI."1 (`config_key`, `config_value`, `group_id`, `sort_order`, `type`, `url`) VALUES ('_store_email_footer_html_".$lang["code"]."', '".addslashes($ef_html)."', 12, 8, 'textarea', '')");
				}
				
                $idb->Execute('COMMIT;');
				
				$url = Wizard::getInstance()->buildUrl(array(Wizard::PARAM_NEXT => 1, Wizard::PARAM_PAGE => Wizard::getInstance()->getCurrentPage()));
				Wizard::getInstance()->redirect($url);
			}
			else $page->assignTemplateVar('error', $_error);
		
		}
		$page->setTemplate('templates/shop_owner.html');
	}

    /**
     * Finish page
     * @param WizardPage $page
     * @throws GuzzleException
     * @throws WizardException
     */
	public function finishPage(WizardPage $page) {
        // notification
        $this->pageNotification('finish');
		$db = Wizard::getInstance()->getDatabaseObject();
		// Set 1st store default language as default language for admin
		$query = "SELECT * FROM ".DB_PREFIX."_config_1 WHERE config_key='_STORE_LANGUAGE' LIMIT 1";
		/** @var ADORecordSet $rs */
		$rs = $db->Execute($query);
		if($rs->RecordCount() > 0){
			$defaultLang = $rs->fields['config_value'];
		} else {
			$defaultLang = '';
		}
		

		
		$shoplink = '<a href="../index.php">';
		
		// create admin password
		$password = $this->_genPass();
		$page->assignTemplateVar('password',$password);
		$page->assignTemplateVar('shoplink',$shoplink);
		$admin = array();
		$pw = new xt_password();
		$admin['user_password'] = $pw->hash_password($password);
		
		$db->AutoExecute(TABLE_ADMIN_ACL_AREA_USER,$admin,'UPDATE',"user_id='1'");
		
		// update mod_rewrite
        $mod_rewrite_found = $this->checkModeRewrite();
		if (!$mod_rewrite_found) {
			$db->Execute("UPDATE ".TABLE_CONFIGURATION." SET config_value='false' WHERE config_key='_SYSTEM_MOD_REWRITE'");
		}

		// set SSL && delete Wizard folder in APS mode
        if (defined('_APS_INSTALL_PACKAGE') && _APS_INSTALL_PACKAGE == true) {
            $db->Execute("UPDATE ".TABLE_MANDANT_CONFIG." SET shop_ssl='1'");
        }
		
		$page->setTemplate('templates/finish_page.html');
	}
	
	/**
	 * Generates a password
	 * @return string
	 */
	protected function _genPass() {
		$newpass = "";
		$laenge=6;
		$laengeS = 2;
		$string="ABCDEFGHIJKLMNPQRSTUVWXYZabcdefghijklmnpqrstuvwxyz123456789";
		$stringS = "!#$%&()*+,-./";
	
		mt_srand((double)microtime()*1000000);
	
		for ($i=1; $i <= $laenge; $i++) {
			$newpass .= substr($string, mt_rand(0,strlen($string)-1), 1);
		}
		for ($i = 1; $i <= $laengeS; $i++) {
			$newpass .= substr($stringS, mt_rand(0, strlen($stringS) - 1), 1);
		}
		$newpass_split = str_split($newpass);
		shuffle($newpass_split);
		$newpass = implode($newpass_split);
		return $newpass;
	}

    /**
     * Database page
     * @param WizardPage $page
     * @throws WizardException
     * @throws GuzzleException
     */
	public function databasePage(WizardPage $page) {
        /** @var ADOConnection $idb */
        // notification
        $this->pageNotification('database');
		global $_SYSTEM_INSTALL_SUCCESS;

		$_pre_defined = ($_SYSTEM_INSTALL_SUCCESS=='false' && defined('_SYSTEM_DATABASE_HOST')) ? true : false;
		
        $install_eu_rates = (int) getParamDefault('install_eu_rates', 1);
        $page->assignTemplateVar('install_eu_rates', empty($install_eu_rates) ? '' : ' checked="checked"');
		
		$demodata = (int) getParamDefault('_dbDemodata', 1);
		$page->assignTemplateVar('_dbDemodata', empty($demodata) ? '' : ' checked="checked"');

        $isSSL = checkHTTPS();
        $page->assignTemplateVar('_isSSL', $isSSL === true ?' checked="checked"' : '');

        $error = false;
        $_error = array();


        $page->assignTemplateVar('error', false);
        $page->assignTemplateVar('sel_engine','innodb');
        $page->assignTemplateVar('import_lang_de','0');
        $page->assignTemplateVar('import_lang_en','0');
		
		if (Wizard::getInstance()->isPost()) {

            $install_eu_rates = (int) $_POST['install_eu_rates'];

			$demodata = (int) $_POST['_dbDemodata'];
			$user = $_POST['_dbUser'];
			if ($_pre_defined) $user =_SYSTEM_DATABASE_USER;
			$page->assignTemplateVar('_dbUser',$user);
			
			$database = $_POST['_dbDatabase'];
			if ($_pre_defined) $database =_SYSTEM_DATABASE_DATABASE;
			$page->assignTemplateVar('_dbDatabase',$database);
			
			$host = (!$_POST['_dbServer'] ? 'localhost' : $_POST['_dbServer']);
			if ($_pre_defined) $host =_SYSTEM_DATABASE_HOST;
			#    if ($_pre_defined) $host =_SYSTEM_DATABASE_HOST;
			$page->assignTemplateVar('_dbServer',$host);
			
			$password = $_POST['_dbPassword'];
			if ($_pre_defined) $password =_SYSTEM_DATABASE_PWD;
			$page->assignTemplateVar('_dbPassword',$password);
			
			$email =  $_POST['_sysAdminmail'];
			$page->assignTemplateVar('_sysAdminmail',$email);
			
			$email2 =  $_POST['_sysAdminmail2'];
			$page->assignTemplateVar('_sysAdminmail2',$email2);
			
			$prefix =   $_POST['_dbPrefix'];
			if ($_pre_defined && defined('DB_PREFIX')) $prefix =DB_PREFIX;
			if (!defined('DB_PREFIX')) define('DB_PREFIX', substr($prefix, -1, 1) == "_" ? substr($prefix, 0, strlen($prefix)-1) : $prefix);
			
			$page->assignTemplateVar('_dbPrefix',$prefix);
			
			$sel_country = $_POST['store_country'];
			$sel_language = $_POST['default_language'];
			
			$sel_engine = 'innodb'; //$_POST['storage_engine'];
			$page->assignTemplateVar('sel_engine',$sel_engine);
			
			// selected languages
			$languages = array();
			if (isset($_POST['import_lang_de']) && $_POST['import_lang_de']=='1') {
				$languages[]='de';
				$page->assignTemplateVar('import_lang_de','1');
			}
			if (isset($_POST['import_lang_en']) && $_POST['import_lang_en']=='1') {
				$languages[]='en';
				$page->assignTemplateVar('import_lang_en','1');
			}

			// validate input
			if ($host=='')  {
				$error = true;
				$_error[] = array('text'=>constant('_ERROR_SERVER'));
			}
			if ($user=='') {
				$error = true;
				$_error[] = array('text'=>constant('_ERROR_USER'));
			}
			if ($database=='') {
				$error = true;
				$_error[] = array('text'=>constant('_ERROR_DATABASE'));
			}
			if ($password=='') {
				$error = true;
				$_error[] = array('text'=>constant('_ERROR_PASSWORD'));
			}
            $preg_out  = [];
            preg_match('/[a-zA-Z]+[a-zA-Z0-9_]+/', DB_PREFIX, $preg_out);
            if (count($preg_out)<1) {
                $error = true;
                $_error[] = array('text'=>constant('_ERROR_DB_PREFIX'));
                $page->assignTemplateVar('_dbPrefix','xt');
            }
			
			if ($email=='' or $_POST['_sysAdminmail2']=='') {
				$error = true;
				$_error[] = array('text'=>constant('_ERROR_EMAIL'));
			} else {
				if ($email != $_POST['_sysAdminmail2']) {
					$error = true;
					$_error[] = array('text'=>constant('_ERROR_EMAIL_MATCH'));
				}
			}
			
			if (false === filter_var ($email, FILTER_VALIDATE_EMAIL)) {
				$error = true;
				$_error[] = array('text'=>constant('_ERROR_EMAIL'));
			}
			
			if (false === filter_var ($_POST['_sysAdminmail2'], FILTER_VALIDATE_EMAIL)) {
				$error = true;
				$_error[] = array('text'=>constant('_ERROR_EMAIL'));
			}
			
			// no language selected
			if (count($languages)==0) {
				$error = true;
				$_error[] = array('text'=>constant('_ERROR_NO_LANGUAGE_SELECTED'));
			}
			// default language not in list
			if (!in_array($sel_language,$languages)) {
				$error = true;
				$_error[] = array('text'=>constant('_ERROR_DEFAULT_LANGUAGE_NOT_SELECTED'));//'<br />($selected-language: '.$sel_language.' $language-packs:'.implode(', ',$languages).')');
			}
			
			// check if we can connect to database
			if (!$error) {
			
			
				//$dsn = 'mysql://'.$user.':'.$password.'@'.$host.'/'.$database;
				try {
					if (!defined('_SRV_WEB_FRAMEWORK'))
						define('_SRV_WEB_FRAMEWORK', 'xtFramework' . DS);

                    include_once _SRV_WEBROOT . _SRV_WEB_FRAMEWORK . 'library/adodb-xt/xtcommerce-errorhandler.inc.php';

                    try
                    {
						$idb = ADONewConnection('mysqli');
						$idb->Connect($host, $user, $password, $database);
                    } catch (Exception $e)
                    {
                        throw new WizardException(constant('_ERROR_DB_CONNECT'). $e->getMessage(), WizardException::ERROR_LEVEL_FATAL);
                    }

                    $version = $idb->GetOne("SELECT VERSION() AS Version");
                    if($version===false) $version = 0;
                    $version = preg_replace('/[^\d.]/', '', $version);
                    if (version_compare($version, self::MYSQL_MIN_VERSION, '<')) {
                        $error = true;
                        $s = sprintf(constant('_ERROR_MYSQL_VERSION'), $version, self::MYSQL_MIN_VERSION);
                        $_error[]=array('text'=> $s);
                    }

                } catch (exception $e)
                {
                    $_error[] = array('text' => $e->getMessage());
					$error = true;
				}
			
			}
			
			if ($error) {
				$page->assignTemplateVar('error',$_error);
			} else {

				$install_prefix = '';
				if ($prefix!='') {
					if ($prefix[strlen($prefix)-1]=='_') {
						$prefix = substr($prefix,0,-1);
						$install_prefix = $prefix.'_';
					} else {
						$install_prefix = $prefix.'_';
					}
				} else {
					$prefix = 'xt';
					$install_prefix = 'xt_';
				}
		
				try
				{
				// set uft8 collation
					$idb->Execute('ALTER DATABASE `'.$database.'` DEFAULT CHARACTER SET `utf8`');
				} catch (exception $e) {
					$_error[] = array('text' => $e->getMessage());
					$error = true;
				}
		
                try
                {
					$idb->Execute("SET NAMES 'utf8'");
                } catch (exception $e)
                {
					throw new WizardException(constant('_ERROR_NEED_MYSQL5'), WizardException::ERROR_LEVEL_FATAL);
				}

				/*
                $createViewGranted = true;
                try {
                    
                    $idb->Execute("DROP TABLE IF EXISTS view_test_table");
                    $idb->Execute("CREATE TABLE view_test_table (qty INT, price INT)");
                    $idb->Execute("INSERT INTO view_test_table VALUES(3, 50)");
                    
                    // test to create view
                    $idb->Execute("CREATE VIEW view_test AS SELECT qty, price, qty*price AS value FROM view_test_table");
                    
                    $idb->Execute("DROP TABLE IF EXISTS view_test_table");
                    $idb->Execute("DROP VIEW view_test");
                    
                } catch (exception $e) {
                    $createViewGranted = false;
                    $_error[] = array('text' => $e->getMessage());
                    $error = true;
                }
                
                
                if (!$createViewGranted)
                {
                    $_error[] = array('text' => _ERROR_GRANT_CREATE_VIEW);
                    $error = true;
                }
				*/

                if($error)
                {
                    $page->assignTemplateVar('error', $_error);
                }
                else {

                    $sql_version = $idb->GetOne("SELECT VERSION() AS Version");
                    if(version_compare($sql_version, '5.6') == -1)
                    {
                        die('Systemanforderung  mysql 5.6 minimum nicht erfüllt. <a href="https://xtcommerce.atlassian.net/wiki/spaces/MANUAL/pages/917573/Systemanforderungen" target="_blank">Handbuch</a>');
                    }

					$idb->Execute("SET CHARACTER_SET_CLIENT=utf8");
					$idb->Execute("SET CHARACTER_SET_RESULTS=utf8");
					$error = false;

                    $enterprise_sql = 'enterprise.sql';

                    $res = $this->_installSQL($idb, $enterprise_sql, $install_prefix,'',$sel_engine);
                    if ($res != '-1')
                    {
						// if not -1 $res of _installSQLis of type Exception
						WizardLogger::getInstance()->log($res->getMessage() . ' Install main sql.');
						if (strpos($res->getMessage(),'acl_area')) {
                            $_error[]=array('text'=>constant('_TEXT_ERROR_DATABASE_NOT_EMPTY'));
                        } else {
                            $_error[]=array('text'=>$res->getMessage());
                        }

						$page->assignTemplateVar('error',$_error);
						$error = true;
					}
                }
	
	        if ($error==false) {
	
	            // write config
	            $conf_content = '<?php' . "\n";
	            $conf_content .='defined(\'_VALID_CALL\') or die(\'Direct Access is not allowed.\');'. "\n";
	            $conf_content .='date_default_timezone_set(\'Europe/Berlin\');'. "\n";
	            $conf_content .='define(\'_SYSTEM_DATABASE_HOST\', \''.$host.'\');'. "\n";
	            $conf_content .='define(\'_SYSTEM_DATABASE_USER\', \''.$user.'\');'. "\n";
	            $conf_content .='define(\'_SYSTEM_DATABASE_PWD\', \''.$password.'\');'. "\n";
	            $conf_content .='define(\'_SYSTEM_DATABASE_DATABASE\', \''.$database.'\');'. "\n";
	            $conf_content .='define(\'DB_PREFIX\',\''.$prefix.'\');'. "\n";
	            $conf_content .='define(\'DB_STORAGE_ENGINE\',\''.$sel_engine.'\');'. "\n";
	            $conf_content .='define(\'_CORE_DEBUG_MAIL_ADDRESS\',\''.$email.'\');'. "\n";
	            $conf_content .='$_SYSTEM_INSTALL_SUCCESS = \'true\';'. "\n";
	            if (defined('_APS_INSTALL_PACKAGE') && _APS_INSTALL_PACKAGE == true) {
                    $conf_content .='define(\'_APS_INSTALL_PACKAGE\',\'true\');'. "\n";
                }
	            $fp = fopen(_SRV_WEBROOT . 'conf/config.php', 'w');
	            fputs($fp, $conf_content);
	            fclose($fp);
				sleep(2);

				// change cronjobs permission
				if(is_dir(_SRV_WEBROOT._SRV_WEB_CRONJOBS)){
					chmod(_SRV_WEBROOT._SRV_WEB_CRONJOBS, 0777);
				}
	
	            // include config, include tables
	            require_once _SRV_WEBROOT . 'conf/config.php';
	            require_once _SRV_WEBROOT . 'conf/database.php';
	
	            // mandant sichern
	            $domain = $_SERVER['SERVER_NAME'];

	            // wir versuchen jetzt fälle zu bemerken
                // wo server_name != http_host, zb domain.de != www.domain.de
	            if($domain != $_SERVER["HTTP_HOST"])
                {
                    // wenn server_name nicht mit www. beginnt, suchen wir nicht weiter, zb. shop.domain.de
                    // wahrscheinlich sollten dann server_name und http_host identisch sein (?)
                    if(preg_match('/^www\.', $domain) != 1){
                        $domain = $_SERVER["HTTP_HOST"];
                        // https://stackoverflow.com/questions/2297403/what-is-the-difference-between-http-host-and-server-name-in-php
                        // heisst imo: wenn wir bis hierher kommen, ist die wahrscheinlichkeit grösser, dass http_host *richtiger* ist
                    }
                }

	            $store = array();
	            $store['shop_ssl_domain']=$domain;
                $store['shop_ssl']= array_key_exists('_isSSL', $_POST) && ((int) $_POST['_isSSL'] || $_POST['_isSSL'] == 'on') == 1 ? 1 : 0;
	            $idb->AutoExecute(TABLE_MANDANT_CONFIG,$store,'UPDATE',"shop_id='1'");
	
	            $admin = array();
	            $admin['email'] = $email;
	            $idb->AutoExecute(TABLE_ADMIN_ACL_AREA_USER,$admin,'UPDATE',"user_id='1'");
				
				// create security key
				$gen = date("Ydm").$domain;
                    $idb->Execute("INSERT INTO ".TABLE_CONFIGURATION." (config_key,config_value,group_id) VALUES ('_SYSTEM_SECURITY_KEY','".md5($gen)."','0') ");
	        }
	
				if ($error==false)
				{
					if ( $demodata=='1')
					{
						$res = $this->_installSQL($idb, 'demo_data.sql', $install_prefix, '');
						if ($res!='-1') {
							// if not -1 $res of _installSQLis of type Exception
							WizardLogger::getInstance()->log($res->getMessage() . ' Install demo data.');
							$_error[]=array('text'=>$res->getMessage());
							$page->assignTemplateVar('error',$_error);
							$error = true;
						}
					}
					// redirect to language import
					$url = Wizard::getInstance()->buildUrl(array(
							Wizard::PARAM_SCRIPT => 'StartPageScript',
			            	Wizard::PARAM_PAGE => 'language',
							'lng' => implode('.',$languages),
							'sel_country' => $sel_country,
							'sel_language' => $sel_language,
							'demodata' => $demodata,
                            'install_eu_rates' => $install_eu_rates
					));
					// Redirect to info page
					Wizard::getInstance()->redirect($url);
				}
			}

		} else {
			$page->assignTemplateVar("_dbServer", defined("_SYSTEM_DATABASE_HOST") ? _SYSTEM_DATABASE_HOST : "");
			$page->assignTemplateVar("_sysAdminmail", defined("_CORE_DEBUG_MAIL_ADDRESS") ? _CORE_DEBUG_MAIL_ADDRESS : "");
			$page->assignTemplateVar("_dbUser", defined("_SYSTEM_DATABASE_USER") ? _SYSTEM_DATABASE_USER : "");
			$page->assignTemplateVar("_sysAdminmail2", defined("_CORE_DEBUG_MAIL_ADDRESS") ? _CORE_DEBUG_MAIL_ADDRESS : "");
			$page->assignTemplateVar("_dbPassword", defined("_SYSTEM_DATABASE_PWD") ? _SYSTEM_DATABASE_PWD : "");
			$page->assignTemplateVar("_dbDatabase", defined("_SYSTEM_DATABASE_DATABASE") ? _SYSTEM_DATABASE_DATABASE : "");
			$page->assignTemplateVar("_dbPrefix", defined("DB_PREFIX") ? DB_PREFIX : "");
		}

		if ($error) {
            $this->pageNotification('database',$_error);
        }

		// assign country dropdown
		// load from file
		$handle = fopen (_SRV_WEBROOT . 'media/lang/de_countries.csv',"r");
		$country_data = array();
		while ( ($data = fgetcsv ($handle, 1000, ";",'"')) !== FALSE ) {
			$country = array();
			$country['code']=$data[2];
			$country['name']=$data[1];
			$country_data[]=$country;
		}
		fclose ($handle);
		
		if (isset($sel_country)) {
			$page->assignTemplateVar('selected_country',$sel_country);
		} else {
			$page->assignTemplateVar('selected_country','DE');
		}
		
		if (isset($sel_language)) {
			$page->assignTemplateVar('selected_language',$sel_language);
		} else {
			$page->assignTemplateVar('selected_language','de');
		}
		
		$language_data = array();
		$lang = array();
		$lang['code']='de';
		$lang['name']='Deutsch';
		$language_data[]=$lang;
		$lang['code']='en';
		$lang['name']='Englisch';
		$language_data[]=$lang;

				
		$page->assignTemplateVar('store_countries',$country_data);
		$page->assignTemplateVar('default_language',$language_data);
		$page->assignTemplateVar("isPreDefined", $_pre_defined);
		$page->setTemplate('templates/database.html');
	}

    /**
     * Insert content of .sql file per line into dtabase
     * @param ADOConnection $idb
     * @param string $filename
     * @param string $prefix
     * @param string $language_code
     * @param string $engine
     * @return Exception|int
     */
	public function _installSQL( $idb, $filename, $prefix='', $language_code='', $engine='innodb') {
		
		$query = '';
		// open sql
		
		if ($language_code=='') {
			$filename = _SRV_WEBROOT.'xtWizard/sql/'.$filename;
		} else {
			$filename = _SRV_WEBROOT.'xtWizard/languages/'.$language_code.'/'.$filename;
		}
		
		
		$sql_content = $this->_getFileContent($filename);
		// replace windows linefeeds
		$sql_content = str_replace("\r\n","\n",$sql_content);
		$queries = array();
		$chars = strlen($sql_content);
		for ($i = 0; $i < $chars - 1; $i++) {
			// check if char is ; and next \n
			if ($sql_content[$i]==';' && $sql_content[$i+1]=="\n") {
				$query .= $sql_content[$i];
				$queries[] = $query;
				$query = '';
				$i++;
			} else {
				if ($sql_content[$i]=='-' && $sql_content[$i+1]=='-') {
					// skip to next \n
					for ($ii=$i;$ii<$chars;$ii++) {
	
						if ($sql_content[$ii]=="\n") {
							break;
						} else {
							$i++;
						}
	
					}
				} else {
					if (!isset($query)) $query='';
					$query.=$sql_content[$i];
				}
			}
		}
		
		foreach ($queries as $key => $val) {
			$query = trim($val);
			$query = str_replace('##_',$prefix,$query);
			$query = str_replace('#E_',$engine,$query);
			
			// ok, now search vor OTHER INSERT INTO statements, and break them up
			if (substr($query,0,6)=='INSERT') {
				$check_qry = substr($query,7);
				
				if (strstr($check_qry,'INSERT')) {
					$qry = explode('INSERT',$check_qry);
					foreach ($qry as $k => $v) {
						$queries[]='INSERT '.$v;
					}
					unset ($queries[$key]);
				} else {
					$queries[$key]=$query;
				}
			} else {
				$queries[$key]=$query;
			}
		}

		foreach ($queries as $key => $val) {
			try {
				$idb->Execute($val);
			} catch (exception $e) {
				// echo $val;
				return $e;
			}
		}
		return -1;
	}
	
	/**
	 * Read file and return its conetents
	 * @param string $filename
	 * @return string
	 */
	protected function _getFileContent($filename) {
		$handle = fopen($filename, 'rb');
		$content = fread($handle, filesize($filename));
		fclose($handle);
		return $content;
	}

    /**
     * Check server settings
     * @return array <number, string>
     * @throws WizardException
     */
	protected function  _checkServerSettings() {

        global $is_pro_version;

        $php_min_version = '5.6.0';
        $ion_min_version = '10.2';

		$setting = $checks = array();
	    $setting['message'] = 0;
	    $checks = array();
	    $has_error = false;
	    $has_blocking_error = false;

		$checks[0]=array('desc'=>sprintf(constant('_SYSTEM_PHP_VERSION'), $php_min_version),'check'=>'1');  // mandatory
        $checks[1]=array('desc'=>sprintf(constant('_SYSTEM_ION_CUBE_VERSION'), $ion_min_version),'check'=>'1'); // mandatory in pro
		$checks[2]=array('desc'=>constant('_SYSTEM_ZLIB'),'check'=>'1');// mandatory
		$checks[4]=array('desc'=>constant('_SYSTEM_CURL'),'check'=>'1');// mandatory
		$checks[6]=array('desc'=>constant('_SYSTEM_GDLIB'),'check'=>'1');// mandatory
		$checks[8]=array('desc'=>constant('_SYSTEM_GDLIB_GIF'),'check'=>'1');// mandatory
        $checks[9]=array('desc'=>constant('_SYSTEM_FILEINFO'), 'check' => '1');// mandatory
		$checks[14]=array('desc'=>constant('_SYSTEM_SESSION_AUTOSTART'),'check'=>'1');// mandatory
		$checks[16]=array('desc'=>constant('_SYSTEM_FILE_UPLOAD'),'check'=>'1');// mandatory
		$checks[18]=array('desc'=>constant('_SYSTEM_MEMORY_LIMIT'),'check'=>'1');
	    $checks[21]=array('desc'=>constant('_SYSTEM_CHECK_IMAGE_FTBBOX'),'check'=>'1');// mandatory
	    $checks[22]=array('desc'=>constant('_SYSTEM_CHECK_MOD_REWRITE'),'check'=>'1');
	    $checks[23]=array('desc'=>constant('_SYSTEM_CHECK_SOAP'),'check'=>'1');// mandatory
	    $checks[24]=array('desc'=>constant('_SYSTEM_CHECK_OPENSSL'),'check'=>'1');// mandatory
		$checks[25]=array('desc'=>constant('_MAX_EXECUTION_TIME'),'check'=>'1');
        $checks[26]=array('desc'=>constant('_SYSTEM_CHECK_HTACCESS'),'check'=>'1');// mandatory
        $checks[27]=array('desc'=>constant('_SYSTEM_MBSTRING'),'check'=>'1');// mandatory
        if ( PHP_OS == "WINNT" ) {
            $checks[28]=array('desc'=>constant('_SYSTEM_LINK'),'check'=>'1');// mandatory (non windows)
        } else {
            $checks[28]=array('desc'=>constant('_SYSTEM_SYMLINK'),'check'=>'1');// mandatory (non windows)
        }

        if(Wizard::getInstance()->getWizardType()==2) { // update
            $checks[28]=array('desc'=>"SQL min version ".self::MYSQL_MIN_VERSION, 'check'=>'1');
            $mysql_ok = $this->checkMysqlVersion();
            if($mysql_ok !== true)
            {
                $checks[28]['version'] = $mysql_ok;
                $checks[28]['check']=0;
                $setting['message'] = 1;
                $has_error=true;
                $has_blocking_error = true;
            }
        }


		// php version
		$checks[0]['version'] = PHP_VERSION;
		if (version_compare($php_min_version,PHP_VERSION)!='-1') {
			$checks[0]['check']=0;
	        $setting['message'] = 1;
            $has_error=true;
            $has_blocking_error = true;
		}

        // ioncube available (pro only)
        if($is_pro_version && empty($_REQUEST['dontcheckion']))
        {
            $plg = new plugin();
            $ion_v = $plg->getIonCubeVersion();
            $checks[1]['version'] = $ion_v;
            if ($ion_v === false || $ion_v === true || version_compare($ion_v, $ion_min_version, '<'))
            {
                $checks[1]['check'] = 0;
                $setting['message'] = 1;
                $has_error = true;
                $has_blocking_error = true;
            }
        }
	
		$disabled = explode(',', ini_get('disable_functions'));
	    // Check for curl
	    if (!function_exists('curl_version') || in_array('curl_exec', $disabled)) {
	    	$checks[4]['check']=0;
	    	$setting['message'] = 1;
            $has_error=true;
            $has_blocking_error = true;
	    }

        if ( PHP_OS == "WINNT" ) {
            // check for symlinks
            if (!function_exists('link') || in_array('link', $disabled)) {
                $checks[28]['check']=0;
                $setting['message'] = 1;
                $has_error=true;
                $has_blocking_error = true;
            }

        } else {
            // check for symlinks
            if (!function_exists('symlink') || in_array('symlink', $disabled)) {
                $checks[28]['check']=0;
                $setting['message'] = 1;
                $has_error=true;
                $has_blocking_error = true;
            }

        }

		// check GDlib
		if (function_exists('gd_info')) {
			$gd_info = gd_info();
			$checks[6]['version'] = $gd_info['GD Version'];
	
			if ($gd_info['GIF Read Support']!='1' or $gd_info['GIF Create Support']!='1') {
				$checks[8]['check']=0;
	            $setting['message'] = 1;
                $has_error=true;
                $has_blocking_error = true;
			}
	
		} else {
			$checks[6]['check']=0;
			$checks[6]['version'] = '- missing -';
	
			$checks[8]['check']=0;
			$checks[8]['version'] = '- missing -';
	        $setting['message'] = 1;
            $has_error=true;
            $has_blocking_error = true;
	    }
		
        // check Fileinfo
        if (!function_exists('finfo_file')) {
            $checks[9]['check'] = 0;
            $setting['message'] = 1;
            $has_error=true;
            $has_blocking_error = true;
        }
	
		// session auto start
		if (ini_get('session.auto_start')==1) {
			$checks[14]['check']=0;
	        $setting['message'] = 1;
            $has_error=true;
            $has_blocking_error = true;
		}
	
		// file uploads
		if (ini_get('file_uploads')===0 || strtolower(ini_get('file_uploads'))==='off') {
			$checks[16]['check']=0;
	        $setting['message'] = 1;
            $has_error=true;
            $has_blocking_error = true;
		}
	
		// max_execution_time
		// memory_limit
		$ini_memory_limit = ini_get('memory_limit');
		$limit = $this->convert2MB($ini_memory_limit);
		$checks[18]['version']=$ini_memory_limit;
		$limit=(int)$limit;
		if ($limit<128 && $ini_memory_limit!='-1') {
			$checks[18]['check']=0;
	        $setting['message'] = 1;
            $has_error=true;
		}
	    
	    // check imageftbbox function
	    if (!function_exists('imageftbbox')) {
			$checks[21]['check']=0;
	        $setting['message'] = 1;
            $has_error=true;
            $has_blocking_error = true;
	    }
	
	    // check for modrewrite
        $mod_rewrite_found = $this->checkModeRewrite();
	    if (!$mod_rewrite_found) {
	        $checks[22]['check']=0;
	        $setting['message'] = 1;
            $has_error=true;
	    }
		
		// soap
	    if (!class_exists('SoapClient', false)) {
	        $checks[23]['check']=0;
	        $setting['message'] = 1;
            $has_error=true;
            $has_blocking_error = true;
	    }

		//open ssl
	    if (!defined('OPENSSL_VERSION_NUMBER')) {
	        $checks[24]['check']=0;
	        $setting['message'] = 1;
            $has_error=true;
            $has_blocking_error = true;
	    }
		
		//max_execution time of script on the server
        $checks[25]['version']=ini_get("max_execution_time");
        if (ini_get("max_execution_time")<180 && ini_get("max_execution_time")!=0) {
            $checks[25]['check']=0;
            $setting['message'] = 1;
            $has_error=true;
        }

        // check if htaccess was uploaded
        if (!file_exists('../.htaccess')) {
            $checks[26]['check']=0;
            $setting['message'] = 1;
            $has_error=true;
            $has_blocking_error = true;
        }

        // check if mbstring is enabled
        if (!extension_loaded('mbstring')) {
            $checks[27]['check']=0;
            $setting['message'] = 1;
            $has_error=true;
            $has_blocking_error = true;
        }



        $setting['has_error'] = $has_error;
        $setting['has_blocking_error'] = $has_blocking_error;
	    $setting['checks'] = $checks;
	    return $setting;
	
	}

	protected function convert2MB($from){
		$from = trim($from);
		$from = strtoupper($from);

		preg_match("/\d+/", $from, $number);
		$number = (int) $number[0];
		if(!$number) return false;

		preg_match("/[A-Z]+/", $from, $size);
		$size = $size ? $size[0] : '';

		switch($size)
		{
			case "KB":
			case "K":
				return $number/1024;
			case "MB":
			case "M":
				return $number;
			case "GB":
			case "G":
				return $number*pow(1024,1);
			case "TB":
			case "T":
				return $number*pow(1024,2);
			case "PB":
			case "P":
				return $number*pow(1024,3);
			case "B":
			default:
				return $number/1024/1024;
		}
	}

    /**
     * @return bool|string
     * @throws WizardException
     */
	protected function checkMysqlVersion() {
		$version = Wizard::getInstance()->getDatabaseObject()->GetOne("SELECT VERSION() AS Version");
		
		if ($version!=false) {
		    $version = preg_replace('/[^\d.]/', '', $version);
            if (version_compare($version, self::MYSQL_MIN_VERSION, '<')){
				return $version;
			}
            return true;
		}
		return 0;
	}

    /**
     * Info page
     * @param WizardPage $page
     * @throws WizardException
     */
	public function infoPage(WizardPage $page) {
		$page
		->setTemplate('templates/info_page.html')
		->assignTemplateVar('message', getParamDefault(Wizard::PARAM_MSG, ''))
		->assignTemplateVar('title', getParamDefault(Wizard::PARAM_TITLE, ''));
	}


    /**
     * Renders dbbackup page
     * @param WizardPage $page
     * @throws WizardException
     */
    public function dbBackup(WizardPage $page) {

        global $DB_PREFIX;
        // Set the correct wizard type
        Wizard::getInstance()->setWizardType(Wizard::WIZARD_TYPE_UPDATE);
		WizardLogger::getInstance()->clearLog();
        $error = $_error = false;

        $cleanup_tables = array(
            'adodb_logsql' => false,
            $DB_PREFIX.'callback_log' => array('col' => 'created', 'days' => 180),
            $DB_PREFIX.'clean_cache_logs' => false,
            $DB_PREFIX.'exportimport_log' => false,
            $DB_PREFIX.'failed_login' => array('col' => 'last_try', 'days' => 60),
            $DB_PREFIX.'plugin_history' => array('col' => 'date_added', 'days' => 60),
            $DB_PREFIX.'sessions2' => array('col' => 'modified', 'days' => 180),
        );

        $dbBackup = (int) getParamDefault('_dbBackup', 0);
        $dbCleanup = (int) getParamDefault('_dbCleanup', 0);

        $sql_backup_dir = 'plugin_cache/database/';
        $sql_backup_path = _SRV_WEBROOT.$sql_backup_dir;

        if (Wizard::getInstance()->isPost()) {
            if ( ! empty($dbCleanup))
            {
                /** @var ADOConnection $db */
                $db = Wizard::getInstance()->getDatabaseObject();
                foreach($cleanup_tables as $table => $dateColSettings)
                {
                    $tblExists = $db->GetOne('SELECT table_name FROM information_schema.tables WHERE table_schema = ? AND table_name = ?',
                        array(_SYSTEM_DATABASE_DATABASE, $table));
                    if($tblExists)
                    {
                        $where = '1';
                        if(is_array($dateColSettings))
                        {
                            $where = $dateColSettings['col'].' < NOW() - INTERVAL '.$dateColSettings['days'].' DAY';
                        }
                        $db->Execute('DELETE FROM '.$table.' WHERE '.$where);
                        $db->Execute('COMMIT');
                    }
                }
            }
            if ( ! empty($dbBackup))
            {
                if(!is_dir($sql_backup_path)){
                    mkdir($sql_backup_path);
                    chmod($sql_backup_path, 0777);
                }
                else {
                    chmod($sql_backup_path, 0777);
                }

                $date = date('Y-m-d__H-i-s');
                $host = _SYSTEM_DATABASE_HOST;
                $schema = _SYSTEM_DATABASE_DATABASE;
                try {
                    $dump = new Dump;
                    $dump
                        ->file($sql_backup_path."{$schema}___{$date}.sql.gz")
                        ->dsn("mysql:host={$host};dbname={$schema}")
                        ->user(_SYSTEM_DATABASE_USER)
                        ->pass(_SYSTEM_DATABASE_PWD)
                        ->tmp($sql_backup_path)
                        ->encoding('utf8mb4');

                    new xt_dump_export($dump);
                }
                catch (Exception $e)
                {
                    $msg = 'Database backup was not successful: '. $e->getMessage();
                    WizardLogger::getInstance()->log($msg);
                    WizardLogger::getInstance()->log($e->getTraceAsString());
                    $_error[]=array('text'=>$msg);
                    $error = true;
                }
            }
            if (!$error){
                // redirect to update script page
                $url = Wizard::getInstance()->buildUrl(array(
                        Wizard::PARAM_SCRIPT => 'StartPageScript',
                        Wizard::PARAM_PAGE => 'update',
                ));
              
                Wizard::getInstance()->redirect($url);
            }
        }

        $tpl_vars = array(
            'sql_backup_dir' => $sql_backup_dir,
            'show_language' => true,
            'cleanup_tables' => $cleanup_tables,
            '_dbBackup' => empty($dbBackup) ? '' : ' checked="checked"',
            '_dbCleanup' => empty($dbCleanup) ? '' : ' checked="checked"',
            'error' => $_error
        );
        $page->setTemplate('templates/update_page_dbbackup.html')->assignTemplateVars($tpl_vars);
    }

    /**
     * Backuping database
     * @param $host
     * @param $user
     * @param $pass
     * @param $name
     * @param string $tables
     * @return bool
     */
    function backup_tables($host,$user,$pass,$name,$tables = '*')
    {
        
        $link = mysqli_connect($host,$user,$pass);
        mysqli_select_db($link, $name);
        
        //get all of the tables
        if($tables == '*')
        {
            $tables = array();
            $result = mysqli_query($link, 'SHOW TABLES');
            while($row = mysqli_fetch_row($result))
            {
                $tables[] = $row[0];
            }
        }
        else
        {
            $tables = is_array($tables) ? $tables : explode(',',$tables);
        }
        $return = '';
        //cycle through
        foreach($tables as $table)
        {
            $result = mysqli_query($link, 'SELECT * FROM '.$table);
            $num_fields = mysqli_num_fields($result);
            
            $return.= 'DROP TABLE IF EXISTS '.$table.';';
            $row2 = mysqli_fetch_row(mysqli_query($link, 'SHOW CREATE TABLE '.$table));
            $return.= "\n\n".$row2[1].";\n\n";
            
            for ($i = 0; $i < $num_fields; $i++) 
            {
                while($row = mysqli_fetch_row($result))
                {
                    $return.= 'INSERT INTO '.$table.' VALUES(';
                    for($j=0; $j<$num_fields; $j++) 
                    {
                        $row[$j] = addslashes($row[$j]);
                        $row[$j] = preg_replace('/\n/',"\\n",$row[$j]);
                        if (isset($row[$j])) { $return.= '"'.$row[$j].'"' ; } else { $return.= '""'; }
                        if ($j<($num_fields-1)) { $return.= ','; }
                    }
                    $return.= ");\n";
                }
            }
            $return.="\n\n\n";
        }
        
        //save file
        
        $handle = fopen(_SRV_WEBROOT._SRV_WEB_PLUGIN_CACHE.'db-backup-'.date("Y-m-d_H-i-s").'.sql','w+');
        fwrite($handle,$return);
        fclose($handle);
        return true;
    }

    /**
     * Renders update page
     * @param WizardPage $page
     * @throws WizardException
     */
    public function updatePage(WizardPage $page) {
        // Set the correct wizard type
        Wizard::getInstance()->setWizardType(Wizard::WIZARD_TYPE_UPDATE);
        
        // Get all loaded scripts
        $loadedScripts = Wizard::getInstance()->getLoadedScripts();
        $updateScripts = array();
        $all_applied= true;
        $storeVersion = Wizard::getInstance()->getStoreVersion();


        // todo sortieren der $loadedScripts nach targetVersion, zZ passt's zufällig
        /**
         * @var  $script ExecutableScript
         */
        foreach ($loadedScripts as $k => $script) {
            if ($script->getWizardType() == Wizard::WIZARD_TYPE_UPDATE)
            {
                $appliableVersion = $script->getAppliableShopVersion();
                $targetVersion = $script->getTargetShopVersion();
                $loadedScripts[$k]->targetVersion = $targetVersion;
            }
        }
        // do sort

        $applied = false;
        $appliable = false;
        $tooltip = '';
        foreach ($loadedScripts as $script) {
            // Find only update scripts
            if ($script->getWizardType() == Wizard::WIZARD_TYPE_UPDATE) {


                // $targetVersion => diese version soll vom update script erzeugt werden
                $targetVersion = $script->getTargetShopVersion();

                // $appliableVersion => die minimum shop version
                $appliableVersion = $script->getAppliableShopVersion();
                if(strpos($appliableVersion, '-min') !== false)
                {
                    $appliableVersion = str_replace('-min', '', $appliableVersion);
                }

                // $skippableVersion => ab dieser version ist ein db-update nicht notwendig, ergo schritt kann ausgelassen werden
                $skippableVersion = $script->getskippableShopVersion();
                
                // wenn $storeVersion >= der $skippableVersion
                if(
                    version_compare($storeVersion ,$skippableVersion ,'>=')
                )
                {
                    $applied = true;
                    $appliable = false;
                    $tooltip = constant('TEXT_ALREADY_APPLIED');
                }
                // sonst wenn $storeVersion < $targetVersion
                else if(version_compare($storeVersion, $targetVersion , '<'))
                {
                    $applied = false;
                    $appliable = true;
                    $tooltip = constant('TEXT_APPLY_UPDATE');
                    // aber wenn $storeVersion < $appliableVersion
                    if(version_compare($storeVersion, $appliableVersion , '<') && version_compare($storeVersion, $skippableVersion , '<'))
                    {
                        $appliable = false;
                        $tooltip = constant('TEXT_APPLY_CAN_NOT_BE_APPLIED');
                    }
                }

                // Get first page of the script and set it
                $pages = $script->getPages();
                $firstPage = array_shift($pages);
                $all_applied &= $applied;
                $updateScripts[$script->targetVersion] = array(
                    'Title' => '<span style="font-weight:bold;font-size:0.8em">'.constant('TEXT_DB_UPDATE') . ':&nbsp; &nbsp;</span>'.$script->getScriptTitle(),
                    'Page' => $firstPage->getPageUrl(),
                    'Applied' => $applied,
                    'Appliable' => $appliable,
                    'Tooltip' => $tooltip,
                );
            }
        }
         $url = Wizard::getInstance()->buildUrl(array(
                Wizard::PARAM_SCRIPT => 'StartPageScript',
                Wizard::PARAM_PAGE => 'backupPlugins',
        ));
        $highestTargetVersion =$this->getHighestTargetVersion();
        $page->setTemplate('templates/index.html')->assignTemplateVar('highestTargetVersion', $highestTargetVersion);
        $page->assignTemplateVar('storeVersion', $storeVersion);
        $page->assignTemplateVar('licWarning', sprintf(constant('TEXT_INFO_PLACE_LICENSE'),$highestTargetVersion,$highestTargetVersion));
        $page->assignTemplateVar('next_page', $url);
        $page->assignTemplateVar('all_applied', $all_applied);
        $page->assignTemplateVar('scripts', $updateScripts);
        $page->setTemplate('templates/update_page.html')->assignTemplateVar('show_language', true);
    }

    /**
     * Renders plugins backup page
     * @param WizardPage $page
     * @throws WizardException
     */
    public function backupPlugins(WizardPage $page) {
        // Set the correct wizard type
        Wizard::getInstance()->setWizardType(Wizard::WIZARD_TYPE_UPDATE);
		$plugin_cache =_SRV_WEBROOT.'plugin_cache/update_backup/';
        if(!is_dir($plugin_cache)){
            mkdir($plugin_cache);
            chmod($plugin_cache, 0777);         
        }
		else {
			chmod($plugin_cache, 0777);
		}
		if(empty($_SESSION['plgBackupFolder']))
		{
			$_SESSION['plgBackupFolder'] = $plugin_cache. 'update_backup_'.date("Y-m-d_H-i-s").'/';
		}
        $backupFolder = $_SESSION['plgBackupFolder'];
        if(!is_dir($backupFolder)){
            mkdir($backupFolder);
            chmod($backupFolder, 0777);         
        }
		else {
			chmod($backupFolder, 0777);
		}
        $pluginFolder =_SRV_WEBROOT.'plugins/';
        if ($handle = opendir($pluginFolder)) {
            while (false !== ($entry = readdir($handle))) {
                $file =  _SRV_WEBROOT.'xtFramework/classes/class.plugin.php';
                if (file_exists($file)){
                    include_once $file;
                     if ((!in_array($entry,array('.','..','.htaccess','index.html'))) && (strstr($entry, '.')===false)){
                        $pl = new plugin();
                        $this->addControllerCommand(ControllerCommand::factory()
                                ->setAction(function(ControllerCommand $command) use ($entry,$pl,$pluginFolder,$backupFolder) {
                                    try{
                                        
                                        $a = $pl->dircopy($pluginFolder.$entry,$backupFolder.$entry,false);
                                        
                                    }catch (Exception $e){
                                        $command->setErrorMessage("<span class='msg-error'>".constant('TEXT_FAILED_PL_BACKUP')." [". $entry."]: " . $e->getMessage() . "</span><br/>");
                                    }
                                          
                                    return true;
                                })
                                ->setDescription(constant('TEXT_BACKINGUP_PLUGIN') . ' [' . $entry . '] ')
                                ->setAbortOnError(false)
                        );
                                         
                    }
                }
            }
            closedir($handle);
        }

		$this->addControllerCommand(ControllerCommand::factory()
			->setAction(function(ControllerCommand $command) use ($entry,$pl,$pluginFolder,$backupFolder) {
				try{

					unset($_SESSION['plgBackupFolder']);

				}catch (Exception $e){
					$command->setErrorMessage("<span class='msg-error'>".constant('TEXT_FAILED_PL_BACKUP')." [". $entry."]: " . $e->getMessage() . "</span><br/>");
				}

				return true;
			})
			->setDescription(constant('TEXT_BACKINGUP_PLUGIN') . ' [' . $entry . '] ')
			->setAbortOnError(false)
		);

		$page->assignTemplateVar('processing',constant('TEXT_BACKINGUP_PLUGIN'),true);
        $this->execAsyncAction($page);
    }

    /**
     * Update Files info page
     * @param WizardPage $page
     * @throws WizardException
     */
    public function displayUpdateFilesPage(WizardPage $page) {
        $highestTargetVersion = $this->getHighestTargetVersion();
        $page->setTemplate('templates/index.html')->assignTemplateVar('highestTargetVersion', $highestTargetVersion);
        $msg = sprintf(constant('TEXT_OVERWRITE_FILES_MSG'), $highestTargetVersion);
        $page
        ->setTemplate('templates/info_page.html')
        ->assignTemplateVar('message', $msg)
        ->assignTemplateVar('title', constant('TEXT_OVERWRITE_FILES_TITLE'))
        ->assignTemplateVar('next', true);
        
    }


    /**
     * Renders plugins update page
     * @param WizardPage $page
     * @throws WizardException
     */
   public function pluginsUpdate(WizardPage $page) {
       
        $idb = Wizard::getInstance()->getDatabaseObject();
        $pluginFolder =_SRV_WEBROOT.'plugins/';

        $file =  _SRV_WEBROOT.'xtFramework/classes/class.plugin.php';
        if (file_exists($file)){
            include_once $file;

            /** @var array $rs_ar */
            $rs_ar = $idb->GetArray("SELECT * FROM ". DB_PREFIX."_plugin_products ");

            $this->addControllerCommand(ControllerCommand::factory()
                ->setAction(function(ControllerCommand $command) use ($pluginFolder,$idb, $rs_ar) {

                    set_exception_handler(array($this, 'handleException'));

                    $nextPlgMsg = 'starting update '.$rs_ar[0]['code'];

                    $command->setAbortOnError(false);

                    $command->setErrorMessage($nextPlgMsg);
                    return true;
                })
                ->setAbortOnError(false)
            );

            if(count($rs_ar)>0){
                 foreach($rs_ar as $k => $plugin_data){
                    
                     $this->addControllerCommand(ControllerCommand::factory()
                        ->setAction(function(ControllerCommand $command) use ($pluginFolder,$idb,$plugin_data, $rs_ar, $k) {

                            $nextPlgMsg = '';
                            if($k < count($rs_ar)-1)
                            {
                                $nextPlgMsg = 'starting update '.$rs_ar[$k+1]['code'];
                            }

                            $command->setAbortOnError(false);

                            $entry = $plugin_data['code'];
							$pl = new plugin($plugin_data['plugin_id']);
                            $pl->setPosition("installer");
                            $xml = $pluginFolder.$entry.'/installer/'.$entry.'.xml';
							if (file_exists($xml)){
								$xml_data = $pl->xmlToArray($xml);
								$current_version  = $this->adaptPluginUpdateVersion($plugin_data['version']);
								$new_version = $this->adaptPluginUpdateVersion($xml_data['xtcommerceplugin']['version']);

								global $is_pro_version;
                                $plgLicType = $pl->getRequiredStoreLicenseTypeFromXml($xml);
                                $plgLicTypeError = false;
                                if ($is_pro_version && $plgLicType == 'FREE')
                                {
                                    $plgLicTypeError = sprintf(constant('TEXT_ERROR_PLUGIN_ONLY_IN_FREE_VERSION'));
                                }
                                else if (!$is_pro_version && $plgLicType == 'PRO')
                                {
                                    $plgLicTypeError = sprintf(constant('TEXT_ERROR_PLUGIN_ONLY_IN_PRO_VERSION'));
                                }
                                if($plgLicTypeError)
                                {
                                    if($plugin_data['plugin_id'])
                                    {
                                        $pi = new plugin_installed();
                                        $pi->_setStatus($plugin_data['plugin_id'], false, true);
                                    }
                                    $command->setErrorMessage("<span class='msg-skipped'>" . $plgLicTypeError . " [" . $plugin_data['code'] . "] </span><br/><br/>".$nextPlgMsg);
                                }
							    else
                                {
                                    if ($current_version < $new_version)
                                    { // current installed vesrsion is lower than the new one
                                        if ($current_version < $this->adaptPluginUpdateVersion($xml_data['xtcommerceplugin']['minimumupdateversion']))
                                        { // current version is lower than the minimum update version  - can't proceed
                                            $command->setErrorMessage("<span class='msg-skipped'>" . constant('TEXT_PLUGIN_MINIMUM_UPDATE_VERSION') . " [" . $plugin_data['code'] . "] </span><br/><br/>".$nextPlgMsg);
                                        }
                                        else
                                        {
                                            try
                                            {
                                                $status = $plugin_data['plugin_status'];
                                                $pl->mode = 'update';
                                                $res = $pl->InstallPlugin($xml, 'false', $plugin_data['plugin_id']);
                                                $pli = new plugin_installed();
                                                $pli->_setStatus($plugin_data['plugin_id'], $status, true);
                                                $command->setErrorMessage("<span class='msg-success'>" . constant('TEXT_PLUGIN_UPDATED') . " [" . $plugin_data['code'] . "] </span><br/><br/>".$nextPlgMsg);
                                            } catch (Exception $e)
                                            {
                                                $pli = new plugin_installed();
                                                $pli->_setStatus($plugin_data['plugin_id'], $status, false);
                                                $command->setErrorMessage("<span class='msg-error'>" . constant('TEXT_FAILED_PLUGIN_UPDATED') . " [" . $plugin_data['code'] . "]: " . $e->getMessage() . "</span><br/><br/>".$nextPlgMsg);
                                            }
                                        }
                                    }
                                    elseif ($current_version == $new_version)
                                    { // current version equals the new one
                                        $command->setErrorMessage("<span class='msg-skipped'>" . constant('TEXT_PLUGIN_UP_TO_DATE') . " [" . $plugin_data['code'] . "] </span><br/><br/>".$nextPlgMsg);
                                    }
                                    elseif ($current_version > $new_version)
                                    { // current version is higher than the one provided with the update
                                        $command->setErrorMessage("<span class='msg-skipped'>" . constant('TEXT_PLUGIN_HAS_HIGHER_VERSION') . " [" . $plugin_data['code'] . "] </span><br/><br/>".$nextPlgMsg);
                                    }
                                }
                            }else{
								 $command->setErrorMessage("<span class='msg-skipped'>".constant('TEXT_PLUGIN_NO_FILES_FOUND')." [". $plugin_data['code']."] </span><br/><br/>".$nextPlgMsg);
							} 
                            return true;
                        })
                            ->setDescription(constant('TEXT_UPDATING_PLUGIN') . ' [' . $plugin_data['code'] . '] ')
                            ->setAbortOnError(false)
                    );
                 }
            }
        }
		 $page->assignTemplateVar('processing',constant('TEXT_UPDATING_PLUGIN'),true);
        $this->execAsyncAction($page);
    }


    /**
     * Renders language content ukpdate page
     * @param WizardPage $page
     * @throws WizardException
     */
    public function languageContentUpdate(WizardPage $page) {
        global $language,$store_handler;
        $idb = Wizard::getInstance()->getDatabaseObject();
        $lang_folder =_SRV_WEBROOT.'media/lang/';
        $download_path = _SRV_WEBROOT.'media/lang_downloads/';
        $prefix = DB_PREFIX .'_';
        $languages = $language->_getLanguageList();
        
        require_once _SRV_WEBROOT . 'xtFramework/classes/class.language_sync.php';
        // Download latest translations
        foreach ($languages as $lng_var) {
            // Controller command for installing language
            $this->addControllerCommand(ControllerCommand::factory()
                    ->setAction(function(ControllerCommand $command) use($lng_var) {
                        
                        if (function_exists('curl_version')) {
                            $lang_sync = new language_sync();
                            $lang_sync->_set(array('language' => $lng_var["code"]));
                        }
                            
                        return true;
                    })
                    ->setDescription(constant('TEXT_DOWNLOADING_LATEST_TRANSLATIONS') . ' [' . strtoupper($lng_var["code"]) . '] ')
                    ->setAbortOnError(false)
            );
        }
        
        
        foreach ($languages as $key => $val){            
            $this->addControllerCommand(ControllerCommand::factory()
                    ->setAction(function(ControllerCommand $command) use ($idb,$val,$lang_folder,$prefix,$download_path, $language) {
                        try{
                            $file = $lang_folder.$val['code'].'_content.yml';
                            $cnt_file = $download_path. $val['code'] . '_content.yml';
                            if (file_exists($cnt_file)) {
                                $file = $cnt_file;
                            }
                            $language->_importYML($file,$val['code']);
                            $language->initMissingKeys($val['code']);
                            $language->initEmptyKeys($val['code']);
                            //$command->getExecutableScript()->_importYML( $idb, $file, $val['code'], false, $prefix);
                        }catch (Exception $e){
                            $command->setErrorMessage("<span class='msg-error'>".constant('TEXT_FAILED_INSERT_LANG_CONTENT')." [". $val['code']."]: " . $e->getMessage() . "</span><br/>");
                        }
                        return true;
                    })
                        ->setDescription(constant('TEXT_INSERTING_LANG_CONTENT') . ' [' . $val['code'] . '] ')
                        ->setAbortOnError(false)
                );
        }
        
        $allStores = $store_handler->getStores();
        foreach($allStores as $store){
            foreach ($languages as $key => $val){
             $this->addControllerCommand(ControllerCommand::factory()
                    ->setAction(function(ControllerCommand $command) use ($idb,$prefix,$store,$val) {
                        try{

                            /** @var ADORecordSet $rs */
                           $rs = $idb->Execute("SELECT * FROM ".$prefix."language_content WHERE language_key='TEXT_CAT_STORE".$store['id']."' and class='admin' and language_code='".$val["code"]."'");
                           if ($rs->RecordCount()>0){
                               $idb->Execute("UPDATE ".$prefix."language_content SET language_value='".$store['text']."' WHERE language_key='TEXT_CAT_STORE".$store['id']."' and class='admin' and language_code='".$val["code"]."' ");
                           }else {

                               $insert['translated']=1;
                               $insert['language_code']=$val['code'];
                               $insert['language_key']='TEXT_CAT_STORE'.$store['id'];
                               $insert['language_value']=$store['text'];
                               $insert['class']='admin';
                               $idb->AutoExecute($prefix.'language_content',$insert); 
                           }

                        }catch (Exception $e){
                            $command->setErrorMessage("<span class='msg-error'>".constant('TEXT_FAILED_INSERT_LANG_CONTENT_MENU'). $e->getMessage() . "</span><br/>");
                        }
                        return true;
                    })
                        ->setDescription(constant('TEXT_INSERTING_LANG_CONTENT_MENU') )
                        ->setAbortOnError(false)
                );
           }
        }
        
        $page->assignTemplateVar('processing',constant('TEXT_INSERTING_LANG_CONTENT'),true);
        $this->execAsyncAction($page);
    }

    /**
     * @param WizardPage $page
     * @throws WizardException
     */
    public function _61postInstall(WizardPage $page)
    {
        $install_prefix = DB_PREFIX .'_';

        $update_sql = 'postInstall.sql';

        $_61Updater = new Update60xTo61x();

        $res = $_61Updater->_installSQL(Wizard::getInstance()->getDatabaseObject(), $update_sql, $install_prefix,'');

        foreach ($res as $key => $val) {
            $this->addControllerCommand(ControllerCommand::factory()
                ->setAction(function(ControllerCommand $command) use ($val) {
                    $idb = Wizard::getInstance()->getDatabaseObject();
                    try{
                        $idb->Execute($val);
                    }catch (Exception $e){
                        /*'1060 - Duplicate column name','1062 - Duplicate entry', '1050- table alredy exists','1091 -  Can't DROP '%s'; check that column/key exists'*/
                        $allowed_error_codes = array(1062,1061,1060,1050,1091, 1054, 1091);
                        if (in_array($e->getCode(),$allowed_error_codes)) {
                            $command->setErrorMessage("<span class='msg-skipped'>".constant('TEXT_QUERY_SKIPPED')."  " . $e->getMessage() . "</span><br/>");
                        }else{
                            $command->setErrorMessage("<span class='msg-error'>".constant('TEXT_QUERY_FAILED')." " . $e->getMessage() . "</span><br/>");
                            return false;
                        }
                    }

                    return true;
                })
                ->setDescription(constant('TEXT_EXECUTING_QUERY').$val . "<br/>")
                ->setAbortOnError(true)
            );
        }

        $page->assignTemplateVar('processing',constant('TEXT_UPDATING_DB_STORES'),true);
        $this->execAsyncAction($page);
    }

    /**
     * @param WizardPage $page
     * @throws WizardException
     */
    public function installKco(WizardPage $page)
    {
        $msg = 'Install KCO';

        $this->addControllerCommand(ControllerCommand::factory()
            ->setAction(function(ControllerCommand $command) use ($msg) {
                try{
                    global $xtPlugin, $is_pro_version;
                    if(!$is_pro_version && !array_key_exists('xt_klarna_kco', $xtPlugin->active_modules))
                    {
                        $plugin = new plugin();
                        $plugin->setPosition('installer');

                        $xml = _SRV_WEBROOT.'plugins/xt_klarna_kco/installer/xt_klarna_kco.xml';
                        if (file_exists($xml))
                        {
                            $plugin->InstallPlugin($xml);
                        }
                        else throw new Exception();
                    }
                }catch (Exception $e){
                    $command->setErrorMessage("<span class='msg-skipped'>".$msg."  " . $e->getMessage() . "</span><br/>Installieren Sie im Backend xt_klarna_kco !");
                }

                return true;
            })
            ->setDescription($msg. "<br/>")
            ->setAbortOnError(true)
        );


        $page->assignTemplateVar('processing',$msg,true);
        $this->execAsyncAction($page);
    }

    /**
     * clean cache page
     * @param WizardPage $page
     * @throws WizardException
     */
    public function cleanCache(WizardPage $page)
    {
        global $store_handler;
        $msg = 'Clean Cache';

        // clean cache
        $this->addControllerCommand(ControllerCommand::factory()
            ->setAction(function(ControllerCommand $command) use ($store_handler) {
                try{
                    if(file_exists(_SRV_WEBROOT._SRV_WEB_PLUGINS.'xt_cleancache/classes/class.xt_cleancache.php'))
                    {
                        include_once _SRV_WEBROOT._SRV_WEB_PLUGINS.'xt_cleancache/classes/class.xt_cleancache.php';
                        $cc = new cleancache();
                        $cc->cleanTemplateCached('all');
                    }
                }catch (Exception $e){
                    $command->setErrorMessage("<span class='msg-error'>Clean cache failed " . $e->getMessage() . "</span><br/>");
                    return false;
                }

                return true;
            })
            ->setDescription("Clean cache<br/>")
            ->setAbortOnError(false)
        );

        $page->assignTemplateVar('processing',$msg,true);
        $this->execAsyncAction($page);
    }

    /**
     * Finish Update page
     * @param WizardPage $page
     * @throws WizardException
     */
    public function finishUpdatePage(WizardPage $page)
    {
        global $is_pro_version;
        $page->setTemplate('templates/finish_update_page.html')->assignTemplateVar('is_pro_version', $is_pro_version);;
    }

    /**
     * Renders index page
     * @param WizardPage $page
     * @throws WizardException
     */
	public function displayStartPage(WizardPage $page)
    {
        global $is_pro_version;

        $httpsCheck = $this->checkHttpsAvailable();
		// Reset
		Wizard::getInstance()->setWizardType(null);
		$page->setTemplate('templates/index.html')
            ->assignTemplateVar('show_language', true)
            ->assignTemplateVar('httpsCheck', $httpsCheck);

        $highestTargetVersion = $this->getHighestTargetVersion();
        $page->setTemplate('templates/index.html')->assignTemplateVar('highestTargetVersion', $highestTargetVersion);
	}
	
	/**
	 * Get pages
	 * @return string
	 */
	protected function getPagesXml() {
		return file_get_contents(ROOT_DIR_PATH . 'pages.xml');
	}

    /**
     * Import language helper function
     * @param ADOConnection $idb
     * @param string $language_code
     * @param string $install_prefix
     * @param ControllerCommand $command
     * @return string|number
     */
	public function _importLang( $idb, $language_code, $install_prefix, ControllerCommand $command) {
		global $filter;
	
		require_once _SRV_WEBROOT.'xtFramework/library/phpxml/xml.php';

		if(empty($install_prefix)) $install_prefix = 'xt';
	
		// $language, $currency
		// well, import language file
	
		$path = 'media/lang/';
		$downlaod_path = 'media/lang_downloads/';
		$file = _SRV_WEBROOT . $path . $language_code . '.xml';
		$cnt_file = _SRV_WEBROOT . $downlaod_path . $language_code . '_content.yml';
		if (!file_exists($cnt_file)) {
			$cnt_file = _SRV_WEBROOT . $path . $language_code . '_content.yml';
		}
		$msg = array();
		
		if (!file_exists($file)) return 'language file:'. $file.' not found';
		if (!file_exists($cnt_file)) return 'language file:'. $cnt_file.' not found';
	

        // add language
        $xml = file_get_contents($file);
        $xml_data = XML_unserialize($xml);
        $msg[] = constant('TEXT_READING_XML_FILE');

        // check if language allready existing
        $code = $filter->_filter($xml_data['xtcommerce_language']['code'],'lng');
        $lng = $this->_getLanguageList($idb, 'admin', 'code', $install_prefix);
        $curr = $filter->_filter($xml_data['xtcommerce_language']['default_currency'],'cur');


        $_data = array();
        if (array_key_exists($code, $lng) && is_array($lng[$code]))  $_data['languages_id']=$lng[$code]['languages_id'];

        $_data['name'] = $filter->_filter($xml_data['xtcommerce_language']['name']);
        $_data['code'] = $code;
        $_data['content_language']=$code;

        $_data['default_currency'] = $curr;
        $_data['font'] = $filter->_filter($xml_data['xtcommerce_language']['font']);
        $_data['font_position'] = $filter->_filter($xml_data['xtcommerce_language']['font_position']);
        $_data['font_size'] = $filter->_filter($xml_data['xtcommerce_language']['font_size']);
        $_data['image'] = $filter->_filter($xml_data['xtcommerce_language']['image']);
        $_data['language_charset'] = 'utf-8';
        $_data['setlocale'] = $filter->_filter($xml_data['xtcommerce_language']['setlocale']);


        $rtn = $this->_save_lang($idb, $_data, $install_prefix);
        $msg[] = constant('TEXT_SAVING_LANGUAGE');
        if ($rtn!=-1) return $rtn;
        // import definitions
        $replace = false;
        if (isset($data['replace_existing'])) $replace = true;
        //$language->_importXML($cnt_file,$code,$replace);
        $this->_importYML($idb, $cnt_file, $code,$replace, $install_prefix);

        $msg[] = constant('TEXT_IMPORTING_TRANSLATIONS');

        // check for currencies
        $installed_currencies = $this->_getCurrencyList($idb, 'admin', 'code', $install_prefix);
        if (!array_key_exists($curr, $installed_currencies)) {
            // add currency
            $curr_data = array();
            $curr_data['code']=$curr;
            $curr_data['dec_point']=',';
            $curr_data['decimals']='2';
            $curr_data['prefix']=$curr;
            $curr_data['suffix']='';
            $curr_data['thousands_sep']='.';
            $curr_data['title']=$curr;
            $curr_data['value_multiplicator']='1';
            $this->_save_currency($idb, $curr_data, $install_prefix);
            $msg[] = constant('TEXT_SAVING_CURRENCY');
        }

        // duplicate country definition
        // check if lng country list exists
        $country_file = _SRV_WEBROOT . $path . $code . '_countries.csv';
        if (!file_exists($country_file)) {
            // load english ones
            $country_file = _SRV_WEBROOT.$path.'en_countries.csv';
            $this->_importCountries($idb, $country_file,$code, $install_prefix);
        } else {
            $this->_importCountries($idb, $country_file,$code, $install_prefix);
        }

        $msg[] = constant('TEXT_IMPORT_COUNTRIES');

        // load stopwordlist
        $stopwords_file = _SRV_WEBROOT.$path.$code.'_stop_words.csv';
        if (file_exists($stopwords_file)) {
            // load english ones
            $this->_importStopWords($idb, $stopwords_file,$code, $install_prefix);
            $msg[] = constant('TEXT_IMPORT_STOPWORDS');
        }
        // mail templates
        $this->_installMailTemplates($idb, $code, 15, $install_prefix);

        $msg[] = constant('TEXT_INSTALL_MAIL_TEMPLATES');
        $command->setErrorMessage(join("<br />", $msg));
        // deactivate for all stores
        return -1;

	}

    /**
     * @param $idb ADOConnection
     * @param $lng
     * @param $max_id
     * @param $prefix
     * @return bool|int
     */
	public function _installMailTemplates( $idb, $lng, $max_id, $prefix) {
		if ($max_id==0 || empty($max_id)) return false;

		$mail_dir = _SRV_WEBROOT.'xtWizard/languages/'.$lng.'/mails';
		for ($i=1;$i<$max_id+1;$i++) {
	
			if (file_exists($mail_dir.'/'.$i.'_'.$lng.'_txt.txt')) {
	
				$file_prefix = $i.'_'.$lng.'_';
				
				$html_content = $this->_getFileContent($mail_dir.'/'.$file_prefix.'html.txt');
				$txt_content = $this->_getFileContent($mail_dir.'/'.$file_prefix.'txt.txt');
				$subject = $this->_getFileContent($mail_dir.'/'.$file_prefix.'subject.txt');
	
				$insert_array = array();
				$insert_array['tpl_id']=$i;
				$insert_array['language_code']=$lng;
				$insert_array['mail_body_html']=$html_content;
				$insert_array['mail_body_txt']=$txt_content;
				$insert_array['mail_subject']=$subject;
				try {
					$idb->AutoExecute($prefix.'mail_templates_content',$insert_array);
				} catch (exception $e) {
					return $e->msg;
				}
	
			}
		}
	
		return -1;
	
	}

    /**
     * @param $idb ADOConnection
     * @param $file
     * @param $code
     * @param $prefix
     */
	public function _importStopWords( $idb, $file, $code, $prefix) {
	
		if (!file_exists($file)) return;
	
		$handle = fopen ($file,"r");
		$idb->Execute("DELETE FROM ".$prefix."seo_stop_words WHERE language_code='".$code."'");
		while ( ($data = fgetcsv ($handle, 1000, ";",'"')) !== FALSE ) {
	
			$insert_array=array();
			$insert_array['language_code']=$code;
			$insert_array['stopword_lookup']=$data[0];
			$insert_array['stopword_replacement']=$data[1];
			$insert_array['replace_word']=$data[2];
	
			$idb->AutoExecute($prefix."seo_stop_words",$insert_array);
		}
	
		fclose ($handle);
	
	
	}

    /**
     * @param $idb ADOConnection
     * @param $file
     * @param $code
     * @param $prefix
     */
	public function _importCountries( $idb, $file,$code, $prefix) {
	
		if (!file_exists($file)) return;
	
		$handle = fopen ($file,"r");
		$idb->Execute("DELETE FROM ".$prefix."countries_description WHERE language_code='".$code."'");
		while ( ($data = fgetcsv ($handle, 1000, ";",'"')) !== FALSE ) {
	
			$insert_array=array();
			$insert_array['language_code']=$code;
			$insert_array['countries_name']=$data[1];
			$insert_array['countries_iso_code_2']=$data[2];
	
			$idb->AutoExecute($prefix."countries_description",$insert_array);
		}
	
		fclose ($handle);
	
	
	}

    /**
     * @param $idb  ADOConnection
     * @param $curr_data
     * @param $prefix
     * @return mixed|void
     */
	public function _save_currency( $idb, $curr_data, $prefix)
	{
		try {
			$idb->AutoExecute($prefix.'currencies',$curr_data);
		} catch (exception $e) {
			echo 'Error in importing currency '.$e->msg;
			return $e->msg;
		}
	}

    /**
     * @param $idb  ADOConnection
     * @param string $list_type
     * @param string $index
     * @param $prefix
     * @return mixed
     */
	public function _getCurrencyList( $idb, $list_type = 'store', $index='', $prefix){
	
		$qry_where = " where c.currencies_id != '' ";
	
		$qry =  "SELECT * FROM " . $prefix."currencies c ".$qry_where." ";

		$data = array();

		/** @var ADORecordSet $record */
		$record = $idb->Execute($qry);
		while(!$record->EOF){
	
			$record->fields['id'] = $record->fields['code'];
			$record->fields['text'] = $record->fields['title'];
	
			if ($index=='') $data[$record->fields['code']] = $record->fields;
			if ($index=='code') $data[$record->fields['code']] = $record->fields;
			$record->MoveNext();
		}$record->Close();
	
		return $data;
	}

    /**
     * @param $idb ADOConnection
     * @param $file
     * @param $code
     * @param bool $replace
     * @param $prefix
     */
	public function _importYML( $idb, $file, $code, $replace = false, $prefix) {
	
		if (!file_exists($file)) return;
	
		$lines = file ($file);
	
		// load language definitions
		$definitions = array();
        /** @var ADORecordSet $rs */
		$rs = $idb->Execute("SELECT language_key FROM ".$prefix."language_content WHERE language_code='".$code."'");
		if ($rs->RecordCount()>0) {
			while (!$rs->EOF) {
				$definitions[$rs->fields['language_key']]='1';
				$rs->MoveNext();
			}
		}
	
	
		foreach ($lines as $line_num => $line) {
			$delimiterPos = strpos($line, '=', 0);
			 
			if ($delimiterPos === false) {
				continue;
			}
			$systemPart = substr($line, 0, $delimiterPos);
			$value = substr($line, $delimiterPos+1);
			list($plugin, $class, $key) = explode('.', $systemPart);
				
			if (!isset($definitions[$key]) && $key!='' && $key!='new')   {
				// key not existing
				$insert_data = array();
				$insert_data['language_key']=$key;
				$insert_data['language_code']=$code;
				$insert_data['language_value']=trim(str_replace("\n",'',$value));
				$insert_data['class']=$class;
				$insert_data['plugin_key']=$plugin;
				$insert_data['translated']='1';
				$idb->AutoExecute($prefix.'language_content',$insert_data);
			}
				
		}
	
		// now get untranslated definitions and insert //TODO check if EN is existing
		$sql = "SELECT * FROM ".$prefix."language_content a WHERE a.language_code='en' and a.language_key NOT IN (SELECT language_key FROM ".$prefix."language_content b WHERE b.language_code='".$code."')";
		$rs = $idb->Execute($sql);
		if ($rs->RecordCount()>0) {
			while (!$rs->EOF) {
				$insert_data = array();
				$insert_data['language_key']=$rs->fields['language_key'];
				$insert_data['language_code']=$code;
				$insert_data['language_value']=$rs->fields['language_value'];
				$insert_data['class']=$rs->fields['class'];
				$insert_data['plugin_key']=$rs->fields['plugin_key'];
				$insert_data['translated']='0';
				$idb->AutoExecute($prefix."language_content",$insert_data);
				$rs->MoveNext();
			}
		}
	
	
	}

    /**
     * @param $idb ADOConnection
     * @param $_data
     * @param $prefix
     * @return int|string
     */
	public function _save_lang( $idb, $_data, $prefix)
	{
		try {
			$idb->Execute('DELETE FROM '.$prefix.'languages WHERE CONTENT_LANGUAGE=? AND CODE=?', array($_data['content_language'], $_data['code']));
			$idb->AutoExecute($prefix.'languages',$_data);
		} catch (exception $e) {
			return $e->getMessage();
		}
		return -1;
	}

    /**
     * @param $idb  ADOConnection
     * @param $active_country
     * @param string $prefix
     * @return Exception|int
     *
     */
    public function _activateStoreCountry( $idb, $active_country,$prefix='') {
        $active_country = substr($active_country, 0, 2);
        // deactivate all countries
        try {
            $query = "UPDATE ".$prefix."countries SET status = 0";
            $idb->Execute($query);
        } catch (exception $e) {
            // echo $val;
            return $e;
        }

        try {
            $query = "UPDATE ".$prefix."countries SET status = 1 WHERE countries_iso_code_2 ='".$active_country."'";
            $idb->Execute($query);
        } catch (exception $e) {
            // echo $val;
            return $e;
        }

        $query = "UPDATE ".$prefix."config_1 SET config_value = '".$active_country."' WHERE config_key ='_STORE_COUNTRY'";
        $idb->Execute($query);

        if ($active_country=='BR') {
            $query = "UPDATE ".$prefix."config_1 SET config_value = 'BRL' WHERE config_key ='_STORE_CURRENCY'";
            $idb->Execute($query);
        }
        // set as home country
        return -1;
    }

    /**
     * @param $idb ADOConnection
     * @param array $countries
     * @param string $prefix
     * @return Exception|int
     */
    public function _activateCountries( $idb, $countries = array(), $prefix='')
    {
        foreach($countries as $active_country)
        {
            $active_country = substr($active_country, 0, 2);

            try
            {
                $query = "UPDATE " . $prefix . "countries SET status = 1 WHERE countries_iso_code_2 ='" . $active_country . "'";
                $idb->Execute($query);
            } catch (Exception $e)
            {
                // echo $val;
                return $e;
            }
        }

        return -1;
    }

    /**
     * @param $idb ADOConnection
     * @param string $list_type
     * @param string $index
     * @param $prefix
     * @return array
     */
	public function _getLanguageList( $idb, $list_type = '',$index='', $prefix){

        $qry_where = '';
		if ($list_type!='all')
		$qry_where = " WHERE l.language_status = '1'";

        $data = array();

		/** @var ADORecordSet $record */
		$record = $idb->Execute("SELECT * FROM " . $prefix . "languages l ".$qry_where." order by sort_order");
		while(!$record->EOF){
			$record->fields['id'] = $record->fields['code'];
			$record->fields['text'] = $record->fields['name'];
			$record->fields['icon'] = $record->fields['image'];
			$record->fields['edit'] = $record->fields['allow_edit'];
	
			if ($index=='') $data[] = $record->fields;
			if ($index=='code') $data[$record->fields['code']] = $record->fields;
			$record->MoveNext();
		}$record->Close();
	
		return $data;
	}

    /**
     * deactivate all languages, only activate standard language (requirement for trusted shops)
     *
     * @param $idb ADOConnection
     * @param string $active_language
     * @param mixed $prefix
     * @return Exception|int
     */
	public function _activateLanguage( $idb, $active_language,$prefix='') {
		 
		$active_language=substr($active_language,0,2);
	
		try {
			$query = "UPDATE ".$prefix."languages SET language_status = 1";
			$idb->Execute($query);
		} catch (exception $e) {
			// echo $val;
			return $e;
		}
		
		$query = "UPDATE ".$prefix."config_1 SET config_value = '".$active_language."' WHERE config_key ='_STORE_LANGUAGE'";
		$idb->Execute($query);

        $query = "UPDATE ".$prefix."config_1 SET config_value = '".$active_language."' WHERE config_key ='_STORE_HREFLANG_DEFAULT'";
        $idb->Execute($query);
	
		return -1;
	
	}

    /**
     * @param $idb ADOConnection
     * @param $country
     * @param string $prefix
     * @return int
     * @throws WizardException
     */
	public function _taxSetup( $idb, $country, $prefix='') {
		
		switch ($country) {
			case 'BE': // Belgium
				$this->setEUTax(21,12,$prefix);
				break;
			case 'BG': // Bulgaria
				$this->setEUTax(20,9,$prefix);
				break;
			case 'DK': // Denmark
				$this->setEUTax(25,0,$prefix);
				break;
			case 'DE': // Germany
				$this->setEUTax(19,7,$prefix);
				break;
			case 'EE': // Estonia
				$this->setEUTax(20,9,$prefix);
				break;
			case 'FI': // Finland
				$this->setEUTax(23,13,$prefix);
				break;
			case 'FR': // France
				$this->setEUTax(19.6,7,$prefix);
				break;
			case 'GR': // greece
				$this->setEUTax(23,13,$prefix);
				break;
			case 'GB': // UK
				$this->setEUTax(20,5,$prefix);
				break;
			case 'IE': // ireland
				$this->setEUTax(23,13.5,$prefix);
				break;
			case 'LV': // Latvia
				$this->setEUTax(21,12,$prefix);
				break;
			case 'LT': // Lithuania
				$this->setEUTax(21,9,$prefix);
				break;
			case 'MT': // Malta
				$this->setEUTax(18,5,$prefix);
				break;
			case 'LU': // Luxembourg
				$this->setEUTax(15,12,$prefix);
				break;
			case 'NL': // Netherlands
				$this->setEUTax(19,6,$prefix);
				break;
			case 'PL': // Poland
				$this->setEUTax(23,8,$prefix);
				break;
			case 'PT': // Portugal
				$this->setEUTax(23,8,$prefix);
				break;
			case 'RO': // Romania
				$this->setEUTax(24,9,$prefix);
				break;
			case 'SE': // Sweden
				$this->setEUTax(25,12,$prefix);
				break;
			case 'SI': // Slovenia
				$this->setEUTax(20,8.5,$prefix);
				break;
			case 'ES': // Spain
				$this->setEUTax(21,10,$prefix);
				break;
			case 'CZ': // Czech Republic
				$this->setEUTax(20,14,$prefix);
				break;
			case 'HU': // Hungary
				$this->setEUTax(27,18,$prefix);
				break;
			case 'CY': //
				$this->setEUTax(17,8,$prefix);
				break;
			case 'CH': //
				$this->setEUTax(8,2.5,$prefix);
				break;
	
	
			case 'BR':
                $query = "INSERT INTO ".$prefix."tax_class(tax_class_title) VALUES ('No Tax')";
				$idb->Execute($query);
				$id1 = $idb->insert_ID();
                $query = "INSERT INTO ".$prefix."tax_rates( `tax_zone_id`, `tax_class_id`, `tax_rate`, `tax_rate_countries`) VALUES ( 31, ".(int)$id1.", 0.0000, '')";
				$idb->Execute($query);
                $query = "INSERT INTO ".$prefix."tax_rates( `tax_zone_id`, `tax_class_id`, `tax_rate`, `tax_rate_countries`) VALUES ( 0, ".(int)$id1.", 0.0000, '')";
				$idb->Execute($query);
				break;
			default:
				
                $query = "INSERT INTO ".$prefix."tax_class(tax_class_title) VALUES ( 'Standardsatz')";
				$idb->Execute($query);
				$id1 = $idb->insert_ID();
                $query = "INSERT INTO ".$prefix."tax_class(tax_class_title) VALUES ('Ermäßigter Steuersatz')";
				$idb->Execute($query);
				$id2 = $idb->insert_ID();
                $query = "INSERT INTO ".$prefix."tax_class(tax_class_title, is_digital_tax) VALUES ('Digitale Artikel', 1)";
				$idb->Execute($query);
				$id3 = $idb->insert_ID();
	
                $query = "INSERT INTO ".$prefix."tax_rates( `tax_zone_id`, `tax_class_id`, `tax_rate`, `tax_rate_countries`) VALUES ( 31, ".(int)$id1.", 19.0000, '')";
				$idb->Execute($query);
                $query = "INSERT INTO ".$prefix."tax_rates( `tax_zone_id`, `tax_class_id`, `tax_rate`, `tax_rate_countries`) VALUES ( 31, ".(int)$id2.", 7.0000, '')";
				$idb->Execute($query);
                $query = "INSERT INTO ".$prefix."tax_rates( `tax_zone_id`, `tax_class_id`, `tax_rate`, `tax_rate_countries`) VALUES ( 0, ".(int)$id1.", 0.0000, '')";
				$idb->Execute($query);
                $query = "INSERT INTO ".$prefix."tax_rates( `tax_zone_id`, `tax_class_id`, `tax_rate`, `tax_rate_countries`) VALUES ( 0, ".(int)$id2.", 0.0000, '')";
				$idb->Execute($query);
	
	
				break;
		}
	
		$this->setDigitalProductTaxes($prefix);
		return -1;
	}

    /**
     * @param $idb ADOConnection
     * @param $country
     * @param $install_eu_rates
     * @param string $prefix
     * @param bool $truncate truncate class and rates table and recreate entries
     * @param bool $id1  id des standard steuersatzes
     * @param bool $id2  id des ermäßigten steuersatzes
     * @param bool $id3  id des steuersatzes digital
     * @return int
     * @throws WizardException
     */
    public function _taxSetupJuly2021( $idb, $country, $install_eu_rates, $prefix='', $truncate = true, $id1 = false, $id2 = false, $id3 = false)
    {
        if($truncate)
        {
            $idb->Execute('TRUNCATE '.$prefix.'tax_class');
            $idb->Execute('TRUNCATE '.$prefix.'tax_rates');

            $query = "INSERT INTO ".$prefix."tax_class(tax_class_title) VALUES ( 'Standardsatz')";
            $idb->Execute($query);
            $id1 = $idb->insert_ID();
            $query = "INSERT INTO ".$prefix."tax_class(tax_class_title) VALUES ('Ermäßigter Steuersatz')";
            $idb->Execute($query);
            $id2 = $idb->insert_ID();
            $query = "INSERT INTO ".$prefix."tax_class(tax_class_title, is_digital_tax) VALUES ('Digitale Artikel', 1)";
            $idb->Execute($query);
            $id3 = $idb->insert_ID();
        }

        $tax_rates_eu = [
            'AT' => [20,10],
            'BE' => [21,6],
            'BG' => [20,9],
            'CY' => [19,5],
            'CZ' => [21,10],
            'DE' => [19,7],
            'DK' => [25,25],
            'EE' => [20,9],
            'GR' => [24,6],
            'ES' => [21,10],
            'FI' => [24,10],
            'FR' => [20,10],
            'HR' => [25,5],
            'HU' => [27,5],
            'IE' => [23,9],
            'IT' => [22,5],
            'LT' => [21,5],
            'LU' => [17,8],
            'LV' => [21,12],
            'MT' => [18,5],
            'NL' => [21,9],
            'PL' => [23,5],
            'PT' => [23,6],
            'RO' => [19,5],
            'SE' => [25,6],
            'SI' => [22,9.5],
            'SK' => [20,10],
        ];

        $tax_rates = $tax_rates_eu;
        if(!$install_eu_rates)
        {
            $tax_rates = [];
            if(array_key_exists($country, $tax_rates_eu))
                $tax_rates[$country] = $tax_rates_eu[$country];
            else $tax_rates[''] = [0,0];
        }
        $zone = $idb->GetOne('SELECT zone_id FROM '.$prefix.'countries WHERE countries_iso_code_2 = ?',[$country]);

        $query = "INSERT INTO ".$prefix."tax_rates( `tax_zone_id`, `tax_class_id`, `tax_rate`, `tax_rate_countries`) VALUES ( ?, ?, ?, ?)";
        foreach($tax_rates as $country => $rates)
        {
            if(!$install_eu_rates) $country = '';
            if ($id1) $idb->Execute($query, [$zone, $id1, $rates[0], $country]);
            if ($id2) $idb->Execute($query, [$zone, $id2, $rates[1], $country]);
        }
        $idb->Execute($query, [0, $id1, 0, '']);
        $idb->Execute($query, [0, $id2, 0, '']);

        if ($id3) $this->setDigitalProductTaxes($prefix);
        return -1;
    }

    /**
     * @param $normal
     * @param $optional
     * @param $prefix
     * @throws WizardException
     */
	function setEUTax($normal,$optional,$prefix) {
		$idb = Wizard::getInstance()->getDatabaseObject();
		
        $query = "INSERT INTO ".$prefix."tax_class(tax_class_title) VALUES ( 'Standardsatz')";
		$idb->Execute($query);
		$id1 = $idb->insert_ID();
        $query = "INSERT INTO ".$prefix."tax_class(tax_class_title) VALUES ('Ermäßigter Steuersatz')";
		$idb->Execute($query);
		$id2 = $idb->insert_ID();
        $query = "INSERT INTO ".$prefix."tax_class(tax_class_title, is_digital_tax) VALUES ('Digitale Artikel', 1)";
		$idb->Execute($query);
		$id3 = $idb->insert_ID();
	
        $query = "INSERT INTO ".$prefix."tax_rates( `tax_zone_id`, `tax_class_id`, `tax_rate`, `tax_rate_countries`) VALUES ( 31, '".(int)$id1."', '".$normal."', '')";
		$idb->Execute($query);
        $query = "INSERT INTO ".$prefix."tax_rates( `tax_zone_id`, `tax_class_id`, `tax_rate`, `tax_rate_countries`) VALUES ( 31, '".(int)$id2."', '".$optional."', '')";
		$idb->Execute($query);
        $query = "INSERT INTO ".$prefix."tax_rates( `tax_zone_id`, `tax_class_id`, `tax_rate`, `tax_rate_countries`) VALUES ( 0, '".(int)$id1."', 0.0000, '')";
		$idb->Execute($query);
        $query = "INSERT INTO ".$prefix."tax_rates( `tax_zone_id`, `tax_class_id`, `tax_rate`, `tax_rate_countries`) VALUES ( 0, '".(int)$id2."', 0.0000, '')";
		$idb->Execute($query);
	}

    /**
     * @param $prefix
     * @throws WizardException
     */
	protected function setDigitalProductTaxes($prefix) {
		$idb = Wizard::getInstance()->getDatabaseObject();

        $tax_rates_eu = [
            'AT' => 20,
            'BE' => 21,
            'BG' => 20,
            'CY' => 19,
            'CZ' => 21,
            'DE' => 19,
            'DK' => 25,
            'EE' => 20,
            'GR' => 23,
            'ES' => 21,
            'FI' => 24,
            'FR' => 5.5,
            'HR' => 25,
            'HU' => 27,
            'IE' => 23,
            'IT' => 22,
            'LT' => 211,
            'LU' => 3,
            'LV' => 21,
            'MT' => 18,
            'NL' => 21,
            'PL' => 23,
            'PT' => 23,
            'RO' => 24,
            'SE' => 25,
            'SI' => 22,
            'SK' => 20,
            ''   => 0
        ];
        $tax_class_id = $idb->GetOne("select tax_class_id FROM ".$prefix."tax_class WHERE tax_class_title='Digitale Artikel'");
        $query = "INSERT INTO ".$prefix."tax_rates( `tax_zone_id`, `tax_class_id`, `tax_rate`, `tax_rate_countries`) VALUES ( 31, ?, ?, ?)";
		foreach ($tax_rates_eu as $country => $rate)
        {
            $idb->Execute($query, [$tax_class_id, $rate, $country]);
        }
	}

	function adaptPluginUpdateVersion($version){
		$v = explode(".",$version);
		$final = '';
		for($i=0;$i<count($v);$i++){
			if (strlen((string)$v[$i])==1){
				$final .= '0'.$v[$i];  // adding leadig 0
			}else if (strlen((string)$v[$i])==2){
				$final .= $v[$i];
			}
		}
		return (int)$final;
	}


    /**
     * @param $page
     * @param array $error
     * @throws GuzzleException
     */
	function pageNotification($page,$error=array()) {

	    if (isset($_GET['ajaxLoad'])) return;

        $webservice = 'https://webservices.xt-commerce.com/';

        try {

            $client = new GuzzleHttp\Client ( [
                'base_uri' => $webservice,
                'verify'=>false,
                'auth' => [
                    'public',
                    'public'
                ]
            ] );

            $lic_data = $this->getLicenseFileInfo(array('key','mailaddress'));
            $data = [];
            $data['license_key']=$lic_data['key'];
            $data['page']=$page;
            if (isset($error)) $data['error']=serialize($error);

            $response = $client->post ( $webservice.'license/step_notification', [
                'json' => $data
            ] );

        } catch (GuzzleHttp\Exception\ClientException $e) {
            echo $e->getMessage().'<br/>'.  nl2br( $e->getTraceAsString());
        }
    }

    /**
     * read license file and return requested parameters
     * @param array $params
     * @return array|void
     */
    public function getLicenseFileInfo($params=array()) {

        if (count($params)==0) return;
        $return=array();
        $_lic = _SRV_WEBROOT . 'lic/license.txt';


        $_file_content = file($_lic);
        foreach ($_file_content as $bline_num => $bline) {
            if (strpos($bline, ':') !== FALSE) {

                $val_line = substr($bline,0,strpos($bline, ':'));

                if (in_array($val_line,$params)) {
                    $return[$val_line]=trim(substr($bline,strpos($bline, ':')+1));
                }
            }

        }
        return $return;
    }
	
    function checkModeRewrite()
    {
        if(!empty($_SERVER['IIS_WasUrlRewritten']))
            $mod_rewrite_found = true;
        else if(array_key_exists('HTTP_MOD_REWRITE',$_SERVER))
            $mod_rewrite_found = true;
        else if( array_key_exists('REDIRECT_URL', $_SERVER))
            $mod_rewrite_found = true;
        else
            $mod_rewrite_found = false;

        // $mod_rewrite_found = false; // to test stream_socket_client

        if(!$mod_rewrite_found)
        {
            $connection_timeout = 2;
            $data_read_timeout = 2;

            $context = stream_context_create();

            $schema = '';
            if(isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS']))
            {
                $schema = 'ssl://';
                stream_context_set_option($context, 'ssl', 'verify_peer', false); // fix ssl certificate verify issues, eg mammp on osx
            }

            $stream_source = $schema.$_SERVER['HTTP_HOST'];
            if(strpos(':', $stream_source) == false)
            {
                $stream_source .= ':'. $_SERVER["SERVER_PORT"];
            }

            $remote = stream_socket_client($stream_source ,
                $errno, $errstr, $connection_timeout, STREAM_CLIENT_CONNECT, $context);

            if(is_resource($remote))
            {
                $response = '';
                if(stream_set_timeout($remote, $data_read_timeout))
                {
                    $url = _SRV_WEB . 'xtWizard/test_mod_rewrite.html';
                    $host = $_SERVER['HTTP_HOST'];

                    $headers[] = "GET ".$url." HTTP/1.1";
                    $headers[] = "Host: ".$host;
                    $headers[] = "Connection: close";

                    fputs($remote, join("\r\n", $headers) . "\r\n\r\n");

                    while (!feof($remote))
                    {
                        $response .= fread($remote, 1024);
                    }

                    $stream_info = stream_get_meta_data($remote);

                    if ($stream_info['timed_out']) {
                        WizardLogger::getInstance()->log('checkModeRewrite: Data connection timed out: '.$stream_source);
                    }
                }
                else { WizardLogger::getInstance()->log('checkModeRewrite: Could not set timeout for socket: '.$stream_source);}

                fclose($remote);

                $mod_rewrite_found = strpos($response, 'test_mod_rewrite_ok') != false;
            }
            else { WizardLogger::getInstance()->log('checkModeRewrite: Could not create socket: '.$stream_source);}
        }

        return $mod_rewrite_found;
    }

    /**
     * @param int $port
     * @return array 0 - not available, 1 - available but http, 2 - available and called over https
     */
    function checkHttpsAvailable($port = 443)
    {
        $isSecure = checkHTTPS();
        $result = ['httpsAvailable' => 0, 'httpsUrl' => ''];

        $url = _SRV_WEB . 'xtWizard/test_https.html';
        $host = $_SERVER['HTTP_HOST'];

        if($isSecure)
        {
            $httpsUrl = 'https://'.$_SERVER['HTTP_HOST'];
            $httpsUrl .= _SRV_WEB.'xtWizard/index.php';
            $result = ['httpsAvailable' => 2, 'httpsUrl' => $httpsUrl];
        }
        else
        {
            $connection_timeout = 2;
            $data_read_timeout = 2;

            $context = stream_context_create();

            $schema = 'ssl://';
            stream_context_set_option($context, 'ssl', 'verify_peer', false); // fix ssl certificate verify issues, eg mammp on osx

            $stream_source = $schema . $_SERVER['HTTP_HOST'];
            if (strpos(':', $stream_source) == false)
            {
                $stream_source .= ':' . $port;
            }

            $remote = stream_socket_client($stream_source,
                $errno, $errstr, $connection_timeout, STREAM_CLIENT_CONNECT, $context);

            if (is_resource($remote))
            {
                $response = '';

                if (stream_set_timeout($remote, $data_read_timeout))
                {


                    $headers[] = "GET " . $url . " HTTP/1.1";
                    $headers[] = "Host: " . $host;
                    $headers[] = "Connection: close";

                    fputs($remote, join("\r\n", $headers) . "\r\n\r\n");


                    while (!feof($remote))
                    {
                        $response .= fread($remote, 1024);
                    }

                    $stream_info = stream_get_meta_data($remote);

                    if ($stream_info['timed_out'])
                    {
                        //echo 'checkHttpsAvailable: Data connection timed out ';
                        WizardLogger::getInstance()->log('checkModeRewrite: Data connection timed out: ' . $stream_source);
                    }
                }
                else
                {
                    //echo 'checkHttpsAvailable: Could not set timeout for socket ';
                    WizardLogger::getInstance()->log('checkModeRewrite: Could not set timeout for socket: ' . $stream_source);
                }

                fclose($remote);

                if (strpos($response, 'test_https_ok') != false)
                {
                    $httpsUrl = 'https://'.$_SERVER['HTTP_HOST'];
                    $httpsUrl .= _SRV_WEB .'xtWizard/index.php';
                    $result = ['httpsAvailable' => 1, 'httpsUrl' => $httpsUrl];
                }
            }
            else
            {
                //echo 'checkHttpsAvailable: Could not create socket '.$stream_source .$errno. '  '. $errstr;
                WizardLogger::getInstance()->log('checkModeRewrite: Could not create socket: ' . $stream_source);
            }
        }

        return $result;
    }

}

if(!function_exists("checkHTTPS"))
{
    function checkHTTPS()
    {
        $isSecure = false;
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') {
            $isSecure = true;
        }
        elseif($_SERVER['SERVER_PORT'] == '443' || $_SERVER['SERVER_PORT'] == '8443'){
            $isSecure = true;
        }
        elseif (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https' || !empty($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] == 'on') {
            $isSecure = true;
        }
        return $isSecure;
    }
}
